<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactInquiry;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewFeaturesAndReportsTest extends TestCase
{
    protected function getAdminUser(): User
    {
        $superRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Administrator', 'description' => 'Super Admin', 'is_system' => true]
        );

        return User::firstOrCreate(
            ['email' => 'admin@autologos.in'],
            [
                'name' => 'Store Administrator',
                'password' => bcrypt('password123'),
                'role_id' => $superRole->id,
            ]
        );
    }

    /**
     * Test 1: Product create and update accepts HSN / SAC Code
     */
    public function test_product_add_and_update_supports_hsn_code(): void
    {
        $admin = $this->getAdminUser();

        $category = Category::first() ?: Category::create(['name' => 'Test Cat', 'slug' => 'test-cat-' . uniqid(), 'status' => true]);
        $brand = Brand::first() ?: Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand-' . uniqid(), 'status' => true]);

        $sku = 'HSN-TEST-' . strtoupper(uniqid());

        // Create product with HSN code
        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'HSN Emblem Test Part',
            'sku' => $sku,
            'regular_price' => 999.00,
            'sale_price' => 799.00,
            'cost_price' => 450.00,
            'stock_quantity' => 25,
            'min_stock_alert' => 5,
            'tax_rate' => 18.00,
            'hsn_code' => '87082900',
            'product_type' => 'logo',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', $sku)->first();
        $this->assertNotNull($product);
        $this->assertEquals('87082900', $product->hsn_sac);
        $this->assertEquals('87082900', $product->hsn_code);

        // Update product with new HSN code
        $updateResponse = $this->actingAs($admin)->put(route('admin.products.update', $product->id), [
            'name' => 'HSN Emblem Test Part Updated',
            'sku' => $sku,
            'regular_price' => 1099.00,
            'sale_price' => 899.00,
            'cost_price' => 500.00,
            'stock_quantity' => 30,
            'min_stock_alert' => 5,
            'tax_rate' => 18.00,
            'hsn_code' => '87089900',
            'product_type' => 'logo',
            'brand_id' => $brand->id,
            'primary_category_id' => $category->id,
        ]);

        $updateResponse->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertEquals('87089900', $product->hsn_sac);
        $this->assertEquals('87089900', $product->hsn_code);

        // Check product edit page renders HSN code
        $editPage = $this->actingAs($admin)->get(route('admin.products.edit', $product->id));
        $editPage->assertStatus(200);
        $editPage->assertSee('87089900');
        $editPage->assertSee('HSN / SAC Code');

        // Check products index page shows HSN code
        $indexPage = $this->actingAs($admin)->get(route('admin.products.index'));
        $indexPage->assertStatus(200);
        $indexPage->assertSee('87089900');

        // Clean up test product
        $product->forceDelete();
    }

    /**
     * Test 2: All brand icons exist and are pure white SVGs
     */
    public function test_all_brand_icons_are_white_and_exist(): void
    {
        $brands = Brand::all();
        $this->assertNotEmpty($brands);

        foreach ($brands as $brand) {
            $svgPath = public_path('images/brands/' . $brand->slug . '.svg');
            $this->assertFileExists($svgPath, "Missing SVG for brand {$brand->name} ({$brand->slug})");

            $content = file_get_contents($svgPath);
            $this->assertStringContainsString('<svg', $content);
            $this->assertStringContainsString('#ffffff', strtolower($content), "Brand {$brand->name} ({$brand->slug}) does not contain white color #ffffff");

            // Verify model accessor returns valid asset url
            $this->assertStringContainsString('images/brands/' . $brand->slug . '.svg', $brand->logo_url);
        }

        // Also check Jeep and Mitsubishi explicitly
        $this->assertFileExists(public_path('images/brands/jeep.svg'));
        $this->assertFileExists(public_path('images/brands/mitsubishi.svg'));
        $this->assertFileExists(public_path('images/brands/mitshubhishi.svg'));
    }

    /**
     * Test 3: All 4 Reports load and render correctly
     */
    public function test_all_four_reports_load_and_export(): void
    {
        $admin = $this->getAdminUser();

        // 1. Business Report
        $businessRes = $this->actingAs($admin)->get(route('admin.reports.business'));
        $businessRes->assertStatus(200);
        $businessRes->assertSee('Business Performance Report', false);
        $businessRes->assertSee('Paid Net Revenue');

        $businessExport = $this->actingAs($admin)->get(route('admin.reports.business.export'));
        $businessExport->assertStatus(200);
        $this->assertStringContainsString('text/csv', $businessExport->headers->get('Content-Type'));

        // 2. Inventory Report
        $inventoryRes = $this->actingAs($admin)->get(route('admin.reports.inventory'));
        $inventoryRes->assertStatus(200);
        $inventoryRes->assertSee('Inventory & Stock Report', false);
        $inventoryRes->assertSee('Total Stock Units');

        $inventoryExport = $this->actingAs($admin)->get(route('admin.reports.inventory.export'));
        $inventoryExport->assertStatus(200);
        $this->assertStringContainsString('text/csv', $inventoryExport->headers->get('Content-Type'));

        // 3. Tax Report
        $taxRes = $this->actingAs($admin)->get(route('admin.reports.tax'));
        $taxRes->assertStatus(200);
        $taxRes->assertSee('Tax & GST Audit Report', false);
        $taxRes->assertSee('HSN / SAC Code Summary Breakdown', false);

        $taxExport = $this->actingAs($admin)->get(route('admin.reports.tax.export'));
        $taxExport->assertStatus(200);
        $this->assertStringContainsString('text/csv', $taxExport->headers->get('Content-Type'));

        // 4. Payment Report
        $paymentRes = $this->actingAs($admin)->get(route('admin.reports.payment'));
        $paymentRes->assertStatus(200);
        $paymentRes->assertSee('Payment & Settlements Report', false);
        $paymentRes->assertSee('Razorpay Online Gateway');

        $paymentExport = $this->actingAs($admin)->get(route('admin.reports.payment.export'));
        $paymentExport->assertStatus(200);
        $this->assertStringContainsString('text/csv', $paymentExport->headers->get('Content-Type'));
    }

    /**
     * Test 4: Dashboard displays Customer Support Inquiries in upper section
     */
    public function test_dashboard_displays_customer_support_inquiries_widget(): void
    {
        $admin = $this->getAdminUser();

        // Ensure at least one test inquiry exists
        $inquiry = ContactInquiry::create([
            'name' => 'Vikram Sharma',
            'email' => 'vikram@example.com',
            'phone' => '9812345678',
            'subject' => 'Need Jeep Compass Monogram Logo',
            'message' => 'Please let me know if front chrome logo for Jeep Compass is in stock.',
            'is_read' => false,
        ]);

        $dashboardRes = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee('Customer Support Inquiries');
        $dashboardRes->assertSee('Vikram Sharma');
        $dashboardRes->assertSee('Need Jeep Compass Monogram Logo');
        $dashboardRes->assertSee('Unread');

        // Clean up
        $inquiry->delete();
    }
}
