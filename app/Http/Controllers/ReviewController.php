<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, int $productId)
    {
        $product = Product::active()->findOrFail($productId);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'nullable|email|max:150',
            'title' => 'nullable|string|max:150',
            'comment' => 'required|string|max:1000',
        ]);

        $isVerified = false;
        if (Auth::check()) {
            $user = Auth::user();
            // Check if user previously bought this product
            $isVerified = Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->whereHas('items', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->exists();
        }

        Review::create([
            'product_id' => $productId,
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_verified_purchase' => $isVerified,
            'status' => 'approved', // Auto-approved or moderated
        ]);

        return redirect()->back()->with('success', 'Thank you for your valuable review!');
    }
}
