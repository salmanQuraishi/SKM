<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCart();
        $totals = $this->cartService->getCartTotals();

        return view('frontend.cart', compact('cart', 'totals'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $productId = (int) $request->product_id;
        $variantId = $request->variant_id ? (int) $request->variant_id : null;
        $quantity = (int) ($request->quantity ?? 1);

        $result = $this->cartService->addItem($productId, $variantId, $quantity);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            if ($request->has('buy_now')) {
                return redirect()->route('checkout');
            }
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $result = $this->cartService->updateQuantity((int) $request->item_id, (int) $request->quantity);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('cart.index')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function remove(Request $request, int $itemId)
    {
        $result = $this->cartService->removeItem($itemId);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('cart.index')->with('success', $result['message']);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $result = $this->cartService->applyCoupon($request->code);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function removeCoupon(Request $request)
    {
        $result = $this->cartService->removeCoupon();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function getSummary(Request $request)
    {
        $paymentMethod = $request->get('payment_method', 'online');
        $totals = $this->cartService->getCartTotals($paymentMethod);
        $cartCount = $this->cartService->getItemCount();

        return response()->json([
            'totals' => $totals,
            'cart_count' => $cartCount,
        ]);
    }
}
