<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        $order = null;
        $orderNumber = $request->get('order_number');
        $phone = $request->get('phone');

        if ($request->filled('order_number') && ($request->filled('phone') || $request->filled('email'))) {
            $query = Order::where('order_number', trim($request->order_number));

            if ($request->filled('phone')) {
                $query->where('customer_phone', trim($request->phone));
            } elseif ($request->filled('email')) {
                $query->where('customer_email', trim($request->email));
            }

            $order = $query->with(['items.product', 'statusHistory', 'shipment'])->first();

            if (!$order) {
                session()->flash('error', 'No order found with the provided details. Please check your Order Number and Mobile/Email.');
            }
        }

        return view('frontend.track_order', compact('order', 'orderNumber', 'phone'));
    }
}
