<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            // If there's a guest cart in session, merge it
            $sessionId = Session::getId();
            $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
            if ($guestCart) {
                foreach ($guestCart->items as $item) {
                    $existing = $cart->items()
                        ->where('product_id', $item->product_id)
                        ->where('variant_id', $item->variant_id)
                        ->first();

                    if ($existing) {
                        $existing->increment('quantity', $item->quantity);
                    } else {
                        $cart->items()->create([
                            'product_id' => $item->product_id,
                            'variant_id' => $item->variant_id,
                            'quantity' => $item->quantity,
                        ]);
                    }
                }
                $guestCart->items()->delete();
                $guestCart->delete();
            }
            return $cart->load(['items.product.images', 'items.variant']);
        }

        $sessionId = Session::getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        return $cart->load(['items.product.images', 'items.variant']);
    }

    public function addItem(int $productId, ?int $variantId = null, int $quantity = 1): array
    {
        $product = Product::active()->findOrFail($productId);

        if ($variantId) {
            $variant = ProductVariant::where('product_id', $productId)->where('status', true)->findOrFail($variantId);
            $availableStock = $variant->stock_quantity;
        } else {
            $availableStock = $product->stock_quantity;
        }

        if ($availableStock < $quantity) {
            return [
                'success' => false,
                'message' => "Only {$availableStock} unit(s) available in stock."
            ];
        }

        $cart = $this->getCart();

        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($newQuantity > $availableStock) {
                return [
                    'success' => false,
                    'message' => "Cannot add more. You already have {$cartItem->quantity} in your cart, max stock is {$availableStock}."
                ];
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        return [
            'success' => true,
            'message' => "{$product->name} added to your cart!",
            'cart_count' => $this->getItemCount(),
            'cart_total' => $this->getCartTotals()['total'],
        ];
    }

    public function updateQuantity(int $itemId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        $cart = $this->getCart();
        $item = $cart->items()->with(['product', 'variant'])->findOrFail($itemId);

        $stock = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
        if ($quantity > $stock) {
            return [
                'success' => false,
                'message' => "Only {$stock} unit(s) available."
            ];
        }

        $item->update(['quantity' => $quantity]);

        return [
            'success' => true,
            'message' => 'Cart updated successfully.',
            'item_total' => $item->total_price,
            'totals' => $this->getCartTotals(),
        ];
    }

    public function removeItem(int $itemId): array
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $itemId)->delete();

        return [
            'success' => true,
            'message' => 'Item removed from cart.',
            'totals' => $this->getCartTotals(),
            'cart_count' => $this->getItemCount(),
        ];
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        $cart->update(['coupon_code' => null]);
    }

    public function applyCoupon(string $code): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }

        $cart = $this->getCart();
        $subtotal = $cart->subtotal;

        $validation = $coupon->isValidForSubtotal($subtotal, Auth::id());
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        $cart->update(['coupon_code' => $coupon->code]);

        return [
            'success' => true,
            'message' => 'Coupon code applied successfully!',
            'totals' => $this->getCartTotals(),
        ];
    }

    public function removeCoupon(): array
    {
        $cart = $this->getCart();
        $cart->update(['coupon_code' => null]);

        return [
            'success' => true,
            'message' => 'Coupon removed.',
            'totals' => $this->getCartTotals(),
        ];
    }

    public function getItemCount(): int
    {
        $cart = $this->getCart();
        return (int) $cart->items()->sum('quantity');
    }

    public function getCartTotals(string $paymentMethod = 'online'): array
    {
        $cart = $this->getCart();
        $items = $cart->items()->with(['product', 'variant'])->get();

        $subtotal = 0.0;
        foreach ($items as $item) {
            $price = $item->variant ? (float) $item->variant->active_price : (float) $item->product->active_price;
            $subtotal += ($price * $item->quantity);
        }

        $discount = 0.0;
        $appliedCoupon = null;
        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon && $coupon->isValidForSubtotal($subtotal, Auth::id())['valid']) {
                $discount = $coupon->calculateDiscount($subtotal);
                $appliedCoupon = $coupon;
            } else {
                $cart->update(['coupon_code' => null]);
            }
        }

        $discountedSubtotal = max(0, $subtotal - $discount);

        // Shipping calculation from settings
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 999);
        $standardShippingCharge = (float) Setting::get('shipping_charge', 99);

        $shipping = ($subtotal >= $freeShippingThreshold || $subtotal == 0) ? 0.0 : $standardShippingCharge;

        // COD charge if payment method is COD
        $codCharge = 0.0;
        if ($paymentMethod === 'cod') {
            $codCharge = (float) Setting::get('cod_charge', 50);
        }

        // GST is inclusive or calculated based on products (typically standard 18% included in prices or applied)
        // In automotive e-commerce MRP prices are GST inclusive
        $tax = round(($discountedSubtotal * 0.18) / 1.18, 2);

        $grandTotal = round($discountedSubtotal + $shipping + $codCharge, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'coupon' => $appliedCoupon ? $appliedCoupon->code : null,
            'tax' => $tax,
            'shipping' => round($shipping, 2),
            'cod_charge' => round($codCharge, 2),
            'total' => $grandTotal,
            'items_count' => $items->sum('quantity'),
            'free_shipping_eligible' => $subtotal >= $freeShippingThreshold,
            'free_shipping_remaining' => max(0, $freeShippingThreshold - $subtotal),
        ];
    }
}
