<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'store_name', 'value' => 'AUTOLOGOS', 'group' => 'general', 'type' => 'text'],
            ['key' => 'store_tagline', 'value' => 'CAR LOGOS & EMBLEMS', 'group' => 'general', 'type' => 'text'],
            ['key' => 'store_phone', 'value' => '+91 98765 43210', 'group' => 'general', 'type' => 'text'],
            ['key' => 'store_email', 'value' => 'info@autologos.in', 'group' => 'general', 'type' => 'text'],
            ['key' => 'store_address', 'value' => '123, Auto Street, New Delhi, India - 110001', 'group' => 'general', 'type' => 'textarea'],
            ['key' => 'gstin', 'value' => '07AAAAA0000A1Z5', 'group' => 'general', 'type' => 'text'],
            ['key' => 'currency_symbol', 'value' => '&#x20B9;', 'group' => 'general', 'type' => 'text'],
            ['key' => 'currency_code', 'value' => 'INR', 'group' => 'general', 'type' => 'text'],
            ['key' => 'announcement_bar_text', 'value' => 'Free Shipping on Orders Over &#x20B9;999', 'group' => 'general', 'type' => 'text'],

            // Shipping & COD
            ['key' => 'shipping_charge', 'value' => '99', 'group' => 'shipping', 'type' => 'number'],
            ['key' => 'free_shipping_threshold', 'value' => '999', 'group' => 'shipping', 'type' => 'number'],
            ['key' => 'enable_cod', 'value' => '1', 'group' => 'shipping', 'type' => 'boolean'],
            ['key' => 'cod_charge', 'value' => '50', 'group' => 'shipping', 'type' => 'number'],

            // Payment Gateway
            ['key' => 'enable_razorpay', 'value' => '1', 'group' => 'payment', 'type' => 'boolean'],
            ['key' => 'razorpay_enabled', 'value' => '1', 'group' => 'payment', 'type' => 'boolean'],
            ['key' => 'razorpay_mode', 'value' => 'test', 'group' => 'payment', 'type' => 'text'], // 'test' or 'live'
            ['key' => 'razorpay_key_id', 'value' => env('RAZORPAY_KEY_ID', 'rzp_test_sampleKey123456'), 'group' => 'payment', 'type' => 'text'],
            ['key' => 'razorpay_key_secret', 'value' => env('RAZORPAY_KEY_SECRET', 'sampleSecretValueRazorpay123'), 'group' => 'payment', 'type' => 'text'],
            ['key' => 'razorpay_webhook_secret', 'value' => env('RAZORPAY_WEBHOOK_SECRET', ''), 'group' => 'payment', 'type' => 'text'],

            // WhatsApp
            ['key' => 'whatsapp_phone', 'value' => '919876543210', 'group' => 'whatsapp', 'type' => 'text'],
            ['key' => 'whatsapp_access_token', 'value' => env('WHATSAPP_ACCESS_TOKEN', ''), 'group' => 'whatsapp', 'type' => 'text'],
            ['key' => 'whatsapp_phone_number_id', 'value' => env('WHATSAPP_PHONE_NUMBER_ID', ''), 'group' => 'whatsapp', 'type' => 'text'],

            // Social
            ['key' => 'facebook_url', 'value' => 'https://facebook.com', 'group' => 'social', 'type' => 'text'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com', 'group' => 'social', 'type' => 'text'],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com', 'group' => 'social', 'type' => 'text'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
