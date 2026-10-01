<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelVariant;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class VehicleHierarchyTest extends TestCase
{
    /**
     * Test Case 1: Company List View
     * Open /vehicles. Tata, Hyundai, Mahindra, Maruti Suzuki etc. should show.
     */
    public function test_vehicles_company_index_page_loads_and_displays_companies(): void
    {
        $response = $this->get(route('vehicles'));

        $response->assertStatus(200);
        $response->assertSee('Select Vehicle Brand');
        $response->assertSee('Tata');
        $response->assertSee('Hyundai');
        $response->assertSee('Mahindra');
    }

    /**
     * Test Case 2: Model List View
     * Click Tata -> Tata car models show: Punch, Nexon, Tiago, Safari etc.
     */
    public function test_company_car_models_page_displays_models_of_that_company(): void
    {
        $response = $this->get(route('vehicle.cars', 'tata'));

        $response->assertStatus(200);
        $response->assertSee('Tata');
        $response->assertSee('Punch');
        $response->assertSee('Nexon');
        $response->assertSee('Tiago');
    }

    /**
     * Test Case 3: Variant List View
     * Click Punch -> Tata Punch variants show: Pure, Adventure, Accomplished, Creative.
     */
    public function test_car_model_variants_page_displays_variants_of_that_car(): void
    {
        $response = $this->get(route('vehicle.variants', ['companySlug' => 'tata', 'carSlug' => 'punch']));

        $response->assertStatus(200);
        $response->assertSee('Tata Punch');
        $response->assertSee('Pure');
        $response->assertSee('Adventure');
        $response->assertSee('Accomplished');
        $response->assertSee('Creative');
    }

    /**
     * Test Case 4: Variant Products View
     * Click Pure -> Tata Punch Pure mapped products show.
     * Important: Punch Adventure or Nexon products must NOT show in this variant view.
     */
    public function test_variant_products_page_displays_only_mapped_products(): void
    {
        $response = $this->get(route('vehicle.products', [
            'companySlug' => 'tata',
            'carSlug' => 'punch',
            'variantSlug' => 'punch-pure',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tata Punch Pure Front Grille Chrome Emblem');
        $response->assertSee('Front Ceramic Brake Pad Set - Tata Punch Pure');

        // Isolation check: Other variants' products MUST NOT appear
        $response->assertDontSee('All-Weather 7D Floor Mats - Tata Punch Adventure');
        $response->assertDontSee('Aero Dynamic Roof Rail Trim - Tata Nexon Smart');
    }

    /**
     * Test Case 5: Empty State View
     * When a variant has no products mapped, a helpful empty state should appear.
     */
    public function test_variant_products_page_shows_empty_state_if_no_products_mapped(): void
    {
        $response = $this->get(route('vehicle.products', [
            'companySlug' => 'tata',
            'carSlug' => 'tiago',
            'variantSlug' => 'tiago-xe',
        ]));

        $response->assertStatus(200);
        $response->assertSee('No Products Available for This Variant');
    }

    /**
     * Test Case 6: AJAX Dependent Dropdowns
     * Models by brand and Variants by model return valid JSON.
     */
    public function test_ajax_dependent_dropdowns_return_correct_json(): void
    {
        $tata = Brand::where('slug', 'tata')->firstOrFail();
        
        // Test by numeric ID
        $modelsResponse = $this->getJson(route('ajax.models_by_brand', $tata->id));
        $modelsResponse->assertStatus(200);
        $modelsResponse->assertJsonFragment(['slug' => 'punch']);

        // Test by slug
        $modelsResponseSlug = $this->getJson(route('ajax.models_by_brand', $tata->slug));
        $modelsResponseSlug->assertStatus(200);
        $modelsResponseSlug->assertJsonFragment(['slug' => 'punch']);

        $punch = CarModel::where('brand_id', $tata->id)->where('slug', 'punch')->firstOrFail();
        
        // Test variants by numeric ID
        $variantsResponse = $this->getJson(route('ajax.variants_by_model', $punch->id));
        $variantsResponse->assertStatus(200);
        $variantsResponse->assertJsonFragment(['slug' => 'punch-pure']);

        // Test variants by slug
        $variantsResponseSlug = $this->getJson(route('ajax.variants_by_model', $punch->slug));
        $variantsResponseSlug->assertStatus(200);
        $variantsResponseSlug->assertJsonFragment(['slug' => 'punch-pure']);
    }

    /**
     * Test Case 7: Dynamic Addition
     * New Car Model and new Variant created dynamically are accessible via vehicle flow.
     */
    public function test_dynamic_vehicle_addition_and_navigation(): void
    {
        $mahindra = Brand::where('slug', 'mahindra')->firstOrFail();

        // Create test model
        $testModel = CarModel::firstOrCreate(
            ['brand_id' => $mahindra->id, 'slug' => 'test-dynamic-suv'],
            ['name' => 'Dynamic SUV', 'is_active' => true, 'sort_order' => 99]
        );

        // Create test variant
        $testVariant = ModelVariant::firstOrCreate(
            ['car_model_id' => $testModel->id, 'slug' => 'dynamic-trim-z'],
            ['name' => 'Trim Z', 'status' => true, 'sort_order' => 1]
        );

        $response = $this->get(route('vehicle.products', [
            'companySlug' => 'mahindra',
            'carSlug' => 'test-dynamic-suv',
            'variantSlug' => 'dynamic-trim-z',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Mahindra');
        $response->assertSee('Dynamic SUV');
        $response->assertSee('Trim Z');
    }

    /**
     * Test Case 8: Safe Deletion Protection
     * A variant with mapped products cannot be deleted.
     */
    public function test_admin_cannot_delete_variant_with_mapped_products(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->whereIn('slug', ['super_admin', 'admin']))->first();
        if (!$admin) {
            $superAdminRole = Role::firstOrCreate(['slug' => 'super_admin'], ['name' => 'Super Admin']);
            $admin = User::firstOrCreate(
                ['email' => 'admin@autologos.in'],
                ['name' => 'Admin User', 'password' => bcrypt('password'), 'role_id' => $superAdminRole->id, 'is_active' => true]
            );
        }

        $pureVariant = ModelVariant::where('slug', 'punch-pure')->firstOrFail();
        $this->assertTrue($pureVariant->products()->count() > 0);

        $response = $this->actingAs($admin)->delete(route('admin.variants.destroy', $pureVariant->id));

        // Must redirect back with error and not delete
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('model_variants', ['id' => $pureVariant->id, 'deleted_at' => null]);
    }

    /**
     * Test Case 9: Product Detail Page Fitment & Breadcrumb
     * Product detail shows complete vehicle hierarchy badge.
     */
    public function test_product_detail_page_shows_vehicle_fitment_and_breadcrumb(): void
    {
        $product = Product::where('slug', 'tata-punch-pure-front-grille-chrome-emblem')->firstOrFail();

        $response = $this->get(route('product.detail', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Tailored Vehicle Fitment');
        $response->assertSee('Tata');
        $response->assertSee('Punch');
        $response->assertSee('Pure');
    }

    /**
     * Test Case 10: Existing Cart & Ecommerce Preservation
     * Adding an existing product to cart continues working without issue.
     */
    public function test_existing_cart_and_order_flow_remains_functional(): void
    {
        $user = User::first() ?? User::factory()->create();
        $product = Product::active()->firstOrFail();

        $response = $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Cart page loads successfully and contains product
        $cartPage = $this->actingAs($user)->get(route('cart.index'));
        $cartPage->assertStatus(200);
        $cartPage->assertSee($product->name);
    }

    /**
     * Test Case 11: Cart count badge persists on page reload
     */
    public function test_cart_count_badge_persists_on_page_reload(): void
    {
        $user = User::factory()->create();
        $product = Product::active()->firstOrFail();

        // Add product to cart (quantity 2)
        $addRes = $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $addRes->assertStatus(200);
        $addRes->assertJson(['success' => true, 'cart_count' => 2]);

        // Page reload (homepage)
        $homeRes = $this->actingAs($user)->get(route('home'));
        $homeRes->assertStatus(200);
        $this->assertMatchesRegularExpression('/id="globalCartCounter"[^>]*>\s*2\s*<\/span>/', $homeRes->getContent());

        // Cart page reload
        $cartRes = $this->actingAs($user)->get(route('cart.index'));
        $cartRes->assertStatus(200);
        $this->assertMatchesRegularExpression('/id="globalCartCounter"[^>]*>\s*2\s*<\/span>/', $cartRes->getContent());
    }

    /**
     * Test Case 12: Wishlist count badge persists on page reload and displays on wishlist page
     */
    public function test_wishlist_count_badge_persists_on_page_reload(): void
    {
        $user = User::factory()->create();
        $product = Product::active()->firstOrFail();

        // Add product to wishlist
        $toggleRes = $this->actingAs($user)->postJson(route('wishlist.toggle', $product->id));
        $toggleRes->assertStatus(200);
        $toggleRes->assertJson(['success' => true, 'added' => true, 'wishlist_count' => 1]);

        // Page reload (homepage)
        $homeRes = $this->actingAs($user)->get(route('home'));
        $homeRes->assertStatus(200);
        $this->assertMatchesRegularExpression('/id="globalWishlistCounter"[^>]*>\s*1\s*<\/span>/', $homeRes->getContent());

        // Wishlist page reload
        $wishlistRes = $this->actingAs($user)->get(route('wishlist.index'));
        $wishlistRes->assertStatus(200);
        $wishlistRes->assertSee($product->name);
        $this->assertMatchesRegularExpression('/id="globalWishlistCounter"[^>]*>\s*1\s*<\/span>/', $wishlistRes->getContent());
    }

    /**
     * Test Case 11: Mega Menu Company -> Car -> Variant Hierarchy
     * Homepage sidebar and navigation menu render dynamic companies, cars, and variant links.
     */
    public function test_homepage_and_nav_mega_menu_shows_companies_cars_and_variants(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);

        // Contains companies in the mega menu
        $response->assertSee('Tata');
        $response->assertSee('Hyundai');
        $response->assertSee('Mahindra');

        // Contains car models
        $response->assertSee('Punch');
        $response->assertSee('Nexon');
        $response->assertSee('Thar');

        // Contains variant links pointing to filtered products route
        $response->assertSee(route('vehicle.products', ['companySlug' => 'tata', 'carSlug' => 'punch', 'variantSlug' => 'punch-pure']));
        $response->assertSee(route('vehicle.products', ['companySlug' => 'mahindra', 'carSlug' => 'thar', 'variantSlug' => 'thar-ax']));
    }

    /**
     * Test Case 14: Shop Page Professional Filters & Active Chips
     */
    public function test_shop_page_loads_with_professional_filters_and_chips(): void
    {
        // 1. Plain shop page
        $res = $this->get(route('shop'));
        $res->assertStatus(200);
        $res->assertSee('FILTER BY');
        $res->assertSee('SELECT VEHICLE');
        $res->assertSee('Categories');
        $res->assertSee('Price Range');
        $res->assertSee('Finish / Color');
        $res->assertSee('Placement');

        // 2. Filtered by brand and finish
        $filteredRes = $this->get(route('shop', ['brand' => 'tata', 'finish' => 'Chrome']));
        $filteredRes->assertStatus(200);
        $filteredRes->assertSee('Active Filters:');
        $filteredRes->assertSee('Brand:');
        $filteredRes->assertSee('Tata');
        $filteredRes->assertSee('Finish:');
        $filteredRes->assertSee('Chrome');
        $filteredRes->assertSee('Clear All');
    }

    /**
     * Test Case 15: Vehicle Variant Products Page Filters
     */
    public function test_vehicle_variant_products_page_loads_with_filters(): void
    {
        // 1. Visit Safari Accomplished
        $res = $this->get(route('vehicle.products', [
            'companySlug' => 'tata',
            'carSlug' => 'safari',
            'variantSlug' => 'safari-accomplished',
        ]));
        $res->assertStatus(200);
        $res->assertSee('FILTERS');
        $res->assertSee('Accomplished');
        $res->assertSee('Exact Fitment Guarantee');
        $res->assertSee('Categories');
        $res->assertSee('Price Range');
        $res->assertSee('Finish / Color');
        $res->assertSee('Placement');

        // 2. Filter within variant by placement and finish
        $filteredRes = $this->get(route('vehicle.products', [
            'companySlug' => 'tata',
            'carSlug' => 'safari',
            'variantSlug' => 'safari-accomplished',
            'placement' => 'Front Grille',
            'finish' => 'Chrome',
        ]));
        $filteredRes->assertStatus(200);
        $filteredRes->assertSee('Reset All');
    }

    /**
     * Test Case 16: Cart Coupon Apply, Remove & Graceful GET Fallbacks
     */
    public function test_cart_coupon_apply_and_graceful_recovery(): void
    {
        $user = User::factory()->create();
        $product = Product::active()->firstOrFail();
        $unitPrice = (float) ($product->sale_price ?: $product->regular_price);
        $qty = $unitPrice > 0 ? (int) ceil(600 / $unitPrice) : 3;

        // 1. GET /cart/coupon/apply should redirect to /cart
        $getRes = $this->actingAs($user)->get(route('cart.coupon.apply'));
        $getRes->assertRedirect(route('cart.index'));

        // 2. Add product to cart with enough quantity to satisfy minimum spend
        $addRes = $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => max(2, $qty),
        ]);
        $addRes->assertStatus(200);

        // 3. Apply valid coupon AUTO10
        $applyRes = $this->actingAs($user)->postJson(route('cart.coupon.apply'), [
            'code' => 'AUTO10',
        ]);
        $applyRes->assertStatus(200);
        $applyRes->assertJson(['success' => true]);

        // 4. Remove coupon
        $removeRes = $this->actingAs($user)->postJson(route('cart.coupon.remove'));
        $removeRes->assertStatus(200);
        $removeRes->assertJson(['success' => true]);
    }
}
