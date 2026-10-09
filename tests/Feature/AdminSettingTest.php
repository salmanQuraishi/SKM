<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminSettingTest extends TestCase
{
    use DatabaseTransactions;
    protected function getAdminUser(): User
    {
        $adminRole = Role::whereIn('slug', ['super_admin', 'admin'])->first();
        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'Super Admin',
                'slug' => 'super_admin',
            ]);
        }

        $admin = User::where('role_id', $adminRole->id)->where('is_active', true)->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]);
        }

        return $admin;
    }

    /**
     * Test 1: Admin can access settings page and see Company Identity & Contact tabs
     */
    public function test_admin_can_view_settings_page_with_company_identity_and_branding(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Company Profile & Store Settings', false);
        $response->assertSee('Company Identity & Logo', false);
        $response->assertSee('Contact & Address', false);
        $response->assertSee('Social & WhatsApp', false);
        $response->assertSee('site_logo');
        $response->assertSee('site_favicon');
        $response->assertSee('store_name');
        $response->assertSee('store_mobile');
    }

    /**
     * Test 2: Admin can update company details (name, mobile, email, address, GSTIN)
     */
    public function test_admin_can_update_company_text_details(): void
    {
        $admin = $this->getAdminUser();

        $payload = [
            'store_name' => 'AutoLogos India Private Limited',
            'store_tagline' => 'Premium Badges & Car Emblems Hub',
            'store_mobile' => '+91 99887 76655',
            'store_phone' => '+91 11 4455 6677',
            'store_email' => 'contact@autologos.in',
            'store_address' => 'Plot 45, Automobile Complex, Okhla Phase 3, New Delhi - 110020',
            'gstin' => '07ABCDE1234F1Z9',
            'header_announcement_text' => 'Special Festive Offer: 15% OFF on all Monograms!',
            'whatsapp_support_number' => '919988776655',
            'instagram_url' => 'https://instagram.com/autologos_official',
            'facebook_url' => 'https://facebook.com/autologos_official',
            'youtube_url' => 'https://youtube.com/@autologos_official',
        ];

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), $payload);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('AutoLogos India Private Limited', Setting::get('store_name'));
        $this->assertEquals('Premium Badges & Car Emblems Hub', Setting::get('store_tagline'));
        $this->assertEquals('+91 99887 76655', Setting::get('store_mobile'));
        $this->assertEquals('contact@autologos.in', Setting::get('store_email'));
        $this->assertEquals('07ABCDE1234F1Z9', Setting::get('gstin'));
        $this->assertEquals('919988776655', Setting::get('whatsapp_support_number'));
        $this->assertEquals('919988776655', Setting::get('whatsapp_phone'));
    }

    /**
     * Test 3: Admin can upload and remove company logo & favicon
     */
    public function test_admin_can_upload_and_remove_logo_and_favicon(): void
    {
        $admin = $this->getAdminUser();

        $logoFile = UploadedFile::fake()->image('test_brand_logo.png', 300, 100);
        $faviconFile = UploadedFile::fake()->create('test_favicon.ico', 50, 'image/x-icon');

        $uploadResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_name' => 'AutoLogos Emblem Co',
            'site_logo' => $logoFile,
            'site_favicon' => $faviconFile,
        ]);

        $uploadResponse->assertRedirect(route('admin.settings.index'));

        $logoPath = Setting::get('site_logo');
        $faviconPath = Setting::get('site_favicon');

        $this->assertNotEmpty($logoPath);
        $this->assertStringContainsString('images/branding/logo_', $logoPath);
        $this->assertTrue(File::exists(public_path($logoPath)));

        $this->assertNotEmpty($faviconPath);
        $this->assertStringContainsString('images/branding/favicon_', $faviconPath);
        $this->assertTrue(File::exists(public_path($faviconPath)));

        // Now test removing the custom logo and favicon
        $removeResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_name' => 'AutoLogos Emblem Co',
            'remove_site_logo' => '1',
            'remove_site_favicon' => '1',
        ]);

        $removeResponse->assertRedirect(route('admin.settings.index'));
        $this->assertEmpty(Setting::get('site_logo'));
        $this->assertEmpty(Setting::get('site_favicon'));
        $this->assertFalse(File::exists(public_path($logoPath)));
        $this->assertFalse(File::exists(public_path($faviconPath)));
    }

    /**
     * Test 4: Frontend views reflect updated company details
     */
    public function test_frontend_views_render_updated_company_details(): void
    {
        Setting::set('store_name', 'Auto Logos', 'general');
        Setting::set('store_mobile', '+91 91234 56789', 'general');
        Setting::set('store_phone', '+91 91234 56789', 'general');
        Setting::set('store_email', 'help@autologos.com', 'general');
        Setting::set('store_address', 'Unit 10, Auto Plaza, New Delhi', 'general');

        \Illuminate\Support\Facades\Cache::forget('all_settings');

        // Test homepage
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('help@autologos.com');
        $homeResponse->assertSee('+91 91234 56789');

        // Test contact page
        $contactResponse = $this->get(route('contact'));
        $contactResponse->assertStatus(200);
        $contactResponse->assertSee('Unit 10, Auto Plaza, New Delhi');
        $contactResponse->assertSee('help@autologos.com');
    }

    /**
     * Test: Admin can clear application cache via AJAX
     */
    public function test_admin_can_clear_cache_via_ajax(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->postJson(route('admin.cache.clear'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);
    }

    /**
     * Test: Guest cannot clear cache
     */
    public function test_guest_cannot_clear_cache(): void
    {
        $response = $this->postJson(route('admin.cache.clear'));

        $response->assertStatus(401);
    }

    /**
     * Test: Admin layout and settings page display the Clear Cache buttons
     */
    public function test_admin_pages_render_clear_cache_buttons(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Clear Cache', false);
        $response->assertSee('adminClearCacheBtn', false);
    }
}
