<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Setting;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected ?string $keyId = null;
    protected ?string $keySecret = null;
    protected ?string $webhookSecret = null;
    protected ?Api $api = null;

    public function __construct()
    {
        $dbKey = Setting::get('razorpay_key_id');
        $envKey = config('services.razorpay.key', env('RAZORPAY_KEY_ID', ''));

        // Prioritize non-dummy key: if DB holds a sample/placeholder key but .env has a real key, use .env
        if ((empty($dbKey) || str_contains($dbKey, 'sample') || str_contains($dbKey, 'placeholder')) && !empty($envKey)) {
            $this->keyId = $envKey;
        } else {
            $this->keyId = $dbKey ?: $envKey;
        }

        $dbSecret = Setting::get('razorpay_key_secret');
        $envSecret = config('services.razorpay.secret', env('RAZORPAY_KEY_SECRET', ''));
        if ((empty($dbSecret) || str_contains($dbSecret, 'sample') || str_contains($dbSecret, 'placeholder')) && !empty($envSecret)) {
            $this->keySecret = $envSecret;
        } else {
            $this->keySecret = $dbSecret ?: $envSecret;
        }

        $dbWebhookSecret = Setting::get('razorpay_webhook_secret');
        $envWebhookSecret = config('services.razorpay.webhook_secret', env('RAZORPAY_WEBHOOK_SECRET', ''));
        $this->webhookSecret = $dbWebhookSecret ?: $envWebhookSecret;

        if ($this->isConfigured()) {
            try {
                $this->api = new Api($this->keyId, $this->keySecret);
            } catch (\Exception $e) {
                Log::error('Razorpay Init Error: ' . $e->getMessage());
            }
        }
    }

    public function isEnabled(): bool
    {
        return Setting::get('razorpay_enabled', Setting::get('enable_razorpay', '1')) == '1';
    }

    public function isConfigured(): bool
    {
        return !empty($this->keyId)
            && !empty($this->keySecret)
            && !str_contains($this->keyId, 'sample')
            && !str_contains($this->keyId, 'placeholder');
    }

    public function isTestMode(): bool
    {
        $mode = Setting::get('razorpay_mode', 'test');
        if ($mode === 'live') {
            return false;
        }
        return str_starts_with((string)$this->keyId, 'rzp_test_') || $mode === 'test';
    }

    public function getKeyId(): string
    {
        return $this->keyId ?: 'rzp_test_sampleKey123456';
    }

    public function getKeySecret(): ?string
    {
        return $this->keySecret;
    }

    public function getWebhookSecret(): ?string
    {
        return $this->webhookSecret;
    }

    public function getApi(): ?Api
    {
        return $this->api;
    }

    public function createRazorpayOrder(Order $order): array
    {
        $amountInPaise = (int) round($order->total_amount * 100);

        if ($this->isConfigured() && $this->api) {
            try {
                $rzpOrder = $this->api->order->create([
                    'receipt' => $order->order_number,
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'notes' => [
                        'order_id' => (string) $order->id,
                        'order_number' => (string) $order->order_number,
                        'customer_name' => (string) $order->customer_name,
                        'customer_phone' => (string) $order->customer_phone,
                    ],
                ]);

                return [
                    'success' => true,
                    'razorpay_order_id' => $rzpOrder['id'],
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'key_id' => $this->keyId,
                    'is_mock' => false,
                ];
            } catch (\Exception $e) {
                Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
                throw new \Exception('Failed to initialize Razorpay payment: ' . $e->getMessage());
            }
        }

        // Return simulated mock Razorpay Order ID for test/sandbox mode if API credentials are dummy
        $mockOrderId = 'order_' . substr(md5($order->order_number . time()), 0, 14);
        return [
            'success' => true,
            'razorpay_order_id' => $mockOrderId,
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'key_id' => $this->getKeyId(),
            'is_mock' => true,
        ];
    }

    public function verifyPaymentSignature(array $attributes): bool
    {
        $razorpayOrderId = $attributes['razorpay_order_id'] ?? '';
        $razorpayPaymentId = $attributes['razorpay_payment_id'] ?? '';
        $razorpaySignature = $attributes['razorpay_signature'] ?? '';

        if (empty($razorpayOrderId) || empty($razorpayPaymentId)) {
            return false;
        }

        // If mock test order and placeholder credentials, accept valid format
        if (!$this->isConfigured()) {
            return true;
        }

        if (!empty($this->keySecret)) {
            $expectedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $this->keySecret);
            return hash_equals($expectedSignature, (string)$razorpaySignature);
        }

        return false;
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if (empty($this->webhookSecret) || empty($signature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $this->webhookSecret);
        return hash_equals($expected, $signature);
    }

    public function fetchPayment(string $paymentId): ?array
    {
        if ($this->api) {
            try {
                $payment = $this->api->payment->fetch($paymentId);
                return $payment ? $payment->toArray() : null;
            } catch (\Exception $e) {
                Log::warning('Razorpay fetchPayment warning: ' . $e->getMessage());
            }
        }
        return null;
    }
}
