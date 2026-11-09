<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Parse date range from request or standard preset
     */
    protected function parseDateRange(Request $request): array
    {
        $preset = $request->get('preset', 'this_month');

        if ($preset === 'today') {
            $from = Carbon::today()->toDateString();
            $to = Carbon::today()->toDateString();
        } elseif ($preset === 'yesterday') {
            $from = Carbon::yesterday()->toDateString();
            $to = Carbon::yesterday()->toDateString();
        } elseif ($preset === 'this_week') {
            $from = Carbon::now()->startOfWeek()->toDateString();
            $to = Carbon::now()->endOfWeek()->toDateString();
        } elseif ($preset === 'last_month') {
            $from = Carbon::now()->subMonth()->startOfMonth()->toDateString();
            $to = Carbon::now()->subMonth()->endOfMonth()->toDateString();
        } elseif ($preset === 'this_year') {
            $from = Carbon::now()->startOfYear()->toDateString();
            $to = Carbon::now()->endOfYear()->toDateString();
        } elseif ($preset === 'custom' && $request->filled('date_from') && $request->filled('date_to')) {
            $from = $request->get('date_from');
            $to = $request->get('date_to');
        } else {
            // Default: this month
            $preset = 'this_month';
            $from = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
            $to = $request->get('date_to', Carbon::now()->toDateString());
        }

        return [$from, $to, $preset];
    }

    /**
     * 1. Business Report
     */
    public function business(Request $request)
    {
        [$dateFrom, $dateTo, $preset] = $this->parseDateRange($request);

        $ordersQuery = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        $totalOrdersCount = (clone $ordersQuery)->count();
        $grossSales = (clone $ordersQuery)->sum('total_amount');
        $paidRevenue = (clone $ordersQuery)->where('payment_status', 'paid')->sum('total_amount');
        $pendingRevenue = (clone $ordersQuery)->where('payment_status', '!=', 'paid')->sum('total_amount');

        $paidOrdersCount = (clone $ordersQuery)->where('payment_status', 'paid')->count();
        $deliveredOrdersCount = (clone $ordersQuery)->where('order_status', 'delivered')->count();
        $cancelledOrdersCount = (clone $ordersQuery)->where('order_status', 'cancelled')->count();

        $aov = $paidOrdersCount > 0 ? ($paidRevenue / $paidOrdersCount) : 0;
        $totalDiscount = (clone $ordersQuery)->sum('discount_amount');
        $totalTax = (clone $ordersQuery)->sum('tax_amount');
        $totalShipping = (clone $ordersQuery)->sum('shipping_amount');

        $onlineRevenue = (clone $ordersQuery)->where('payment_method', 'razorpay')->where('payment_status', 'paid')->sum('total_amount');
        $codRevenue = (clone $ordersQuery)->where('payment_method', 'cod')->sum('total_amount');

        // Top 10 Selling Products in period
        $topProducts = OrderItem::whereHas('order', function ($q) use ($dateFrom, $dateTo) {
            $q->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo)
              ->where('payment_status', 'paid');
        })
        ->select('product_id', 'product_name', 'product_sku', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(COALESCE(total, quantity * price)) as total_sales'))
        ->groupBy('product_id', 'product_name', 'product_sku')
        ->orderByDesc('total_qty')
        ->take(10)
        ->get();

        // Paginated Orders for details table
        $orders = (clone $ordersQuery)->with(['items', 'user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.business', compact(
            'dateFrom',
            'dateTo',
            'preset',
            'totalOrdersCount',
            'grossSales',
            'paidRevenue',
            'pendingRevenue',
            'paidOrdersCount',
            'deliveredOrdersCount',
            'cancelledOrdersCount',
            'aov',
            'totalDiscount',
            'totalTax',
            'totalShipping',
            'onlineRevenue',
            'codRevenue',
            'topProducts',
            'orders'
        ));
    }

    /**
     * Backward-compatibility for existing sales report route
     */
    public function sales(Request $request)
    {
        return $this->business($request);
    }

    /**
     * 2. Inventory Report
     */
    public function inventory(Request $request)
    {
        $status = $request->get('status', 'all');
        $brandId = $request->get('brand_id');
        $categoryId = $request->get('category_id');
        $search = $request->get('q');

        // Core KPIs
        $totalProducts = Product::count();
        $totalStockUnits = (int) Product::sum('stock_quantity');

        // Valuations
        $totalCostValuation = (float) (Product::selectRaw('SUM(stock_quantity * COALESCE(cost_price, regular_price * 0.6)) as val')->value('val') ?? 0);
        $totalRetailValuation = (float) (Product::selectRaw('SUM(stock_quantity * COALESCE(sale_price, regular_price)) as val')->value('val') ?? 0);
        $potentialMargin = max(0, $totalRetailValuation - $totalCostValuation);

        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();
        $lowStockCount = Product::whereBetween('stock_quantity', [1, 5])->count();
        $inStockCount = Product::where('stock_quantity', '>', 5)->count();

        // Query Products for table
        $productsQuery = Product::with(['brand', 'primaryCategory', 'images']);

        if ($status === 'out_of_stock') {
            $productsQuery->where('stock_quantity', '<=', 0);
        } elseif ($status === 'low_stock') {
            $productsQuery->whereBetween('stock_quantity', [1, 5]);
        } elseif ($status === 'in_stock') {
            $productsQuery->where('stock_quantity', '>', 5);
        }

        if ($brandId) {
            $productsQuery->where('brand_id', $brandId);
        }

        if ($categoryId) {
            $productsQuery->where('primary_category_id', $categoryId);
        }

        if ($search) {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('hsn_sac', 'like', "%{$search}%");
            });
        }

        $products = $productsQuery->orderBy('stock_quantity', 'asc')
            ->paginate(20)
            ->withQueryString();

        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.reports.inventory', compact(
            'status',
            'brandId',
            'categoryId',
            'search',
            'totalProducts',
            'totalStockUnits',
            'totalCostValuation',
            'totalRetailValuation',
            'potentialMargin',
            'outOfStockCount',
            'lowStockCount',
            'inStockCount',
            'products',
            'brands',
            'categories'
        ));
    }

    /**
     * 3. Tax Report (GST)
     */
    public function tax(Request $request)
    {
        [$dateFrom, $dateTo, $preset] = $this->parseDateRange($request);

        $ordersQuery = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid');

        $totalPaidOrders = (clone $ordersQuery)->count();
        $totalInvoicedTurnover = (clone $ordersQuery)->sum('total_amount');
        $totalTaxCollected = (clone $ordersQuery)->sum('tax_amount');
        $totalTaxableSubtotal = (clone $ordersQuery)->sum('subtotal');

        $cgstTotal = $totalTaxCollected / 2;
        $sgstTotal = $totalTaxCollected / 2;

        // Group order items by product HSN code
        $hsnSummary = OrderItem::whereHas('order', function ($q) use ($dateFrom, $dateTo) {
            $q->whereDate('created_at', '>=', $dateFrom)
              ->whereDate('created_at', '<=', $dateTo)
              ->where('payment_status', 'paid');
        })
        ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
        ->select(
            DB::raw("COALESCE(products.hsn_sac, '8708') as hsn_code"),
            DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
            DB::raw('SUM(order_items.quantity) as total_units'),
            DB::raw('SUM(COALESCE(order_items.total, order_items.quantity * order_items.price)) as taxable_amount'),
            DB::raw('AVG(COALESCE(products.tax_rate, 18.00)) as avg_tax_rate')
        )
        ->groupBy('hsn_code')
        ->orderByDesc('taxable_amount')
        ->get();

        // Paginated invoices list
        $orders = (clone $ordersQuery)->with(['user', 'receipt'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.tax', compact(
            'dateFrom',
            'dateTo',
            'preset',
            'totalPaidOrders',
            'totalInvoicedTurnover',
            'totalTaxCollected',
            'totalTaxableSubtotal',
            'cgstTotal',
            'sgstTotal',
            'hsnSummary',
            'orders'
        ));
    }

    /**
     * 4. Payment Report
     */
    public function payment(Request $request)
    {
        [$dateFrom, $dateTo, $preset] = $this->parseDateRange($request);

        $ordersQuery = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        $totalOrdersCount = (clone $ordersQuery)->count();
        $totalCollected = (clone $ordersQuery)->where('payment_status', 'paid')->sum('total_amount');
        $pendingAmount = (clone $ordersQuery)->where('payment_status', 'pending')->sum('total_amount');
        $failedAmount = (clone $ordersQuery)->where('payment_status', 'failed')->sum('total_amount');

        // Razorpay Breakdown
        $razorpayOrdersCount = (clone $ordersQuery)->where('payment_method', 'razorpay')->count();
        $razorpayPaidCount = (clone $ordersQuery)->where('payment_method', 'razorpay')->where('payment_status', 'paid')->count();
        $razorpayCollected = (clone $ordersQuery)->where('payment_method', 'razorpay')->where('payment_status', 'paid')->sum('total_amount');

        // COD Breakdown
        $codOrdersCount = (clone $ordersQuery)->where('payment_method', 'cod')->count();
        $codDeliveredCount = (clone $ordersQuery)->where('payment_method', 'cod')->where('order_status', 'delivered')->count();
        $codCollected = (clone $ordersQuery)->where('payment_method', 'cod')->where('payment_status', 'paid')->sum('total_amount');
        $codPendingCollection = (clone $ordersQuery)->where('payment_method', 'cod')->where('payment_status', '!=', 'paid')->sum('total_amount');

        // Ledger of recent payments
        $orders = (clone $ordersQuery)->with(['payments', 'user', 'receipt'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.payment', compact(
            'dateFrom',
            'dateTo',
            'preset',
            'totalOrdersCount',
            'totalCollected',
            'pendingAmount',
            'failedAmount',
            'razorpayOrdersCount',
            'razorpayPaidCount',
            'razorpayCollected',
            'codOrdersCount',
            'codDeliveredCount',
            'codCollected',
            'codPendingCollection',
            'orders'
        ));
    }

    /**
     * Export Business Report CSV
     */
    public function exportBusinessCsv(Request $request): StreamedResponse
    {
        [$dateFrom, $dateTo] = $this->parseDateRange($request);

        $orders = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->with(['items', 'user'])
            ->get();

        $filename = "Business_Report_{$dateFrom}_to_{$dateTo}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Order Number',
                'Date',
                'Customer Name',
                'Mobile',
                'Items Count',
                'Subtotal (INR)',
                'Discount (INR)',
                'Tax (INR)',
                'Shipping (INR)',
                'Total Amount (INR)',
                'Payment Method',
                'Payment Status',
                'Order Status',
            ]);

            foreach ($orders as $o) {
                fputcsv($handle, [
                    $o->order_number,
                    $o->created_at->format('Y-m-d H:i'),
                    $o->customer_name,
                    $o->customer_phone,
                    $o->items->sum('quantity'),
                    $o->subtotal,
                    $o->discount_amount,
                    $o->tax_amount,
                    $o->shipping_amount,
                    $o->total_amount,
                    strtoupper($o->payment_method),
                    strtoupper($o->payment_status),
                    strtoupper($o->order_status),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Inventory Report CSV
     */
    public function exportInventoryCsv(Request $request): StreamedResponse
    {
        $status = $request->get('status', 'all');
        $query = Product::with(['brand', 'primaryCategory']);

        if ($status === 'out_of_stock') {
            $query->where('stock_quantity', '<=', 0);
        } elseif ($status === 'low_stock') {
            $query->whereBetween('stock_quantity', [1, 5]);
        } elseif ($status === 'in_stock') {
            $query->where('stock_quantity', '>', 5);
        }

        $products = $query->orderBy('stock_quantity', 'asc')->get();
        $date = Carbon::now()->format('Y-m-d');
        $filename = "Inventory_Report_{$status}_{$date}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Product Name',
                'SKU',
                'HSN Code',
                'Brand',
                'Category',
                'Cost Price (INR)',
                'Sale Price (INR)',
                'Regular MRP (INR)',
                'Current Stock',
                'Min Stock Alert',
                'Inventory Cost Value',
                'Inventory Retail Value',
                'Stock Status',
            ]);

            foreach ($products as $p) {
                $statusText = $p->stock_quantity <= 0 ? 'OUT OF STOCK' : ($p->stock_quantity <= 5 ? 'LOW STOCK' : 'IN STOCK');
                $price = $p->sale_price ?: $p->regular_price;
                $cost = $p->cost_price ?: ($p->regular_price * 0.6);

                fputcsv($handle, [
                    $p->name,
                    $p->sku,
                    $p->hsn_sac ?: '8708',
                    $p->brand->name ?? 'Universal',
                    $p->primaryCategory->name ?? 'General',
                    $p->cost_price ?? 0,
                    $p->sale_price ?? $p->regular_price,
                    $p->regular_price,
                    $p->stock_quantity,
                    $p->min_stock_alert,
                    round($p->stock_quantity * $cost, 2),
                    round($p->stock_quantity * $price, 2),
                    $statusText,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Tax Report CSV
     */
    public function exportTaxCsv(Request $request): StreamedResponse
    {
        [$dateFrom, $dateTo] = $this->parseDateRange($request);

        $orders = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid')
            ->with(['items.product', 'receipt'])
            ->get();

        $filename = "Tax_Report_GST_{$dateFrom}_to_{$dateTo}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Invoice / Receipt #',
                'Order Number',
                'Invoice Date',
                'Customer Name',
                'Shipping State',
                'Taxable Subtotal (INR)',
                'CGST (9%)',
                'SGST (9%)',
                'Total Tax (18%)',
                'Invoice Total (INR)',
                'Payment Mode',
            ]);

            foreach ($orders as $o) {
                $receiptNo = $o->receipt->receipt_number ?? "REC-{$o->id}";
                $tax = (float) $o->tax_amount;
                $cgst = round($tax / 2, 2);
                $sgst = round($tax / 2, 2);

                fputcsv($handle, [
                    $receiptNo,
                    $o->order_number,
                    $o->created_at->format('Y-m-d'),
                    $o->customer_name,
                    $o->shipping_state ?? 'Delhi NCR',
                    $o->subtotal,
                    $cgst,
                    $sgst,
                    $tax,
                    $o->total_amount,
                    strtoupper($o->payment_method),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Payment Report CSV
     */
    public function exportPaymentCsv(Request $request): StreamedResponse
    {
        [$dateFrom, $dateTo] = $this->parseDateRange($request);

        $orders = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->with(['payments', 'user'])
            ->get();

        $filename = "Payment_Report_{$dateFrom}_to_{$dateTo}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Transaction Date',
                'Order Number',
                'Customer Name',
                'Mobile',
                'Gateway / Mode',
                'Razorpay Payment ID / Txn ID',
                'Amount (INR)',
                'Payment Status',
                'Order Status',
            ]);

            foreach ($orders as $o) {
                $payment = $o->payments->first();
                $txnId = $payment->razorpay_payment_id ?? ($payment->transaction_id ?? 'N/A');

                fputcsv($handle, [
                    $o->created_at->format('Y-m-d H:i:s'),
                    $o->order_number,
                    $o->customer_name,
                    $o->customer_phone,
                    strtoupper($o->payment_method),
                    $txnId,
                    $o->total_amount,
                    strtoupper($o->payment_status),
                    strtoupper($o->order_status),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Backward-compatibility for exportSalesCsv
     */
    public function exportSalesCsv(Request $request): StreamedResponse
    {
        return $this->exportBusinessCsv($request);
    }
}
