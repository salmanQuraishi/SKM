<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payment\RazorpayService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;
    protected RazorpayService $razorpayService;
    protected WhatsAppService $whatsAppService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        RazorpayService $razorpayService,
        WhatsAppService $whatsAppService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->razorpayService = $razorpayService;
        $this->whatsAppService = $whatsAppService;
    }

    public function index()
    {
        $cart = $this->cartService->getCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your shopping cart is empty.');
        }

        $user = Auth::user();
        $savedAddresses = $user ? $user->addresses()->latest()->get() : collect();
        $totals = $this->cartService->getCartTotals();
        $razorpayKey = $this->razorpayService->getKeyId();
        $razorpayEnabled = $this->razorpayService->isEnabled();
        $isRazorpayConfigured = $this->razorpayService->isConfigured();
        $isTestMode = $this->razorpayService->isTestMode();
        $codEnabled = Setting::get('cod_enabled', Setting::get('enable_cod', '1')) == '1';

        return view('frontend.checkout', compact('cart', 'user', 'savedAddresses', 'totals', 'razorpayKey', 'razorpayEnabled', 'isRazorpayConfigured', 'isTestMode', 'codEnabled'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|digits:6',
            'payment_method' => 'required|in:cod,razorpay',
            'customer_notes' => 'nullable|string|max:500',
            'save_address' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        $shippingAddress = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'landmark' => $request->landmark,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => 'India',
        ];

        $billingAddress = $shippingAddress;

        if ($user && $request->filled('save_address')) {
            Address::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'landmark' => $request->landmark,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'country' => 'India',
                'is_default' => $user->addresses()->count() === 0,
            ]);
        }

        $customerData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ];

        $paymentMethod = $request->payment_method;

        if ($paymentMethod === 'razorpay' && !$this->razorpayService->isEnabled()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Online payment is currently disabled. Please choose another payment method.'], 422);
            }
            return redirect()->back()->with('error', 'Online payment is currently disabled. Please choose another payment method.')->withInput();
        }

        if ($paymentMethod === 'cod' && Setting::get('cod_enabled', Setting::get('enable_cod', '1')) !== '1') {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Cash on Delivery is currently disabled. Please choose another payment method.'], 422);
            }
            return redirect()->back()->with('error', 'Cash on Delivery is currently disabled. Please choose another payment method.')->withInput();
        }

        try {
            // Cancel any previous unpaid pending order for this customer/session
            if ($user) {
                Order::where('user_id', $user->id)
                    ->where('order_status', 'pending')
                    ->where('payment_status', 'pending')
                    ->update(['order_status' => 'cancelled']);
            } elseif (session()->has('last_pending_order_id')) {
                Order::where('id', session('last_pending_order_id'))
                    ->where('order_status', 'pending')
                    ->where('payment_status', 'pending')
                    ->update(['order_status' => 'cancelled']);
            }

            $order = $this->orderService->createOrder(
                $customerData,
                $shippingAddress,
                $billingAddress,
                $paymentMethod,
                $request->customer_notes,
                $user
            );

            if ($paymentMethod === 'cod') {
                session()->forget('last_pending_order_id');
                return redirect()->route('checkout.success', $order->order_number)
                    ->with('success', 'Thank you! Your Cash on Delivery order has been confirmed.');
            }

            session(['last_pending_order_id' => $order->id]);

            // For Online Payment (Razorpay): Create Razorpay Order
            $rzpData = $this->razorpayService->createRazorpayOrder($order);

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'order_id' => $order->id,
                'razorpay_order_id' => $rzpData['razorpay_order_id'],
                'amount' => $rzpData['amount'],
                'currency' => $rzpData['currency'],
                'key_id' => $rzpData['key_id'],
                'is_mock' => $rzpData['is_mock'] ?? false,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
            ]);

        } catch (\Exception $e) {
            Log::error('Order creation error: ' . $e->getMessage());
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_number' => 'required|exists:orders,order_number',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'nullable|string',
        ]);

        $order = Order::where('order_number', $request->order_number)->firstOrFail();

        // If already marked as paid (e.g. by webhook), redirect immediately
        if ($order->payment_status === 'paid') {
            session()->forget('last_pending_order_id');
            return response()->json([
                'success' => true,
                'redirect_url' => route('checkout.success', $order->order_number),
            ]);
        }

        $verified = $this->razorpayService->verifyPaymentSignature($request->all());

        if ($verified) {
            $this->orderService->markOrderPaid($order, $request->razorpay_payment_id, $request->all());
            session()->forget('last_pending_order_id');

            return response()->json([
                'success' => true,
                'redirect_url' => route('checkout.success', $order->order_number),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed. Please contact customer support.',
        ], 400);
    }

    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'invoice', 'receipt', 'latestPayment'])
            ->firstOrFail();

        $whatsappMessage = $this->whatsAppService->getOrderConfirmationMessage($order);
        $whatsappUrl = $this->whatsAppService->getDirectChatUrl($order->customer_phone, $whatsappMessage);

        return view('frontend.checkout_success', compact('order', 'whatsappUrl'));
    }
}
