<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $heroBanners = Banner::hero()->get();
        if ($heroBanners->isEmpty()) {
            // Fallback default banner data if none seeded yet
            $heroBanners = collect([(object)[
                'title' => 'GENUINE CAR LOGOS, MONOGRAMS & EMBLEMS',
                'subtitle' => 'FOR ALL BRANDS & MODELS',
                'badge_text' => '100% Genuine Products',
                'button_text' => 'SHOP NOW >',
                'button_url' => route('shop'),
                'features_json' => [
                    '100% Genuine Products',
                    'Perfect Fit For Your Car',
                    'Premium Chrome Finish',
                    'Easy Returns & Refunds'
                ]
            ]]);
        }

        $brands = Brand::active()->orderBy('sort_order')->get();
        $popularProducts = Product::active()->where('is_featured', true)->with(['images', 'brand'])->take(8)->get();
        $monograms = Product::active()->where('is_monogram', true)->with(['images', 'brand'])->take(8)->get();
        $carModels = CarModel::active()->popular()->with('brand')->orderBy('sort_order')->take(10)->get();

        $sidebarCategories = Category::active()->whereNull('parent_id')->with('children')->orderBy('sort_order')->get();

        return view('frontend.home', compact(
            'heroBanners',
            'brands',
            'popularProducts',
            'monograms',
            'carModels',
            'sidebarCategories'
        ));
    }

    public function searchSuggestions(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%")
                  ->orWhere('compatible_models_text', 'LIKE', "%{$query}%");
            })
            ->with(['images', 'brand'])
            ->take(6)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => '&#x20B9;' . number_format($p->active_price, 2),
                    'url' => route('product.detail', $p->slug),
                    'image' => $p->primary_image_url,
                    'brand' => $p->brand ? $p->brand->name : null,
                ];
            });

        $brands = Brand::active()
            ->where('name', 'LIKE', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'url' => route('brand.products', $b->slug),
                    'type' => 'brand',
                ];
            });

        return response()->json([
            'products' => $products,
            'brands' => $brands,
        ]);
    }
}
