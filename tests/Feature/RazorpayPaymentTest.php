<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\Payment\RazorpayService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RazorpayPaymentTest extends TestCase
{
    use DatabaseTransactions;
    protected function getAdminUser(): User
    {
        $adminRole = Role::firstOrCreate(
            ['slug' => 'super_admin'],
            ['name' => 'Super Admin']
        );

        $admin = User::where('role_id', $adminRole->id)->where('is_active', true)->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]);
        }

        return $admin;
    }

    protected function getCustomerUser(): User
    {
        $user = User::where('email', 'customer@autologos.in')->first();
        if (!$user) {
            $userRole = Role::whereIn('slug', ['user', 'customer'])->first();
            $user = User::create([
                'name' => 'Rohit Sharma',
                'email' => 'customer@autologos.in',
                'password' => bcrypt('secret123'),
                'role_id' => $userRole?->id,
                'is_active' => true,
            ]);
        }
        return $user;
    }

    protected function createTestOrder(array $attributes = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'ORD-RZP-' . strtoupper(uniqid()),
            'customer_name' => 'Test Customer',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '9876543210',
            'shipping_address' => [
                'address_line_1' => 'Plot 10, Auto Road',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
            ],
            'billing_address' => [
                'address_line_1' => 'Plot 10, Auto Road',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
            ],
            'subtotal' => 1200.00,
            'total_amount' => 1200.00,
            'payment_method' => 'razorpay',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ], $attributes));
    }

    /**
     * Test 1: Admin can update Razorpay credentials & configuration
     */
    public function test_admin_can_update_razorpay_payment_settings(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'razorpay_enabled' => '1',
            'razorpay_mode' => 'test',
            'razorpay_key_id' => 'rzp_test_myRealTestKey123',
            'razorpay_key_secret' => 'myRealSecretKey456',
            'razorpay_webhook_secret' => 'whsec_testingSecret789',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('1', Setting::get('razorpay_enabled'));
        $this->assertEquals('1', Setting::get('enable_razorpay'));
        $this->assertEquals('test', Setting::get('razorpay_mode'));
        $this->assertEquals('rzp_test_myRealTestKey123', Setting::get('razorpay_key_id'));
        $this->assertEquals('myRealSecretKey456', Setting::get('razorpay_key_secret'));
        $this->assertEquals('whsec_testingSecret789', Setting::get('razorpay_webhook_secret'));

        $service = app(RazorpayService::class);
        $this->assertTrue($service->isEnabled());
        $this->assertTrue($service->isTestMode());
        $this->assertTrue($service->isConfigured());
        $this->assertEquals('rzp_test_myRealTestKey123', $service->getKeyId());
    }

    /**
     * Test 2: Razorpay service handles mock/demo mode when sample credentials are used
     */
    public function test_razorpay_service_handles_demo_mock_mode_gracefully(): void
    {
        Setting::set('razorpay_key_id', 'rzp_test_sampleKey123456', 'payment');
        Setting::set('razorpay_key_secret', 'sampleSecretValueRazorpay123', 'payment');
        Setting::set('razorpay_mode', 'test', 'payment');

        $service = new RazorpayService();
        $this->assertFalse($service->isConfigured()); // because it has 'sample'
        $this->assertTrue($service->isTestMode());

        $order = $this->createTestOrder([
            'total_amount' => 1499.00,
        ]);

        $rzpOrder = $service->createRazorpayOrder($order);
        $this->assertTrue($rzpOrder['success']);
        $this->assertTrue($rzpOrder['is_mock']);
        $this->assertEquals(149900, $rzpOrder['amount']);
        $this->assertNotEmpty($rzpOrder['razorpay_order_id']);
    }

    /**
     * Test: Checkout page renders successfully with totals and payment methods
     */
    public function test_checkout_page_renders_with_totals_and_payment_methods(): void
    {
        $customer = $this->getCustomerUser();
        $product = Product::active()->first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Chrome Grille Monogram',
                'slug' => 'chrome-grille-monogram-' . uniqid(),
                'selling_price' => 999.00,
                'regular_price' => 1299.00,
                'stock_quantity' => 10,
                'status' => true,
            ]);
        }

        $this->actingAs($customer)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertStatus(200);

        $response = $this->actingAs($customer)->get(route('checkout'));
        $response->assertStatus(200);
        $response->assertViewHas('totals');
        $response->assertViewHas('cart');
        $response->assertSee('ORDER ITEMS', false);
        $response->assertSee('Payment Method', false);
    }

    /**
     * Test 3: Checkout process endpoint creates order and returns Razorpay initialization JSON
     */
    public function test_checkout_process_initiates_razorpay_order(): void
    {
        Setting::set('razorpay_enabled', '1', 'payment');
        Setting::set('enable_razorpay', '1', 'payment');

        $customer = $this->getCustomerUser();

        $product = Product::active()->first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Chrome Grille Monogram',
                'slug' => 'chrome-grille-monogram-' . uniqid(),
                'selling_price' => 999.00,
                'regular_price' => 1299.00,
                'stock_quantity' => 10,
                'status' => true,
            ]);
        }

        // Add to cart
        $this->actingAs($customer)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertStatus(200);

        // Process checkout with Razorpay
        $response = $this->actingAs($customer)->postJson(route('checkout.process'), [
            'name' => 'Rahul Sharma',
            'email' => 'rahul@example.com',
            'mobile' => '9876543210',
            'address_line_1' => 'Flat 204, Lotus Enclave',
            'city' => 'Gurugram',
            'state' => 'Haryana',
            'pincode' => '122001',
            'payment_method' => 'razorpay',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'customer_name' => 'Rahul Sharma',
            'customer_email' => 'rahul@example.com',
            'customer_phone' => '9876543210',
        ]);
        $response->assertJsonStructure([
            'success',
            'order_number',
            'order_id',
            'razorpay_order_id',
            'amount',
            'currency',
            'key_id',
        ]);
    }

    /**
     * Test 4: Online payment verification marks order paid and issues receipt
     */
    public function test_checkout_payment_verification_marks_order_as_paid(): void
    {
        $order = $this->createTestOrder([
            'total_amount' => 1250.00,
            'payment_method' => 'razorpay',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $response = $this->postJson(route('checkout.verify-payment'), [
            'order_number' => $order->order_number,
            'razorpay_order_id' => 'order_simulated123',
            'razorpay_payment_id' => 'pay_simulated_789456',
            'razorpay_signature' => 'mock_signature_valid',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'redirect_url' => route('checkout.success', $order->order_number),
        ]);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('confirmed', $order->order_status);
        $this->assertNotNull($order->receipt);
        $this->assertCount(1, $order->payments);
        $this->assertEquals('pay_simulated_789456', $order->payments->first()->transaction_id);

        // Test idempotency: calling verification again on an already paid order returns success redirect without duplicating records
        $repeatResponse = $this->postJson(route('checkout.verify-payment'), [
            'order_number' => $order->order_number,
            'razorpay_order_id' => 'order_simulated123',
            'razorpay_payment_id' => 'pay_simulated_789456',
            'razorpay_signature' => 'mock_signature_valid',
        ]);

        $repeatResponse->assertStatus(200);
        $repeatResponse->assertJson(['success' => true]);
        $this->assertCount(1, $order->payments);
    }

    /**
     * Test 5: Razorpay Webhook updates order asynchronously with valid signature
     */
    public function test_razorpay_webhook_marks_order_paid(): void
    {
        $webhookSecret = 'whsec_test_secret_123';
        Setting::set('razorpay_webhook_secret', $webhookSecret, 'payment');

        $order = $this->createTestOrder([
            'total_amount' => 2499.00,
            'payment_method' => 'razorpay',
            'payment_status' => 'pending',
        ]);

        $webhookPayload = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_wh_test_12345',
                        'order_id' => 'order_wh_test_67890',
                        'amount' => 249900,
                        'currency' => 'INR',
                        'status' => 'captured',
                        'method' => 'upi',
                        'notes' => [
                            'order_id' => (string)$order->id,
                            'order_number' => $order->order_number,
                        ],
                    ],
                ],
            ],
        ];

        $rawJson = json_encode($webhookPayload);

        // 5a: Invalid signature should return 400
        $badResponse = $this->call('POST', route('razorpay.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => 'invalid_signature_hash',
        ], $rawJson);
        $badResponse->assertStatus(400);

        // 5b: Valid signature should process successfully and mark order paid
        $validSignature = hash_hmac('sha256', $rawJson, $webhookSecret);
        $response = $this->call('POST', route('razorpay.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => $validSignature,
        ], $rawJson);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('confirmed', $order->order_status);
        $this->assertNotNull($order->receipt);
    }

    /**
     * Test 6: Checkout rejects Razorpay order if admin has disabled Razorpay
     */
    public function test_checkout_rejects_razorpay_if_disabled(): void
    {
        Setting::set('razorpay_enabled', '0', 'payment');
        Setting::set('enable_razorpay', '0', 'payment');

        $customer = $this->getCustomerUser();

        $product = Product::active()->first();
        if (!$product) {
            $product = Product::create([
                'name' => 'Car Logo Emblem',
                'slug' => 'car-logo-emblem-' . uniqid(),
                'selling_price' => 500,
                'regular_price' => 600,
                'stock_quantity' => 10,
                'status' => true,
            ]);
        }

        $this->actingAs($customer)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($customer)->postJson(route('checkout.process'), [
            'name' => 'Anil Verma',
            'email' => 'anil@example.com',
            'mobile' => '9811122233',
            'address_line_1' => 'House 12, Sector 14',
            'city' => 'Noida',
            'state' => 'Uttar Pradesh',
            'pincode' => '201301',
            'payment_method' => 'razorpay',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test 7: Cart and stock are preserved when online payment is initiated and cancelled
     */
    public function test_cart_and_stock_remain_intact_when_online_payment_is_initiated_and_cancelled(): void
    {
        Setting::set('razorpay_enabled', '1', 'payment');
        Setting::set('enable_razorpay', '1', 'payment');

        $customer = $this->getCustomerUser();
        $this->actingAs($customer);
        app(\App\Services\CartService::class)->clear();

        $product = Product::create([
            'name' => 'Preserved Stock Emblem',
            'slug' => 'preserved-stock-emblem-' . uniqid(),
            'sku' => 'PSE-' . strtoupper(uniqid()),
            'selling_price' => 750,
            'regular_price' => 899,
            'stock_quantity' => 5,
            'status' => true,
        ]);

        // Add to cart
        $this->actingAs($customer)->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertStatus(200);

        // Initiate online checkout
        $response = $this->actingAs($customer)->postJson(route('checkout.process'), [
            'name' => 'Pooja Sharma',
            'email' => 'pooja@example.com',
            'mobile' => '9876543210',
            'address_line_1' => 'Flat 101, Green View',
            'city' => 'Jaipur',
            'state' => 'Rajasthan',
            'pincode' => '302001',
            'payment_method' => 'razorpay',
        ]);

        $response->assertStatus(200);
        $orderNumber = $response->json('order_number');

        // Verify product stock is NOT decremented yet
        $product->refresh();
        $this->assertEquals(5, $product->stock_quantity);

        // Verify customer cart still has the product
        $cart = app(\App\Services\CartService::class)->getCart();
        $this->assertFalse($cart->items->isEmpty());
        $this->assertEquals(1, $cart->items->first()->quantity);

        // Now verify payment completes successfully
        $verifyResponse = $this->actingAs($customer)->postJson(route('checkout.verify-payment'), [
            'order_number' => $orderNumber,
            'razorpay_order_id' => 'order_test_preserve_123',
            'razorpay_payment_id' => 'pay_test_preserve_456',
            'razorpay_signature' => 'mock_signature_valid',
        ]);

        $verifyResponse->assertStatus(200);

        // Now stock IS decremented and cart IS cleared
        $product->refresh();
        $this->assertEquals(4, $product->stock_quantity);

        $cart = app(\App\Services\CartService::class)->getCart();
        $this->assertTrue($cart->items->isEmpty());
    }
}
