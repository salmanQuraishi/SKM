<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function createOrder(array $customerData, array $shippingAddress, array $billingAddress, string $paymentMethod = 'cod', ?string $customerNotes = null, ?User $user = null): Order
    {
        return DB::transaction(function () use ($customerData, $shippingAddress, $billingAddress, $paymentMethod, $customerNotes, $user) {
            $cart = $this->cartService->getCart();
            $items = $cart->items()->with(['product', 'variant'])->get();

            if ($items->isEmpty()) {
                throw new \Exception('Cart is empty.');
            }

            $totals = $this->cartService->getCartTotals($paymentMethod);

            // Generate Order Number: AL-YYYYMM-XXXX
            $yearMonth = Carbon::now()->format('Ym');
            $latestOrder = Order::where('order_number', 'LIKE', "AL-{$yearMonth}-%")->latest('id')->first();
            $nextSequence = 1001;
            if ($latestOrder && preg_match('/AL-\d{6}-(\d+)/', $latestOrder->order_number, $matches)) {
                $nextSequence = ((int) $matches[1]) + 1;
            }
            $orderNumber = "AL-{$yearMonth}-{$nextSequence}";

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user ? $user->id : null,
                'customer_name' => $customerData['name'],
                'customer_email' => $customerData['email'],
                'customer_phone' => $customerData['mobile'],
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'subtotal' => $totals['subtotal'],
                'discount_amount' => $totals['discount'],
                'coupon_code' => $totals['coupon'],
                'tax_amount' => $totals['tax'],
                'shipping_amount' => $totals['shipping'],
                'cod_charge' => $totals['cod_charge'],
                'total_amount' => $totals['total'],
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'pending',
                'order_status' => $paymentMethod === 'cod' ? 'confirmed' : 'pending',
                'customer_notes' => $customerNotes,
            ]);

            // Save items and handle stock
            foreach ($items as $item) {
                $product = $item->product;
                $variant = $item->variant;
                $unitPrice = $variant ? $variant->active_price : $product->active_price;
                $availableStock = $variant ? $variant->stock_quantity : $product->stock_quantity;

                if ($availableStock < $item->quantity) {
                    throw new \Exception("Sorry, '{$product->name}' is out of stock or requested quantity unavailable.");
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'product_name' => $product->name,
                    'product_sku' => $variant ? $variant->sku : $product->sku,
                    'variant_name' => $variant ? $variant->name : null,
                    'price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'tax_rate' => $product->tax_rate ?? 18.00,
                    'tax_amount' => round(($unitPrice * $item->quantity * 0.18) / 1.18, 2),
                    'total' => round($unitPrice * $item->quantity, 2),
                    'product_image' => $product->primary_image_url,
                ]);

                // Decrement inventory immediately only for COD orders
                if ($paymentMethod === 'cod') {
                    if ($variant) {
                        $variant->decrement('stock_quantity', $item->quantity);
                    }
                    $product->decrement('stock_quantity', $item->quantity);
                }
            }

            // Record Coupon Usage (immediately for COD; for online payment, recorded when paid)
            if ($paymentMethod === 'cod' && $totals['coupon']) {
                $coupon = Coupon::where('code', $totals['coupon'])->first();
                if ($coupon) {
                    $coupon->increment('used_count');
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $user ? $user->id : null,
                        'order_id' => $order->id,
                        'discount_amount' => $totals['discount'],
                    ]);
                }
            }

            // Initial Status History
            $initialStatus = $paymentMethod === 'cod' ? 'confirmed' : 'pending';
            $order->statusHistory()->create([
                'status' => $initialStatus,
                'comment' => $paymentMethod === 'cod' ? 'Order placed via Cash On Delivery.' : 'Order initiated for online payment.',
                'notify_customer' => true,
                'created_by' => 'System',
            ]);

            // Automatically Generate Invoice
            $invoiceNumber = 'INV-' . Carbon::now()->format('Ym') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => Carbon::now(),
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'shipping_amount' => $order->shipping_amount + $order->cod_charge,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
            ]);

            // Clear the cart immediately only for COD; for online payment, cart is preserved until payment is verified
            if ($paymentMethod === 'cod') {
                $this->cartService->clear();
            }

            return $order;
        });
    }

    public function markOrderPaid(Order $order, string $paymentId, array $gatewayData = []): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        // Decrement inventory for online payment orders upon successful payment
        if ($order->payment_method !== 'cod') {
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->decrement('stock_quantity', $item->quantity);
                }
                if ($item->product) {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }
            }
        }

        // Record Coupon Usage for online payment upon successful payment
        if ($order->payment_method !== 'cod' && $order->coupon_code) {
            $coupon = Coupon::where('code', $order->coupon_code)->first();
            if ($coupon) {
                $coupon->increment('used_count');
                CouponUsage::firstOrCreate([
                    'order_id' => $order->id,
                ], [
                    'coupon_id' => $coupon->id,
                    'user_id' => $order->user_id,
                    'discount_amount' => $order->discount_amount,
                ]);
            }
        }

        $order->update([
            'payment_status' => 'paid',
            'order_status' => $order->order_status === 'pending' ? 'confirmed' : $order->order_status,
        ]);

        $order->payments()->create([
            'transaction_id' => $paymentId,
            'payment_gateway' => 'razorpay',
            'razorpay_order_id' => $gatewayData['razorpay_order_id'] ?? null,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $gatewayData['razorpay_signature'] ?? null,
            'amount' => $order->total_amount,
            'currency' => 'INR',
            'status' => 'captured',
            'payment_method_type' => $gatewayData['method'] ?? 'online',
            'raw_response' => $gatewayData,
        ]);

        // Create Payment Receipt
        $receiptNumber = 'REC-' . Carbon::now()->format('Ym') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
        Receipt::create([
            'order_id' => $order->id,
            'payment_id' => $order->latestPayment->id ?? null,
            'receipt_number' => $receiptNumber,
            'receipt_date' => Carbon::now(),
            'amount' => $order->total_amount,
            'payment_method' => 'Razorpay / Online',
        ]);

        $order->statusHistory()->create([
            'status' => 'confirmed',
            'comment' => "Payment of ₹" . number_format($order->total_amount, 2) . " received successfully (Txn: {$paymentId}).",
            'notify_customer' => true,
            'created_by' => 'Payment Gateway',
        ]);

        // Clear cart for the customer upon successful payment
        $this->cartService->clear();
    }

    public function updateOrderStatus(Order $order, string $newStatus, ?string $comment = null, ?string $courier = null, ?string $trackingNumber = null, ?string $adminName = 'Admin'): Order
    {
        $oldStatus = $order->order_status;

        $updateData = ['order_status' => $newStatus];
        if ($courier) {
            $updateData['courier_name'] = $courier;
        }
        if ($trackingNumber) {
            $updateData['tracking_number'] = $trackingNumber;
        }

        if ($newStatus === 'delivered') {
            $updateData['payment_status'] = 'paid';
        }

        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            // Restore inventory on cancellation ONLY if inventory was decremented (paid order or COD order)
            if ($order->payment_status === 'paid' || $order->payment_method === 'cod') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                    if ($item->variant) {
                        $item->variant->increment('stock_quantity', $item->quantity);
                    }
                }
            }
        }

        $order->update($updateData);

        $order->statusHistory()->create([
            'status' => $newStatus,
            'comment' => $comment ?: "Order status updated from {$oldStatus} to {$newStatus}.",
            'notify_customer' => true,
            'created_by' => $adminName,
        ]);

        return $order;
    }
}
