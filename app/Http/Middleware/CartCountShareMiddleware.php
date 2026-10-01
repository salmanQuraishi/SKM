<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class CartCountShareMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Calculate cart and wishlist counts using CartService
        $cartCount = 0;
        try {
            $cartCount = app(\App\Services\CartService::class)->getItemCount();
        } catch (\Throwable $e) {
            $cartCount = 0;
        }

        $wishlistCount = 0;
        $wishlistProductIds = [];
        try {
            $wishlistService = app(\App\Services\WishlistService::class);
            $wishlistCount = $wishlistService->getItemCount();
            $wishlistProductIds = $wishlistService->getProductIds();
        } catch (\Throwable $e) {
            $wishlistCount = 0;
            $wishlistProductIds = [];
        }

        View::share([
            'globalCartCount' => $cartCount,
            'globalWishlistCount' => $wishlistCount,
            'globalWishlistProductIds' => $wishlistProductIds,
        ]);

        return $next($request);
    }
}
