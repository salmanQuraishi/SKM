<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewModerationController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->latest('id')->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $review = Review::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,approved,rejected']);

        $review->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Review status updated.');
    }

    public function destroy(int $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted.');
    }
}
