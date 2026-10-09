<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\Payment\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    protected RazorpayService $razorpayService;
    protected OrderService $orderService;

    public function __construct(RazorpayService $razorpayService, OrderService $orderService)
    {
        $this->razorpayService = $razorpayService;
        $this->orderService = $orderService;
    }

    public function handleWebhook(Request $request)
    {
        $rawPayload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        Log::info('Razorpay Webhook Received', [
            'has_signature' => !empty($signature),
            'event' => $request->input('event'),
        ]);

        // Verify webhook signature if webhook secret is configured
        if ($this->razorpayService->getWebhookSecret()) {
            if (!$signature || !$this->razorpayService->verifyWebhookSignature($rawPayload, $signature)) {
                Log::warning('Razorpay Webhook signature verification failed');
                return response()->json(['error' => 'Invalid webhook signature'], 400);
            }
        }

        $event = $request->input('event');
        $payload = $request->input('payload');

        try {
            switch ($event) {
                case 'payment.captured':
                case 'order.paid':
                    $this->handlePaymentCaptured($payload, $event);
                    break;

                case 'payment.failed':
                    $this->handlePaymentFailed($payload);
                    break;

                default:
                    Log::info("Unhandled Razorpay event: {$event}");
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Razorpay Webhook processing exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handlePaymentCaptured(array $payload, string $event): void
    {
        $paymentData = $payload['payment']['entity'] ?? [];
        $orderData = $payload['order']['entity'] ?? [];

        $paymentId = $paymentData['id'] ?? null;
        $rzpOrderId = $paymentData['order_id'] ?? ($orderData['id'] ?? null);

        // Try locating order by notes or receipt
        $orderNumber = $paymentData['notes']['order_number'] ?? ($orderData['receipt'] ?? null);
        $orderId = $paymentData['notes']['order_id'] ?? null;

        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
        }

        if (!$order && $orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
        }

        if (!$order && $rzpOrderId) {
            $order = Order::whereHas('payments', function ($q) use ($rzpOrderId) {
                $q->where('razorpay_order_id', $rzpOrderId);
            })->first();
        }

        if ($order && $paymentId) {
            if ($order->payment_status !== 'paid') {
                $this->orderService->markOrderPaid($order, $paymentId, [
                    'razorpay_order_id' => $rzpOrderId,
                    'razorpay_payment_id' => $paymentId,
                    'method' => $paymentData['method'] ?? 'online',
                    'webhook_event' => $event,
                ]);
                Log::info("Order #{$order->order_number} marked paid via Razorpay webhook ({$event})");
            }
        } else {
            Log::warning("Could not associate Razorpay payment {$paymentId} with an existing order.");
        }
    }

    protected function handlePaymentFailed(array $payload): void
    {
        $paymentData = $payload['payment']['entity'] ?? [];
        $paymentId = $paymentData['id'] ?? null;
        $orderNumber = $paymentData['notes']['order_number'] ?? null;
        $orderId = $paymentData['notes']['order_id'] ?? null;
        $errorDescription = $paymentData['error_description'] ?? 'Payment failed';

        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
        } elseif ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
        }

        if ($order && $order->payment_status !== 'paid') {
            $order->statusHistory()->create([
                'status' => 'pending',
                'comment' => "Online payment failed (Txn: {$paymentId}): {$errorDescription}",
                'notify_customer' => false,
                'created_by' => 'Razorpay Webhook',
            ]);
            Log::info("Payment failed recorded for Order #{$order->order_number}");
        }
    }
}
