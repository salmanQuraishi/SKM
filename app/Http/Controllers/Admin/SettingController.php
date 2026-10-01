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

        Cache::forget('all_settings');

        return redirect()->route('admin.settings.index')->with('success', 'Store and company settings updated successfully!');
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

            $msg = 'System cache cleared successfully!';

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
}
