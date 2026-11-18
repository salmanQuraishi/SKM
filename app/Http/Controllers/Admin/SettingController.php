<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,gif|max:4096',
            'site_favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,svg,webp|max:1024',
            'store_name' => 'nullable|string|max:150',
            'store_tagline' => 'nullable|string|max:150',
            'store_email' => 'nullable|email|max:150',
            'store_mobile' => 'nullable|string|max:30',
            'store_phone' => 'nullable|string|max:30',
            'mail_mailer' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:150',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:150',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:50',
            'mail_from_address' => 'nullable|email|max:150',
            'mail_from_name' => 'nullable|string|max:150',
        ]);

        $brandingDir = public_path('images/branding');
        if (!File::exists($brandingDir)) {
            File::makeDirectory($brandingDir, 0755, true);
        }

        // Handle Site Logo upload
        if ($request->hasFile('site_logo')) {
            $logoFile = $request->file('site_logo');
            $existingLogo = Setting::get('site_logo');
            if ($existingLogo && File::exists(public_path($existingLogo)) && str_contains($existingLogo, 'images/branding')) {
                @File::delete(public_path($existingLogo));
            }

            $logoName = 'logo_' . time() . '_' . Str::random(6) . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move($brandingDir, $logoName);
            Setting::set('site_logo', 'images/branding/' . $logoName, 'general');
        } elseif ($request->boolean('remove_site_logo')) {
            $existingLogo = Setting::get('site_logo');
            if ($existingLogo && File::exists(public_path($existingLogo)) && str_contains($existingLogo, 'images/branding')) {
                @File::delete(public_path($existingLogo));
            }
            Setting::set('site_logo', '', 'general');
        }

        // Handle Site Favicon upload
        if ($request->hasFile('site_favicon')) {
            $faviconFile = $request->file('site_favicon');
            $existingFavicon = Setting::get('site_favicon');
            if ($existingFavicon && File::exists(public_path($existingFavicon)) && str_contains($existingFavicon, 'images/branding')) {
                @File::delete(public_path($existingFavicon));
            }

            $faviconName = 'favicon_' . time() . '_' . Str::random(6) . '.' . $faviconFile->getClientOriginalExtension();
            $faviconFile->move($brandingDir, $faviconName);
            Setting::set('site_favicon', 'images/branding/' . $faviconName, 'general');
        } elseif ($request->boolean('remove_site_favicon')) {
            $existingFavicon = Setting::get('site_favicon');
            if ($existingFavicon && File::exists(public_path($existingFavicon)) && str_contains($existingFavicon, 'images/branding')) {
                @File::delete(public_path($existingFavicon));
            }
            Setting::set('site_favicon', '', 'general');
        }

        $inputs = $request->except(['_token', '_method', 'site_logo', 'site_favicon', 'remove_site_logo', 'remove_site_favicon']);

        foreach ($inputs as $key => $value) {
            $group = 'general';
            if (str_starts_with($key, 'razorpay_') || $key === 'enable_razorpay') {
                $group = 'payment';
            } elseif (str_starts_with($key, 'shipping_') || $key === 'free_shipping_threshold' || str_starts_with($key, 'cod_') || $key === 'enable_cod') {
                $group = 'shipping';
            } elseif (str_starts_with($key, 'whatsapp_')) {
                $group = 'whatsapp';
            } elseif (str_starts_with($key, 'mail_')) {
                $group = 'mail';
            } elseif (in_array($key, ['facebook_url', 'instagram_url', 'youtube_url', 'twitter_url', 'telegram_url'])) {
                $group = 'social';
            }

            Setting::set($key, is_array($value) ? json_encode($value) : (string) $value, $group);
        }

        // Synchronization for backward-compatibility aliases
        if ($request->has('header_announcement_text')) {
            Setting::set('announcement_bar_text', (string) $request->header_announcement_text, 'general');
        }
        if ($request->has('store_mobile')) {
            if (!$request->filled('store_phone')) {
                Setting::set('store_phone', (string) $request->store_mobile, 'general');
            }
        }
        if ($request->has('shipping_flat_rate')) {
            Setting::set('shipping_charge', (string) $request->shipping_flat_rate, 'shipping');
        }
        if ($request->has('cod_enabled')) {
            Setting::set('enable_cod', (string) $request->cod_enabled, 'shipping');
        }
        if ($request->has('razorpay_enabled')) {
            Setting::set('enable_razorpay', (string) $request->razorpay_enabled, 'payment');
        }
        if ($request->has('whatsapp_support_number')) {
            Setting::set('whatsapp_phone', (string) $request->whatsapp_support_number, 'whatsapp');
        }

        // If mail settings were submitted, sync with .env file and purge mailer
        if ($request->has('mail_mailer') || $request->has('mail_host')) {
            $this->updateEnvFile([
                'MAIL_MAILER' => $request->input('mail_mailer', 'smtp'),
                'MAIL_HOST' => $request->input('mail_host', '127.0.0.1'),
                'MAIL_PORT' => $request->input('mail_port', '587'),
                'MAIL_USERNAME' => $request->input('mail_username', ''),
                'MAIL_PASSWORD' => $request->input('mail_password', ''),
                'MAIL_ENCRYPTION' => in_array(strtolower((string)$request->input('mail_encryption')), ['null', 'none'], true) ? 'null' : $request->input('mail_encryption', 'tls'),
                'MAIL_FROM_ADDRESS' => $request->input('mail_from_address', Setting::get('store_email', 'sales@skmsaller.com')),
                'MAIL_FROM_NAME' => $request->input('mail_from_name', Setting::get('store_name', 'SKM Saller')),
            ]);

            try {
                \Illuminate\Support\Facades\Mail::purge('smtp');
            } catch (\Throwable $e) {}
        }

        Cache::forget('all_settings');

        return redirect()->route('admin.settings.index')->with('success', 'Store, SMTP, and company settings updated successfully!');
    }

    /**
     * Clear application cache, compiled views, routes, config, and temporary data.
     */
    public function clearCache(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            Cache::flush();
            Cache::forget('all_settings');

            // Attempt storage:link and mirror existing storage files for live/shared hosting
            try {
                if (!file_exists(public_path('storage'))) {
                    \Illuminate\Support\Facades\Artisan::call('storage:link');
                }
            } catch (\Throwable $e) {}
            \App\Services\FileUploadService::syncAllStorageFiles();

            $msg = 'System cache cleared & storage files synced successfully!';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg
                ]);
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            $errMsg = 'Failed to clear cache: ' . $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errMsg
                ], 500);
            }

            return redirect()->back()->with('error', $errMsg);
        }
    }

    /**
     * Quick toggle payment method status (Online or COD) via AJAX.
     */
    public function togglePaymentMethod(Request $request)
    {
        $request->validate([
            'method' => 'required|in:online,razorpay,cod',
        ]);

        $method = $request->input('method');

        if ($method === 'online' || $method === 'razorpay') {
            if ($request->has('status')) {
                $new = in_array((string)$request->input('status'), ['1', 'true', 'open'], true) ? '1' : '0';
            } else {
                $current = Setting::get('razorpay_enabled', Setting::get('enable_razorpay', '1')) == '1';
                $new = $current ? '0' : '1';
            }
            Setting::set('razorpay_enabled', $new, 'payment');
            Setting::set('enable_razorpay', $new, 'payment');
            Cache::forget('all_settings');

            return response()->json([
                'success' => true,
                'method' => 'online',
                'is_active' => $new === '1',
                'label' => $new === '1' ? 'Online Payment Open' : 'Online Payment Closed',
                'message' => $new === '1' ? 'Online payment (Razorpay/UPI) is now OPEN at checkout!' : 'Online payment is now CLOSED (hidden) at checkout.'
            ]);
        }

        if ($method === 'cod') {
            if ($request->has('status')) {
                $new = in_array((string)$request->input('status'), ['1', 'true', 'open'], true) ? '1' : '0';
            } else {
                $current = Setting::get('cod_enabled', Setting::get('enable_cod', '1')) == '1';
                $new = $current ? '0' : '1';
            }
            Setting::set('cod_enabled', $new, 'shipping');
            Setting::set('enable_cod', $new, 'shipping');
            Cache::forget('all_settings');

            return response()->json([
                'success' => true,
                'method' => 'cod',
                'is_active' => $new === '1',
                'label' => $new === '1' ? 'Cash on Delivery Open' : 'Cash on Delivery Closed',
                'message' => $new === '1' ? 'Cash on Delivery (COD) is now OPEN at checkout!' : 'Cash on Delivery is now CLOSED (hidden) at checkout.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid payment method.'], 400);
    }

    /**
     * Send test email using current or submitted SMTP settings to verify connectivity.
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email|max:150',
            'mail_mailer' => 'nullable|string|max:50',
            'mail_host' => 'nullable|string|max:150',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:150',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:50',
            'mail_from_address' => 'nullable|email|max:150',
            'mail_from_name' => 'nullable|string|max:150',
        ]);

        try {
            $mailer = $request->input('mail_mailer', Setting::get('mail_mailer', config('mail.default', 'smtp')));
            $host = $request->input('mail_host', Setting::get('mail_host', config('mail.mailers.smtp.host', '127.0.0.1')));
            $port = $request->input('mail_port', Setting::get('mail_port', config('mail.mailers.smtp.port', 587)));
            $username = $request->filled('mail_username') ? $request->input('mail_username') : Setting::get('mail_username', config('mail.mailers.smtp.username'));
            $password = $request->filled('mail_password') ? $request->input('mail_password') : Setting::get('mail_password', config('mail.mailers.smtp.password'));
            $encryption = $request->has('mail_encryption') ? $request->input('mail_encryption') : Setting::get('mail_encryption', config('mail.mailers.smtp.encryption'));
            $fromAddress = $request->input('mail_from_address', Setting::get('mail_from_address', Setting::get('store_email', config('mail.from.address', 'sales@skmsaller.com'))));
            $fromName = $request->input('mail_from_name', Setting::get('mail_from_name', Setting::get('store_name', config('mail.from.name', 'SKM Saller'))));

            $enc = in_array(strtolower((string)$encryption), ['null', 'none', ''], true) ? null : strtolower((string)$encryption);

            config([
                'mail.default' => $mailer ?: 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => (int) $port,
                'mail.mailers.smtp.username' => $username,
                'mail.mailers.smtp.password' => $password,
                'mail.mailers.smtp.encryption' => $enc,
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
            ]);

            if ($enc === 'ssl') {
                config(['mail.mailers.smtp.scheme' => 'smtps']);
            }

            // Purge cached mailers so Symfony Mailer reconnects with latest parameters
            \Illuminate\Support\Facades\Mail::purge('smtp');

            $testEmail = $request->input('test_email');
            $storeName = $fromName ?: 'SKM Saller';

            \Illuminate\Support\Facades\Mail::raw(
                "Hello,\n\nThis is a test email sent from the {$storeName} Admin Panel to verify that your SMTP mail settings are working properly.\n\n" .
                "--- SMTP Diagnostics ---\n" .
                "Server Time: " . now()->format('Y-m-d H:i:s T') . "\n" .
                "Mailer: " . $mailer . "\n" .
                "SMTP Host: " . $host . "\n" .
                "SMTP Port: " . $port . "\n" .
                "Encryption: " . ($enc ?: 'None') . "\n" .
                "From: " . $fromAddress . " (" . $fromName . ")\n" .
                "To: " . $testEmail . "\n\n" .
                "If you received this message, your SMTP credentials and connection are properly configured.\n\n" .
                "Best Regards,\n" . $storeName . " Team",
                function ($message) use ($testEmail, $fromAddress, $fromName, $storeName) {
                    $message->to($testEmail)
                            ->from($fromAddress, $fromName)
                            ->subject("{$storeName} SMTP Test Email - Connection Successful!");
                }
            );

            return response()->json([
                'success' => true,
                'message' => "Success! Test email was successfully sent to {$testEmail}. Please check your inbox and spam folder."
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SMTP Test Email Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Safely synchronize key-value pairs into base .env file.
     */
    protected function updateEnvFile(array $data): void
    {
        $envPath = base_path('.env');
        if (!File::exists($envPath) || !is_writable($envPath)) {
            return;
        }

        $envContent = File::get($envPath);

        foreach ($data as $key => $val) {
            $formattedVal = $val;
            if ($val === null || $val === 'null' || $val === '') {
                $formattedVal = 'null';
            } elseif (str_contains($val, ' ') || str_contains($val, '#')) {
                $formattedVal = '"' . addcslashes($val, '"') . '"';
            }

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$formattedVal}", $envContent);
            } else {
                $envContent .= "\n{$key}={$formattedVal}";
            }
        }

        File::put($envPath, $envContent);
    }
}
