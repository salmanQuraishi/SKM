<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->toDateString());

        $orders = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->with(['items', 'user'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalRevenue = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalOrdersCount = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();

        $codRevenue = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_method', 'cod')
            ->sum('total_amount');

        $onlineRevenue = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_method', 'razorpay')
            ->sum('total_amount');

        return view('admin.reports.sales', compact(
            'orders',
            'dateFrom',
            'dateTo',
            'totalRevenue',
            'totalOrdersCount',
            'codRevenue',
            'onlineRevenue'
        ));
    }

    public function exportSalesCsv(Request $request): StreamedResponse
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->toDateString());

        $orders = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->with('items')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=Sales_Report_{$dateFrom}_to_{$dateTo}.csv",
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Order Number',
                'Date',
                'Customer Name',
                'Customer Mobile',
                'Items Count',
                'Subtotal',
                'Discount',
                'Tax',
                'Shipping',
                'COD Charge',
                'Total Amount',
                'Payment Method',
                'Payment Status',
                'Order Status',
            ]);

            foreach ($orders as $o) {
                fputcsv($handle, [
                    $o->order_number,
                    $o->created_at->format('Y-m-d H:i:s'),
                    $o->customer_name,
                    $o->customer_phone,
                    $o->items->sum('quantity'),
                    $o->subtotal,
                    $o->discount_amount,
                    $o->tax_amount,
                    $o->shipping_amount,
                    $o->cod_charge,
                    $o->total_amount,
                    strtoupper($o->payment_method),
                    strtoupper($o->payment_status),
                    strtoupper($o->order_status),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
