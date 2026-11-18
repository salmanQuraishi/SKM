<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Dynamically configure Mail & SMTP settings from database
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $mailMailer = \App\Models\Setting::get('mail_mailer');
                if ($mailMailer) {
                    config(['mail.default' => $mailMailer]);
                }

                $mailHost = \App\Models\Setting::get('mail_host');
                if ($mailHost) {
                    config(['mail.mailers.smtp.host' => $mailHost]);
                }

                $mailPort = \App\Models\Setting::get('mail_port');
                if ($mailPort) {
                    config(['mail.mailers.smtp.port' => (int) $mailPort]);
                }

                $mailUsername = \App\Models\Setting::get('mail_username');
                if ($mailUsername !== null && $mailUsername !== '') {
                    config(['mail.mailers.smtp.username' => $mailUsername]);
                }

                $mailPassword = \App\Models\Setting::get('mail_password');
                if ($mailPassword !== null && $mailPassword !== '') {
                    config(['mail.mailers.smtp.password' => $mailPassword]);
                }

                $mailEncryption = \App\Models\Setting::get('mail_encryption');
                if ($mailEncryption !== null) {
                    $enc = in_array(strtolower($mailEncryption), ['null', 'none', ''], true) ? null : strtolower($mailEncryption);
                    config(['mail.mailers.smtp.encryption' => $enc]);
                    if ($enc === 'ssl') {
                        config(['mail.mailers.smtp.scheme' => 'smtps']);
                    }
                }

                $fromAddress = \App\Models\Setting::get('mail_from_address', \App\Models\Setting::get('store_email'));
                if ($fromAddress) {
                    config(['mail.from.address' => $fromAddress]);
                }

                $fromName = \App\Models\Setting::get('mail_from_name', \App\Models\Setting::get('store_name', 'SKM Saller'));
                if ($fromName) {
                    config(['mail.from.name' => $fromName]);
                }
            }
        } catch (\Throwable $e) {
            // Silently proceed if database not yet migrated
        }

        // Auto-clear lightweight navigation & AJAX brand hierarchy caches when catalog changes
        $clearNavCaches = function ($model = null) {
            \Illuminate\Support\Facades\Cache::forget('nav_global_categories');
            \Illuminate\Support\Facades\Cache::forget('nav_header_categories');
            \Illuminate\Support\Facades\Cache::forget('nav_global_brands');
            \Illuminate\Support\Facades\Cache::forget('nav_global_models');
            \Illuminate\Support\Facades\Cache::forget('ajax_active_brands_list');

            if ($model instanceof \App\Models\Brand && $model->slug) {
                \Illuminate\Support\Facades\Cache::forget('brand_hierarchy_' . $model->slug);
            } elseif ($model instanceof \App\Models\CarModel && $model->brand) {
                \Illuminate\Support\Facades\Cache::forget('brand_hierarchy_' . $model->brand->slug);
            } elseif ($model instanceof \App\Models\ModelVariant && $model->carModel && $model->carModel->brand) {
                \Illuminate\Support\Facades\Cache::forget('brand_hierarchy_' . $model->carModel->brand->slug);
            }
        };

        \App\Models\Brand::saved($clearNavCaches);
        \App\Models\Brand::deleted($clearNavCaches);
        \App\Models\CarModel::saved($clearNavCaches);
        \App\Models\CarModel::deleted($clearNavCaches);
        \App\Models\ModelVariant::saved($clearNavCaches);
        \App\Models\ModelVariant::deleted($clearNavCaches);
        \App\Models\Category::saved($clearNavCaches);
        \App\Models\Category::deleted($clearNavCaches);

        \Illuminate\Support\Facades\View::composer(['layouts.app', 'frontend.*', 'errors.*'], function ($view) {
            if (!isset($view->getData()['globalCategories'])) {
                $globalCategories = \Illuminate\Support\Facades\Cache::remember('nav_global_categories', 1800, function () {
                    return \App\Models\Category::active()->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
                });

                $headerCategories = \Illuminate\Support\Facades\Cache::remember('nav_header_categories', 1800, function () use ($globalCategories) {
                    $cats = \App\Models\Category::active()
                        ->where('is_featured', true)
                        ->orderBy('sort_order')
                        ->get();

                    return $cats->isEmpty() ? $globalCategories->take(6) : $cats;
                });

                $globalBrands = \Illuminate\Support\Facades\Cache::remember('nav_global_brands', 1800, function () {
                    return \App\Models\Brand::active()
                        ->withCount(['carModels' => function ($q) {
                            $q->active();
                        }])
                        ->orderBy('sort_order')
                        ->get();
                });

                $globalModels = \Illuminate\Support\Facades\Cache::remember('nav_global_models', 1800, function () {
                    return \App\Models\CarModel::active()->popular()->with('brand')->orderBy('sort_order')->take(10)->get();
                });

                $cartCount = $view->getData()['globalCartCount'] ?? \Illuminate\Support\Facades\View::shared('globalCartCount');
                if ($cartCount === null) {
                    try {
                        $cartCount = app(\App\Services\CartService::class)->getItemCount();
                    } catch (\Throwable $e) {
                        $cartCount = 0;
                    }
                    \Illuminate\Support\Facades\View::share('globalCartCount', $cartCount);
                }

                $wishlistCount = $view->getData()['globalWishlistCount'] ?? \Illuminate\Support\Facades\View::shared('globalWishlistCount');
                $wishlistProductIds = $view->getData()['globalWishlistProductIds'] ?? \Illuminate\Support\Facades\View::shared('globalWishlistProductIds');
                if ($wishlistCount === null || $wishlistProductIds === null) {
                    try {
                        $wishlistService = app(\App\Services\WishlistService::class);
                        if ($wishlistCount === null) {
                            $wishlistCount = $wishlistService->getItemCount();
                        }
                        if ($wishlistProductIds === null) {
                            $wishlistProductIds = $wishlistService->getProductIds();
                        }
                    } catch (\Throwable $e) {
                        $wishlistCount = $wishlistCount ?? 0;
                        $wishlistProductIds = $wishlistProductIds ?? [];
                    }
                    \Illuminate\Support\Facades\View::share('globalWishlistCount', $wishlistCount);
                    \Illuminate\Support\Facades\View::share('globalWishlistProductIds', $wishlistProductIds);
                }

                $view->with([
                    'globalCategories' => $globalCategories,
                    'headerCategories' => $headerCategories,
                    'globalBrands' => $globalBrands,
                    'globalModels' => $globalModels,
                    'globalCartCount' => $cartCount,
                    'globalWishlistCount' => $wishlistCount,
                    'globalWishlistProductIds' => $wishlistProductIds,
                    'globalSettings' => \App\Models\Setting::getAllGrouped(),
                    'errors' => $view->getData()['errors'] ?? new \Illuminate\Support\ViewErrorBag(),
                ]);
            }
        });
    }
}
