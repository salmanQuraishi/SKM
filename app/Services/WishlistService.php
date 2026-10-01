<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WishlistService
{
    public function getWishlist(): Wishlist
    {
        if (Auth::check()) {
            $wishlist = Wishlist::firstOrCreate(['user_id' => Auth::id()]);

            // Merge guest wishlist if exists
            $sessionId = Session::getId();
            $guestWishlist = Wishlist::where('session_id', $sessionId)->whereNull('user_id')->first();
            if ($guestWishlist) {
                foreach ($guestWishlist->items as $item) {
                    $wishlist->items()->firstOrCreate([
                        'product_id' => $item->product_id,
                    ]);
                }
                $guestWishlist->items()->delete();
                $guestWishlist->delete();
            }

            return $wishlist;
        }

        $sessionId = Session::getId();
        return Wishlist::firstOrCreate(['session_id' => $sessionId]);
    }

    public function getItemCount(): int
    {
        $wishlist = $this->getWishlist();
        return $wishlist->items()->count();
    }

    public function getProductIds(): array
    {
        $wishlist = $this->getWishlist();
        return $wishlist->items()->pluck('product_id')->toArray();
    }

    public function toggle(int $productId): array
    {
        $product = Product::active()->findOrFail($productId);
        $wishlist = $this->getWishlist();

        $existing = $wishlist->items()->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            $added = false;
            $message = "Removed {$product->name} from your Wishlist.";
        } else {
            $wishlist->items()->create(['product_id' => $productId]);
            $added = true;
            $message = "Added {$product->name} to your Wishlist!";
        }

        $count = $wishlist->items()->count();

        return [
            'success' => true,
            'added' => $added,
            'message' => $message,
            'wishlist_count' => $count,
        ];
    }

    public function moveToCart(int $productId, CartService $cartService): array
    {
        $wishlist = $this->getWishlist();
        $wishlist->items()->where('product_id', $productId)->delete();

        return $cartService->addItem($productId, null, 1);
    }
}
