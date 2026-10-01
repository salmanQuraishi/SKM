<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index()
    {
        $wishlist = $this->wishlistService->getWishlist();
        $items = $wishlist->items()->with('product.images')->latest()->paginate(12);

        return view('frontend.wishlist', compact('wishlist', 'items'));
    }

    public function toggle(Request $request, int $productId)
    {
        $result = $this->wishlistService->toggle($productId);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function moveToCart(Request $request, int $productId, CartService $cartService)
    {
        $this->wishlistService->moveToCart($productId, $cartService);

        return redirect()->route('cart.index')->with('success', 'Product moved to cart!');
    }
}
