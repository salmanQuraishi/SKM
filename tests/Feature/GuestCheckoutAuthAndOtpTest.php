<?php

namespace Tests\Feature;

use App\Models\EmailOtp;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GuestCheckoutAuthAndOtpTest extends TestCase
{
    use DatabaseTransactions;

    private function getCustomerUser(): User
    {
        $role = Role::firstOrCreate(['slug' => 'customer'], ['name' => 'Customer']);
        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_guest_cannot_access_checkout_page_and_is_redirected_to_login(): void
    {
        // Guest attempts to visit checkout
        $response = $this->get(route('checkout'));

        // Must redirect to login
        $response->assertRedirect(route('login'));
        $this->assertEquals(route('checkout'), session('url.intended'));
    }

    public function test_guest_cannot_place_order_and_is_rejected(): void
    {
        $postData = [
            'name' => 'Guest User',
            'email' => 'guest_test@example.com',
            'mobile' => '9876543210',
            'address_line_1' => 'Street 1',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'payment_method' => 'cod',
        ];

        // 1. Web request
        $response = $this->post(route('checkout.process'), $postData);
        $response->assertRedirect(route('login'));

        // 2. AJAX request
        $ajaxResponse = $this->postJson(route('checkout.process'), $postData);
        $ajaxResponse->assertStatus(401);
        $ajaxResponse->assertJson([
            'success' => false,
            'require_login' => true,
        ]);
    }

    public function test_register_send_otp_validates_inputs_and_creates_email_otp(): void
    {
        $email = 'new_customer_' . rand(1000, 9999) . '@gmail.com';
        $phone = '98' . rand(10000000, 99999999);

        $response = $this->postJson(route('register.send-otp'), [
            'name' => 'Rahul Verma',
            'email' => $email,
            'phone' => $phone,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'email' => $email,
        ]);

        $this->assertDatabaseHas('email_otps', [
            'email' => $email,
            'action' => 'register',
        ]);

        $otpRecord = EmailOtp::where('email', $email)->where('action', 'register')->first();
        $this->assertNotNull($otpRecord);
        $this->assertEquals(6, strlen($otpRecord->otp));
        $this->assertFalse($otpRecord->isExpired());
    }

    public function test_register_rejects_duplicate_email(): void
    {
        $existing = $this->getCustomerUser();
        $phone = '97' . rand(10000000, 99999999);

        $response = $this->postJson(route('register.send-otp'), [
            'name' => 'Duplicate Email User',
            'email' => $existing->email,
            'phone' => $phone,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_register_rejects_duplicate_mobile_number(): void
    {
        $existing = $this->getCustomerUser();
        $email = 'unique_email_' . rand(1000, 9999) . '@gmail.com';

        $response = $this->postJson(route('register.send-otp'), [
            'name' => 'Duplicate Phone User',
            'email' => $email,
            'phone' => $existing->phone,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone']);
    }

    public function test_register_verify_otp_rejects_invalid_otp(): void
    {
        $email = 'verify_fail_' . rand(1000, 9999) . '@gmail.com';

        // Generate OTP
        EmailOtp::createOtp($email, 'register', ['name' => 'Fail User', 'password' => Hash::make('password')]);

        // Submit wrong OTP
        $response = $this->postJson(route('register.verify-otp'), [
            'email' => $email,
            'otp' => '000000',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $this->assertFalse(Auth::check());
    }

    public function test_register_verify_otp_creates_user_marks_verified_and_authenticates(): void
    {
        $email = 'verified_user_' . rand(1000, 9999) . '@gmail.com';
        $phone = '96' . rand(10000000, 99999999);
        $payload = [
            'name' => 'Verified User',
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make('password123'),
        ];

        $otpRecord = EmailOtp::createOtp($email, 'register', $payload, 10);

        // Submit correct OTP
        $response = $this->postJson(route('register.verify-otp'), [
            'email' => $email,
            'otp' => $otpRecord->otp,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert user was created in database
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Verified User',
            'phone' => $phone,
        ]);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user->email_verified_at);

        // Assert user is authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_login_send_otp_and_verify_otp_works_for_registered_user(): void
    {
        $user = $this->getCustomerUser();

        // 1. Send Login OTP
        $sendResponse = $this->postJson(route('login.send-otp'), [
            'email' => $user->email,
        ]);

        $sendResponse->assertStatus(200);
        $sendResponse->assertJson(['success' => true]);

        $otpRecord = EmailOtp::where('email', $user->email)->where('action', 'login')->latest('id')->first();
        $this->assertNotNull($otpRecord);

        // 2. Verify Login OTP
        $verifyResponse = $this->postJson(route('login.verify-otp'), [
            'email' => $user->email,
            'otp' => $otpRecord->otp,
        ]);

        $verifyResponse->assertStatus(200);
        $verifyResponse->assertJson(['success' => true]);
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_logged_in_user_can_access_checkout(): void
    {
        $user = $this->getCustomerUser();

        // Add item to cart
        $product = Product::active()->first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Sample Item',
                'sku' => 'SMP-' . rand(100, 999),
                'regular_price' => 300,
                'stock_quantity' => 5,
                'status' => true,
            ]);
        }
        $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertStatus(200);

        $response = $this->actingAs($user)->get(route('checkout'));
        $response->assertStatus(200);
        $response->assertSee('Contact Information');
        $response->assertSee($user->email);
    }

    public function test_guest_cart_items_are_preserved_and_merged_when_user_logs_in(): void
    {
        $product = Product::active()->first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Preserved Emblem Item',
                'sku' => 'PRE-' . rand(100, 999),
                'regular_price' => 450,
                'stock_quantity' => 10,
                'status' => true,
            ]);
        }

        $password = 'password123';
        $user = $this->getCustomerUser();
        $user->update(['password' => Hash::make($password)]);

        // 1. Guest adds item to cart
        $addResponse = $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $addResponse->assertStatus(200);
        $addResponse->assertJson(['success' => true]);

        $guestCart = \App\Models\Cart::whereNull('user_id')->latest('id')->first();
        $this->assertNotNull($guestCart);

        // 2. Guest logs in (maintaining session with guest cart)
        $loginResponse = $this->withSession([
            'guest_cart_id' => $guestCart->id,
            'guest_cart_session_id' => $guestCart->session_id,
        ])->post(route('login.submit'), [
            'email' => $user->email,
            'password' => $password,
        ]);
        $loginResponse->assertRedirect();
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());

        // 3. User cart has preserved the item
        $userCart = \App\Models\Cart::where('user_id', $user->id)->with('items')->first();
        $this->assertNotNull($userCart);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // 4. Cart index & Checkout show the preserved item
        $cartPage = $this->get(route('cart.index'));
        $cartPage->assertStatus(200);
        $cartPage->assertSee($product->name);

        $checkoutPage = $this->get(route('checkout'));
        $checkoutPage->assertStatus(200);
        $checkoutPage->assertSee($product->name);
    }

    public function test_guest_cart_items_are_preserved_when_user_logs_in_via_otp(): void
    {
        $product = Product::active()->first();
        if (!$product) {
            $product = Product::create([
                'name' => 'OTP Cart Item',
                'sku' => 'OTP-' . rand(100, 999),
                'regular_price' => 350,
                'stock_quantity' => 10,
                'status' => true,
            ]);
        }

        $user = $this->getCustomerUser();

        // Guest adds item
        $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertStatus(200);

        $guestCart = \App\Models\Cart::whereNull('user_id')->latest('id')->first();
        $this->assertNotNull($guestCart);

        // Generate OTP
        $otp = EmailOtp::createOtp($user->email, 'login');

        // Verify OTP
        $this->withSession([
            'guest_cart_id' => $guestCart->id,
            'guest_cart_session_id' => $guestCart->session_id,
        ])->postJson(route('login.verify-otp'), [
            'email' => $user->email,
            'otp' => $otp->otp,
        ])->assertStatus(200);

        $this->assertTrue(Auth::check());
        $userCart = \App\Models\Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($userCart);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_guest_cart_items_are_preserved_when_user_registers_with_otp(): void
    {
        $product = Product::active()->first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Register Cart Item',
                'sku' => 'REG-' . rand(100, 999),
                'regular_price' => 399,
                'stock_quantity' => 10,
                'status' => true,
            ]);
        }

        $email = 'cart_register_' . rand(1000, 9999) . '@gmail.com';
        $phone = '95' . rand(10000000, 99999999);

        // Guest adds item
        $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertStatus(200);

        $guestCart = \App\Models\Cart::whereNull('user_id')->latest('id')->first();
        $this->assertNotNull($guestCart);

        // Create registration OTP
        $otp = EmailOtp::createOtp($email, 'register', [
            'name' => 'Cart Reg User',
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make('password123'),
        ]);

        // Verify registration OTP
        $this->withSession([
            'guest_cart_id' => $guestCart->id,
            'guest_cart_session_id' => $guestCart->session_id,
        ])->postJson(route('register.verify-otp'), [
            'email' => $email,
            'otp' => $otp->otp,
        ])->assertStatus(200);

        $this->assertTrue(Auth::check());
        $registeredUser = Auth::user();
        $userCart = \App\Models\Cart::where('user_id', $registeredUser->id)->first();
        $this->assertNotNull($userCart);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_checkout_displays_prefilled_user_data_with_locked_email_and_mobile(): void
    {
        $user = $this->getCustomerUser();
        $phone = '9876543210';
        $user->update([
            'name' => 'Amit Sharma',
            'phone' => $phone,
        ]);

        // Add an item so checkout allows access
        $product = Product::active()->first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Test Item',
                'sku' => 'TST-' . rand(100, 999),
                'regular_price' => 500,
                'stock_quantity' => 10,
                'status' => true,
            ]);
        }

        $this->actingAs($user)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertStatus(200);

        $response = $this->actingAs($user)->get(route('checkout'));
        $response->assertStatus(200);

        // Pre-filled values
        $response->assertSee('value="' . $user->email . '"', false);
        $response->assertSee('value="' . $phone . '"', false);
        $response->assertSee('value="Amit Sharma"', false);

        // Readonly / locked attributes and badges
        $response->assertSee('id="cust_mobile"', false);
        $response->assertSee('id="cust_email"', false);
        $response->assertSee('Verified (Locked)', false);
        $response->assertSee('readonly', false);
    }
}
