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
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (!isset($view->getData()['globalCategories'])) {
                $globalCategories = \App\Models\Category::active()->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();
                $globalBrands = \App\Models\Brand::active()
                    ->with([
                        'carModels' => function ($q) {
                            $q->active()->orderBy('sort_order')->with([
                                'variants' => function ($vq) {
                                    $vq->active()->orderBy('sort_order');
                                }
                            ]);
                        }
                    ])
                    ->orderBy('sort_order')
                    ->get();
                $globalModels = \App\Models\CarModel::active()->popular()->with('brand')->orderBy('sort_order')->take(10)->get();

                $cartCount = $view->getData()['globalCartCount'] ?? \Illuminate\Support\Facades\View::shared('globalCartCount');
                if ($cartCount === null) {
                    try {
                        $cartCount = app(\App\Services\CartService::class)->getItemCount();
                    } catch (\Throwable $e) {
                        $cartCount = 0;
                    }
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
                }

                $view->with([
                    'globalCategories' => $globalCategories,
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
