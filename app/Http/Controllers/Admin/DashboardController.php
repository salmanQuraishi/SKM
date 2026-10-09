<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\ModelVariant;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Metrics
        $totalSales = Order::where('payment_status', 'paid')->sum('total_amount');
        $todaySales = Order::where('payment_status', 'paid')->whereDate('created_at', $today)->sum('total_amount');
        $monthlySales = Order::where('payment_status', 'paid')->where('created_at', '>=', $thisMonth)->sum('total_amount');

        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $processingOrders = Order::whereIn('order_status', ['confirmed', 'processing', 'packed'])->count();
        $shippedOrders = Order::whereIn('order_status', ['shipped', 'out_for_delivery'])->count();
        $deliveredOrders = Order::where('order_status', 'delivered')->count();
        $cancelledOrders = Order::where('order_status', 'cancelled')->count();

        $totalCustomers = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['user', 'customer']);
        })->orWhereNull('role_id')->count();

        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock_quantity', '<=', 5)->count();

        // Recent Orders
        $recentOrders = Order::with('user')->latest()->take(7)->get();

        // Revenue & Orders Data (Last 6 months / 30 days / 1 year)
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        $timeframe = request('timeframe', '6months');
        $timeframeTitle = '(Last 6 Months)';

        if ($timeframe === '30days') {
            $timeframeTitle = '(Last 30 Days)';
            for ($i = 29; $i >= 0; $i--) {
                $day = Carbon::now()->subDays($i);
                $chartLabels[] = $day->format('d M');
                $chartRevenue[] = (float) Order::where('payment_status', 'paid')
                    ->whereDate('created_at', $day)
                    ->sum('total_amount');
                $chartOrders[] = Order::whereDate('created_at', $day)->count();
            }
        } elseif ($timeframe === 'year') {
            $timeframeTitle = '(Last 12 Months)';
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();

                $chartLabels[] = $month->format('M Y');
                $chartRevenue[] = (float) Order::where('payment_status', 'paid')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('total_amount');

                $chartOrders[] = Order::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            }
        } else {
            // Default: 6 months
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();

                $chartLabels[] = $month->format('M Y');
                $chartRevenue[] = (float) Order::where('payment_status', 'paid')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('total_amount');

                $chartOrders[] = Order::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            }
        }

        // Top 5 Selling Products
        $topProducts = Product::withCount(['images'])
            ->withSum('orderItems as total_sold', 'quantity')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'todaySales',
            'monthlySales',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalCustomers',
            'totalProducts',
            'lowStockProducts',
            'recentOrders',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'topProducts',
            'timeframeTitle',
            'timeframe'
        ));
    }

    public function catalogAnalytics(Request $request)
    {
        // 1. Core Catalog KPIs
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', true)->count();
        $inactiveProducts = $totalProducts - $activeProducts;
        $totalStock = (int) Product::sum('stock_quantity');
        $totalInventoryValue = (float) (Product::selectRaw('SUM(stock_quantity * COALESCE(sale_price, regular_price)) as val')->value('val') ?? 0);

        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)->count();
        $lowStockProducts = Product::whereBetween('stock_quantity', [1, 5])->count();
        $inStockProducts = Product::where('stock_quantity', '>', 5)->count();

        $totalBrands = Brand::count();
        $activeBrands = Brand::where('status', true)->count();

        $totalModels = CarModel::count();
        $activeModels = CarModel::where('status', true)->count();

        $totalVariants = ModelVariant::count();
        $activeVariants = ModelVariant::where('status', true)->count();

        $variantsWithProducts = ModelVariant::whereHas('products')->count();
        $variantsWithoutProducts = $totalVariants - $variantsWithProducts;
        $coveragePercentage = $totalVariants > 0 ? round(($variantsWithProducts / $totalVariants) * 100, 1) : 0;

        $totalCategories = Category::count();
        $parentCategories = Category::whereNull('parent_id')->count();
        $subCategories = Category::whereNotNull('parent_id')->count();

        // 2. Chart Data: Top 10 Brands by Products count
        $topBrands = Brand::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(10)
            ->get();
        $brandChartLabels = $topBrands->pluck('name')->toArray();
        $brandChartData = $topBrands->pluck('products_count')->toArray();

        // 3. Chart Data: Top 8 Categories by Products count
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(8)
            ->get();
        $categoryChartLabels = $topCategories->pluck('name')->toArray();
        $categoryChartData = $topCategories->pluck('products_count')->toArray();

        // 4. Chart Data: Placements Breakdown
        $placements = Product::select('placement', DB::raw('count(*) as total'))
            ->whereNotNull('placement')
            ->where('placement', '!=', '')
            ->groupBy('placement')
            ->orderBy('total', 'desc')
            ->pluck('total', 'placement')
            ->toArray();

        // 5. Chart Data: Finishes Breakdown
        $finishes = Product::select('finish', DB::raw('count(*) as total'))
            ->whereNotNull('finish')
            ->where('finish', '!=', '')
            ->groupBy('finish')
            ->orderBy('total', 'desc')
            ->take(7)
            ->pluck('total', 'finish')
            ->toArray();

        // 6. Detailed Tables: Brands with models, variants, products count and stock
        $brandsMatrix = Brand::withCount(['carModels', 'products'])
            ->with(['carModels' => function ($q) {
                $q->withCount('variants');
            }])
            ->orderBy('products_count', 'desc')
            ->get();

        // 7. Categories Matrix
        $categoriesMatrix = Category::with('parent')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->get();

        // 8. Top 12 Car Models by Product count
        $topModels = CarModel::with('brand')
            ->withCount(['variants', 'products'])
            ->orderBy('products_count', 'desc')
            ->take(12)
            ->get();

        // 9. Gap Analysis: Variants without any products
        $gapVariants = ModelVariant::doesntHave('products')
            ->with(['carModel.brand'])
            ->orderBy('name', 'asc')
            ->take(25)
            ->get();

        // 10. Price Tiers
        $priceTiers = [
            'Budget (< ₹500)' => Product::where('regular_price', '<', 500)->count(),
            'Mid Range (₹500 - ₹999)' => Product::whereBetween('regular_price', [500, 999])->count(),
            'Premium (₹1,000 - ₹1,999)' => Product::whereBetween('regular_price', [1000, 1999])->count(),
            'Elite / Kits (₹2,000+)' => Product::where('regular_price', '>=', 2000)->count(),
        ];

        // 11. Interactive Brand Drill-down filter
        $allBrands = Brand::orderBy('name', 'asc')->get(['id', 'name', 'slug']);
        $selectedBrandId = $request->get('brand_id');
        $selectedBrand = null;
        $selectedBrandModels = collect();
        if ($selectedBrandId) {
            $selectedBrand = Brand::withCount(['carModels', 'products'])->find($selectedBrandId);
            if ($selectedBrand) {
                $selectedBrandModels = CarModel::where('brand_id', $selectedBrandId)
                    ->withCount(['variants', 'products'])
                    ->orderBy('products_count', 'desc')
                    ->get();
            }
        }

        return view('admin.catalog_analytics', compact(
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'totalStock',
            'totalInventoryValue',
            'outOfStockProducts',
            'lowStockProducts',
            'inStockProducts',
            'totalBrands',
            'activeBrands',
            'totalModels',
            'activeModels',
            'totalVariants',
            'activeVariants',
            'variantsWithProducts',
            'variantsWithoutProducts',
            'coveragePercentage',
            'totalCategories',
            'parentCategories',
            'subCategories',
            'brandChartLabels',
            'brandChartData',
            'categoryChartLabels',
            'categoryChartData',
            'placements',
            'finishes',
            'brandsMatrix',
            'categoriesMatrix',
            'topModels',
            'gapVariants',
            'priceTiers',
            'allBrands',
            'selectedBrandId',
            'selectedBrand',
            'selectedBrandModels'
        ));
    }
}
