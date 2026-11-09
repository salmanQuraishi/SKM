<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DynamicNavFiltersTest extends TestCase
{
    use DatabaseTransactions;

    private function getAdminUser(): User
    {
        $admin = User::whereHas('role', fn($q) => $q->whereIn('slug', ['super_admin', 'admin']))->first();
        if (!$admin) {
            $superAdminRole = Role::firstOrCreate(['slug' => 'super_admin'], ['name' => 'Super Admin']);
            $admin = User::firstOrCreate(
                ['email' => 'admin_nav_test@example.com'],
                ['name' => 'Admin User', 'password' => bcrypt('password'), 'role_id' => $superAdminRole->id, 'is_active' => true]
            );
        }
        return $admin;
    }

    public function test_all_products_page_loads_successfully()
    {
        $response = $this->get(route('shop'));
        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertSee('ALL PRODUCTS');
    }

    public function test_header_navigation_renders_dynamic_categories_from_database()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Verify the 6 dynamic header categories are rendered from DB
        $response->assertSee('FRONT LOGO');
        $response->assertSee('REAR LOGO');
        $response->assertSee('BONNET &amp; LETTERS', false);
        $response->assertSee('FULL MONOGRAM KITS');
        $response->assertSee('SINGLE BADGES');
        $response->assertSee('NEW ARRIVALS');
    }

    public function test_front_logo_filter_filters_products_and_displays_active_chip()
    {
        $response = $this->get(route('shop', ['category' => 'front-logo']));
        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertSee('Front Logo');
        
        $products = $response->viewData('products');
        $this->assertNotEmpty($products);
        $this->assertLessThan(Product::active()->count(), $products->total());

        // Verify all returned products in first page match front placement, category, or name
        foreach ($products->items() as $product) {
            $matches = str_contains(strtolower($product->placement ?? ''), 'front')
                || str_contains(strtolower($product->name), 'front')
                || $product->categories->contains('slug', 'front-logo')
                || $product->primaryCategory?->slug === 'front-logo';
            $this->assertTrue($matches, "Product {$product->name} does not match front logo filter.");
        }
    }

    public function test_rear_logo_filter_filters_products_and_displays_active_chip()
    {
        $response = $this->get(route('shop', ['category' => 'rear-logo']));
        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertSee('Rear Logo');
        
        $products = $response->viewData('products');
        $this->assertNotEmpty($products);
        $this->assertLessThan(Product::active()->count(), $products->total());

        foreach ($products->items() as $product) {
            $matches = str_contains(strtolower($product->placement ?? ''), 'rear')
                || str_contains(strtolower($product->name), 'rear')
                || $product->categories->contains('slug', 'rear-logo')
                || $product->primaryCategory?->slug === 'rear-logo';
            $this->assertTrue($matches, "Product {$product->name} does not match rear logo filter.");
        }
    }

    public function test_bonnet_tailgate_letters_filter_filters_products()
    {
        $response = $this->get(route('shop', ['category' => 'bonnet-tailgate-letters']));
        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertSee('Bonnet &amp; Letters', false);
        
        $products = $response->viewData('products');
        $this->assertNotEmpty($products);
        $this->assertLessThan(Product::active()->count(), $products->total());
    }

    public function test_kit_full_monogram_filter_filters_products()
    {
        $response = $this->get(route('shop', ['category' => 'kit-full-monogram']));
        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertSee('Full Monogram Kits');
        
        $products = $response->viewData('products');
        $this->assertNotEmpty($products);
        $this->assertLessThan(Product::active()->count(), $products->total());
    }

    public function test_single_name_badge_filter_filters_products()
    {
        $response = $this->get(route('shop', ['category' => 'single-name-badge']));
        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertSee('Single Badges');
        
        $products = $response->viewData('products');
        $this->assertNotEmpty($products);
        $this->assertLessThan(Product::active()->count(), $products->total());
    }

    public function test_new_arrivals_filter_filters_products()
    {
        $response = $this->get(route('shop', ['category' => 'new-arrivals']));
        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertSee('New Arrivals');
        
        $products = $response->viewData('products');
        $this->assertNotEmpty($products);
        $this->assertEquals(Product::active()->where('is_new_arrival', 1)->count(), $products->total());
    }

    public function test_placement_front_filter_fuzzy_matches_front_grille()
    {
        $response = $this->get(route('shop', ['placement' => 'Front']));
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertGreaterThan(100, $products->total());
    }

    public function test_front_logo_combined_with_brand_filter()
    {
        $response = $this->get(route('shop', [
            'category' => 'front-logo',
            'brand' => 'maruti-suzuki',
        ]));
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertGreaterThan(0, $products->total());
        $this->assertLessThan(160, $products->total());
    }

    public function test_admin_product_create_and_edit_shows_category_selection_cards()
    {
        $admin = $this->getAdminUser();

        // 1. Create page
        $createRes = $this->actingAs($admin)->get(route('admin.products.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('Primary Category');
        $createRes->assertSee('Front Logo');
        $createRes->assertSee('Rear Logo');
        $createRes->assertSee('Full Monogram Kits');
        $createRes->assertSee('Single Badges');
        $createRes->assertSee('New Arrivals');

        // 2. Edit page
        $sampleProduct = Product::first();
        if ($sampleProduct) {
            $editRes = $this->actingAs($admin)->get(route('admin.products.edit', $sampleProduct->id));
            $editRes->assertStatus(200);
            $editRes->assertSee('Primary Category');
            $editRes->assertSee('Front Logo');
            $editRes->assertSee('Rear Logo');
        }
    }

    public function test_admin_can_add_product_with_selected_categories()
    {
        $admin = $this->getAdminUser();
        $frontCat = Category::where('slug', 'front-logo')->firstOrFail();
        $newArrivalCat = Category::where('slug', 'new-arrivals')->firstOrFail();

        $sku = 'TEST-NAV-' . rand(1000, 9999);
        $postData = [
            'name' => 'Test Premium Front Emblem ' . rand(100, 999),
            'sku' => $sku,
            'regular_price' => 999,
            'sale_price' => 799,
            'stock_quantity' => 25,
            'product_type' => 'logo',
            'placement' => 'Front Grille',
            'finish' => 'Gloss Chrome',
            'primary_category_id' => $frontCat->id,
            'category_ids' => [$frontCat->id, $newArrivalCat->id],
            'status' => '1',
            'is_new_arrival' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.products.store'), $postData);
        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', $sku)->firstOrFail();
        $this->assertEquals($frontCat->id, $product->primary_category_id);
        $this->assertTrue($product->is_new_arrival);
        $this->assertTrue($product->categories->contains('id', $frontCat->id));
        $this->assertTrue($product->categories->contains('id', $newArrivalCat->id));
    }

    public function test_admin_can_update_product_categories()
    {
        $admin = $this->getAdminUser();
        $rearCat = Category::where('slug', 'rear-logo')->firstOrFail();
        $bonnetCat = Category::where('slug', 'bonnet-tailgate-letters')->firstOrFail();

        $product = Product::firstOrFail();

        $updateData = [
            'name' => $product->name,
            'sku' => $product->sku,
            'regular_price' => $product->regular_price ?: 500,
            'sale_price' => $product->sale_price,
            'stock_quantity' => $product->stock_quantity ?: 10,
            'product_type' => $product->product_type ?: 'logo',
            'placement' => 'Rear Trunk',
            'primary_category_id' => $rearCat->id,
            'category_ids' => [$rearCat->id, $bonnetCat->id],
            'status' => '1',
        ];

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product->id), $updateData);
        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals($rearCat->id, $product->primary_category_id);
        $this->assertTrue($product->categories->contains('id', $rearCat->id));
        $this->assertTrue($product->categories->contains('id', $bonnetCat->id));
    }

    public function test_product_type_dropdown_includes_featured_categories_options()
    {
        $admin = $this->getAdminUser();

        // 1. Create page checks
        $createRes = $this->actingAs($admin)->get(route('admin.products.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('value="front_logo"', false);
        $createRes->assertSee('value="rear_logo"', false);
        $createRes->assertSee('value="bonnet_letters"', false);
        $createRes->assertSee('value="full_monogram_kit"', false);
        $createRes->assertSee('value="single_badge"', false);
        $createRes->assertSee('value="new_arrival"', false);

        // 2. Edit page checks
        $product = Product::first();
        if ($product) {
            $editRes = $this->actingAs($admin)->get(route('admin.products.edit', $product->id));
            $editRes->assertStatus(200);
            $editRes->assertSee('value="front_logo"', false);
            $editRes->assertSee('value="rear_logo"', false);
            $editRes->assertSee('value="bonnet_letters"', false);
            $editRes->assertSee('value="full_monogram_kit"', false);
            $editRes->assertSee('value="single_badge"', false);
            $editRes->assertSee('value="new_arrival"', false);
        }
    }

    public function test_admin_can_add_product_by_product_type_auto_mapping()
    {
        $admin = $this->getAdminUser();
        $frontCat = Category::where('slug', 'front-logo')->firstOrFail();

        $sku = 'TYPE-FRONT-' . rand(1000, 9999);
        $postData = [
            'name' => 'Auto Mapped Front Logo ' . rand(100, 999),
            'sku' => $sku,
            'regular_price' => 699,
            'stock_quantity' => 15,
            'product_type' => 'front_logo',
            'status' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.products.store'), $postData);
        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', $sku)->firstOrFail();
        $this->assertEquals('front_logo', $product->product_type);
        $this->assertEquals($frontCat->id, $product->primary_category_id);
        $this->assertEquals('Front Grille', $product->placement);
        $this->assertTrue($product->categories->contains('id', $frontCat->id));
    }

    public function test_admin_can_add_product_with_new_arrival_product_type()
    {
        $admin = $this->getAdminUser();
        $newArrivalCat = Category::where('slug', 'new-arrivals')->firstOrFail();

        $sku = 'TYPE-NEW-' . rand(1000, 9999);
        $postData = [
            'name' => 'Auto Mapped New Arrival Emblem ' . rand(100, 999),
            'sku' => $sku,
            'regular_price' => 1299,
            'stock_quantity' => 5,
            'product_type' => 'new_arrival',
            'status' => '1',
        ];

        $response = $this->actingAs($admin)->post(route('admin.products.store'), $postData);
        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', $sku)->firstOrFail();
        $this->assertEquals('new_arrival', $product->product_type);
        $this->assertTrue($product->is_new_arrival);
        $this->assertEquals($newArrivalCat->id, $product->primary_category_id);
        $this->assertTrue($product->categories->contains('id', $newArrivalCat->id));
    }
}
