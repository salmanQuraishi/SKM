<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $token;
    protected ?string $phoneId;

    public function __construct()
    {
        $this->token = Setting::get('whatsapp_access_token', env('WHATSAPP_ACCESS_TOKEN'));
        $this->phoneId = Setting::get('whatsapp_phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID'));
    }

    public function getDirectChatUrl(string $phone, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        return 'https://api.whatsapp.com/send?phone=' . $cleanPhone . '&text=' . urlencode($message);
    }

    public function getOrderConfirmationMessage(Order $order): string
    {
        $storeName = Setting::get('store_name', 'AutoLogos India');
        $trackingLink = route('order.track.view', ['order_number' => $order->order_number, 'phone' => $order->customer_phone]);

        return "🚗 *{$storeName}* - Order Confirmation\n\n"
            . "Hello *{$order->customer_name}*,\n\n"
            . "Your order *#{$order->order_number}* has been received successfully!\n\n"
            . "📦 *Items:* " . $order->items->count() . " item(s)\n"
            . "💰 *Total Amount:* &#x20B9;{$order->total_amount}\n"
            . "💳 *Payment:* " . strtoupper($order->payment_method) . " ({$order->payment_status})\n\n"
            . "🔍 *Track your order:* {$trackingLink}\n\n"
            . "Thank you for choosing {$storeName} for your premium car emblems!";
    }

    public function getShippingUpdateMessage(Order $order): string
    {
        $storeName = Setting::get('store_name', 'AutoLogos India');

        return "📦 *{$storeName}* - Shipment Update\n\n"
            . "Hello *{$order->customer_name}*,\n\n"
            . "Great news! Your order *#{$order->order_number}* has been shipped!\n\n"
            . "🚚 *Courier:* " . ($order->courier_name ?: 'Express Courier') . "\n"
            . "🔖 *Tracking No:* " . ($order->tracking_number ?: 'Pending') . "\n"
            . "📅 *Expected Delivery:* " . ($order->expected_delivery_date ? $order->expected_delivery_date->format('d M Y') : '3-5 Business Days') . "\n\n"
            . "Thank you for shopping with us!";
    }

    public function sendCloudMessage(string $recipientPhone, string $message): bool
    {
        if (empty($this->token) || empty($this->phoneId)) {
            Log::info("WhatsApp API credentials missing. Direct web link used instead for: {$recipientPhone}");
            return false;
        }

        try {
            $cleanPhone = preg_replace('/[^0-9]/', '', $recipientPhone);
            if (strlen($cleanPhone) === 10) {
                $cleanPhone = '91' . $cleanPhone;
            }

            $response = Http::withToken($this->token)->post("https://graph.facebook.com/v18.0/{$this->phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $cleanPhone,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp Cloud API Error: ' . $e->getMessage());
            return false;
        }
    }
}
