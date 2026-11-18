<?php

namespace Tests\Feature;

use App\Mail\EmailOtpMail;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class RoleUnificationAndSmtpSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected Role $customerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(
            ['slug' => 'super_admin'],
            ['name' => 'Super Admin', 'is_admin' => true, 'is_system' => true]
        );

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin_test_unification@example.com'],
            [
                'name' => 'Super Admin Tester',
                'password' => Hash::make('secret123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        $this->customerRole = Role::whereIn('slug', ['customer', 'user'])->first() ?? Role::create([
            'name' => 'Customer',
            'slug' => 'customer',
            'is_admin' => false,
            'is_system' => true,
        ]);
    }

    /**
     * Test 1: Customer role is unified, marked as Store User (is_admin = false), and no duplicate role exists.
     */
    public function test_customer_role_is_unified_and_not_admin(): void
    {
        $roles = Role::whereIn('slug', ['customer', 'user'])->get();
        $this->assertLessThanOrEqual(1, $roles->count(), 'Only one unified customer role should exist.');

        $customerRole = Role::where('slug', 'customer')->first();
        $this->assertNotNull($customerRole);
        $this->assertFalse((bool) $customerRole->is_admin, 'Customer role must never have admin access.');
        $this->assertTrue((bool) $customerRole->is_system, 'Customer role is a protected system role.');
        $this->assertTrue($customerRole->isCustomer());
        $this->assertTrue($customerRole->isUser());
    }

    /**
     * Test 2: Roles management page renders Customer badge as Store Customer (not Admin Access).
     */
    public function test_roles_page_renders_customer_as_store_customer(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.roles.index'));
        $response->assertStatus(200);
        $response->assertSee('Store Customer');
        $response->assertDontSee('Customer</span> <span class="badge bg-info-subtle text-info ms-1">Admin Access</span>', false);
    }

    /**
     * Test 3: Staff page /admin/staff loads cleanly and only lists staff/admin roles by default.
     */
    public function test_staff_management_page_excludes_customers_by_default(): void
    {
        // Create dummy customer
        $customerUser = User::create([
            'name' => 'John Customer Demo',
            'email' => 'customer_demo_' . Str::random(6) . '@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $this->customerRole->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.staff.index'));
        $response->assertStatus(200);
        $response->assertSee('Users & Staff Management', false);
        // Default staff listing excludes regular store customers
        $response->assertDontSee($customerUser->email);
    }

    /**
     * Test 4: Filtering staff page with customer role displays informative notice and clean table layout.
     */
    public function test_staff_management_with_customer_role_filter_renders_banner(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.staff.index', ['role' => $this->customerRole->id]));
        $response->assertStatus(200);
        $response->assertSee('Viewing Store Customers Filter');
        $response->assertSee('Open Customer Management');
    }

    /**
     * Test 5: Admin settings page renders Email & SMTP setup tab and fields.
     */
    public function test_admin_settings_renders_smtp_setup_tab_and_fields(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Email & SMTP Settings', false);
        $response->assertSee('SMTP Mail Server Configuration');
        $response->assertSee('mail_host');
        $response->assertSee('mail_port');
        $response->assertSee('mail_username');
        $response->assertSee('mail_password');
        $response->assertSee('Live SMTP Connection Tester');
    }

    /**
     * Test 6: Admin can save SMTP settings successfully.
     */
    public function test_admin_can_save_smtp_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('admin.settings.update'), [
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => 2525,
            'mail_username' => 'testuser123',
            'mail_password' => 'testpass123',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'orders@skmsaller.com',
            'mail_from_name' => 'SKM Saller',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEquals('smtp.mailtrap.io', Setting::get('mail_host'));
        $this->assertEquals('2525', Setting::get('mail_port'));
        $this->assertEquals('testuser123', Setting::get('mail_username'));
        $this->assertEquals('orders@skmsaller.com', Setting::get('mail_from_address'));
    }

    /**
     * Test 7: Send test email endpoint responds with JSON.
     */
    public function test_send_test_email_endpoint_with_log_driver(): void
    {
        $response = $this->actingAs($this->superAdmin)->postJson(route('admin.settings.test-email'), [
            'test_email' => 'recipient@example.com',
            'mail_mailer' => 'log',
            'mail_host' => '127.0.0.1',
            'mail_port' => 2525,
            'mail_encryption' => 'null',
            'mail_from_address' => 'orders@skmsaller.com',
            'mail_from_name' => 'SKM Saller',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'message' => 'Success! Test email was successfully sent to recipient@example.com. Please check your inbox and spam folder.'
        ]);
    }

    /**
     * Test 8: EmailOtpMail envelope subject dynamically incorporates store name.
     */
    public function test_email_otp_mail_uses_dynamic_store_name(): void
    {
        Setting::set('store_name', 'SKM Saller', 'general');

        $otpMailLogin = new EmailOtpMail('123456', 'login', 'Rahul Kumar');
        $this->assertStringContainsString('SKM Saller Login OTP: 123456', $otpMailLogin->envelope()->subject);

        $otpMailRegister = new EmailOtpMail('654321', 'register', 'Rahul Kumar');
        $this->assertStringContainsString('SKM Saller OTP: 654321', $otpMailRegister->envelope()->subject);
    }
}
