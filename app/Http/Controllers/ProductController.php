<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\ModelVariant;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['images', 'brand', 'carModel', 'primaryCategory']);

        // Smart Search Filter
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        // Category Filter (supports primary category and pivot categories)
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $catIds = $category->children->pluck('id')->push($category->id)->toArray();
                $query->where(function ($cq) use ($catIds) {
                    $cq->whereIn('primary_category_id', $catIds)
                       ->orWhereHas('categories', function ($sq) use ($catIds) {
                           $sq->whereIn('categories.id', $catIds);
                       });
                });
            }
        }

        // Brand Filter
        if ($request->filled('brand')) {
            $brand = Brand::where('slug', $request->brand)->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        // Car Model Filter
        if ($request->filled('model')) {
            $model = CarModel::where('slug', $request->model)->first();
            if ($model) {
                $query->where('car_model_id', $model->id);
            }
        }

        // Car Variant Filter
        if ($request->filled('variant')) {
            $variant = ModelVariant::where('slug', $request->variant)->first();
            if ($variant) {
                $query->where('model_variant_id', $variant->id);
            }
        } elseif ($request->filled('variant_id')) {
            $query->where('model_variant_id', $request->variant_id);
        }

        // Product Type Filter
        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }

        // Placement Filter (Front / Rear / Side / Steering)
        if ($request->filled('placement')) {
            $query->where('placement', $request->placement);
        }

        // Finish Filter (Chrome / Black / Silver / Carbon / Red)
        if ($request->filled('finish')) {
            $finish = $request->finish;
            $query->where(function ($q) use ($finish) {
                $q->where('finish', $finish)
                  ->orWhere('finish', 'LIKE', "%{$finish}%");
            });
        }

        // Price Filter
        if ($request->filled('min_price')) {
            $query->where('regular_price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('regular_price', '<=', (float) $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', $request->filled('q') ? 'relevance' : 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('regular_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('regular_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('is_best_seller', 'desc')->orderBy('is_featured', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'relevance':
                $query->orderByRelevance($request->q);
                break;
            default:
                if ($request->filled('q')) {
                    $query->orderByRelevance($request->q);
                } else {
                    $query->latest('products.id');
                }
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $brands = Brand::active()->withCount('products')->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::active()->whereNull('parent_id')->with(['children' => fn($q) => $q->withCount('products')])->withCount('products')->orderBy('sort_order')->get();

        $selectedBrand = $request->filled('brand') ? Brand::where('slug', $request->brand)->first() : null;
        $selectedModel = $request->filled('model') ? CarModel::where('slug', $request->model)->first() : null;
        $selectedVariant = $request->filled('variant') ? ModelVariant::where('slug', $request->variant)->first() : null;
        $selectedCategory = $request->filled('category') ? Category::where('slug', $request->category)->first() : null;

        // Auto-resolve brand & model if deeper level is requested directly
        if ($selectedVariant && !$selectedModel) {
            $selectedModel = $selectedVariant->carModel;
        }
        if ($selectedModel && !$selectedBrand) {
            $selectedBrand = $selectedModel->brand;
        }

        $models = $selectedBrand 
            ? CarModel::active()->where('brand_id', $selectedBrand->id)->withCount('products')->orderBy('name')->get()
            : collect();

        $variants = $selectedModel
            ? ModelVariant::active()->where('car_model_id', $selectedModel->id)->withCount('products')->orderBy('name')->get()
            : ($selectedBrand 
                ? ModelVariant::active()->whereHas('carModel', fn($q) => $q->where('brand_id', $selectedBrand->id))->withCount('products')->orderBy('name')->get()
                : collect());

        $minCatalogPrice = (int) (Product::active()->min('regular_price') ?: 0);
        $maxCatalogPrice = (int) (Product::active()->max('regular_price') ?: 5000);

        return view('frontend.shop', compact(
            'products', 
            'brands', 
            'categories', 
            'models', 
            'variants', 
            'selectedBrand', 
            'selectedModel', 
            'selectedVariant', 
            'selectedCategory', 
            'minCatalogPrice', 
            'maxCatalogPrice'
        ));
    }

    public function detail(string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['images', 'brand', 'carModel', 'modelVariant.carModel.brand', 'primaryCategory', 'variants', 'reviews.user'])
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                if ($product->brand_id) {
                    $q->where('brand_id', $product->brand_id);
                }
                if ($product->primary_category_id) {
                    $q->orWhere('primary_category_id', $product->primary_category_id);
                }
            })
            ->with('images')
            ->take(4)
            ->get();

        return view('frontend.product_detail', compact('product', 'relatedProducts'));
    }

    public function checkPincode(Request $request)
    {
        $pincode = trim($request->get('pincode', ''));
        if (strlen($pincode) !== 6 || !is_numeric($pincode)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid 6-digit Indian PIN code.'
            ]);
        }

        // Standard delivery check estimation
        $deliveryDays = rand(3, 5);
        $expectedDate = now()->addDays($deliveryDays)->format('d M, Y');

        return response()->json([
            'success' => true,
            'message' => "Delivery available! Expected by {$expectedDate} (Express 3-5 days). Cash on Delivery available.",
            'expected_date' => $expectedDate,
            'cod_available' => true,
        ]);
    }
}
