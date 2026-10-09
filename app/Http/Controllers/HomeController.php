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
        $popularProducts = Product::active()->where('is_featured', true)->with(['images', 'brand'])->take(12)->get();
        if ($popularProducts->count() < 12) {
            $needed = 12 - $popularProducts->count();
            $more = Product::active()->whereNotIn('id', $popularProducts->pluck('id'))->with(['images', 'brand'])->take($needed)->get();
            $popularProducts = $popularProducts->concat($more);
        }
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

        $prodQuery = Product::active()->search($query);

        if ($request->filled('category')) {
            $category = \App\Models\Category::where('slug', $request->category)->first();
            if ($category) {
                $catIds = $category->children->pluck('id')->push($category->id)->toArray();
                $prodQuery->where(function ($cq) use ($catIds) {
                    $cq->whereIn('primary_category_id', $catIds)
                       ->orWhereHas('categories', function ($sq) use ($catIds) {
                           $sq->whereIn('categories.id', $catIds);
                       });
                });
            }
        }

        $products = $prodQuery
            ->orderByRelevance($query)
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

        $carModels = \App\Models\CarModel::active()
            ->where('name', 'LIKE', "%{$query}%")
            ->with('brand')
            ->take(3)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'name' => ($m->brand ? $m->brand->name . ' ' : '') . $m->name,
                    'url' => $m->brand ? route('vehicle.variants', [$m->brand->slug, $m->slug]) : route('shop', ['model' => $m->slug]),
                    'type' => 'model',
                ];
            });

        return response()->json([
            'products' => $products,
            'brands' => $brands,
            'models' => $carModels,
        ]);
    }
}
