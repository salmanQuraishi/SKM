<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\ModelVariant;
use App\Models\Product;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Level 1: Choose Your Car Brand / Company
     * URL: /vehicles
     */
    public function companies()
    {
        $companies = Brand::active()
            ->withCount(['carModels' => function ($q) {
                $q->where('status', true);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('frontend.vehicles.companies', compact('companies'));
    }

    /**
     * Level 2: Choose Your Car Model
     * URL: /vehicle/{companySlug}
     */
    public function cars(string $companySlug)
    {
        $company = Brand::active()
            ->where('slug', $companySlug)
            ->firstOrFail();

        $cars = CarModel::active()
            ->where('brand_id', $company->id)
            ->withCount([
                'variants' => function ($q) {
                    $q->where('status', true);
                },
                'products' => function ($q) {
                    $q->where('status', true);
                }
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('frontend.vehicles.cars', compact('company', 'cars'));
    }

    /**
     * Level 3: Choose Your Car Variant
     * URL: /vehicle/{companySlug}/{carSlug}
     */
    public function variants(string $companySlug, string $carSlug)
    {
        $company = Brand::active()
            ->where('slug', $companySlug)
            ->firstOrFail();

        $car = CarModel::active()
            ->where('brand_id', $company->id)
            ->where('slug', $carSlug)
            ->firstOrFail();

        $variants = ModelVariant::active()
            ->where('car_model_id', $car->id)
            ->withCount(['products' => function ($q) {
                $q->where('status', true);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('frontend.vehicles.variants', compact('company', 'car', 'variants'));
    }

    /**
     * Level 4: View Products specifically compatible with Selected Variant
     * URL: /vehicle/{companySlug}/{carSlug}/{variantSlug}
     */
    public function products(string $companySlug, string $carSlug, string $variantSlug, Request $request)
    {
        $company = Brand::active()
            ->where('slug', $companySlug)
            ->firstOrFail();

        $car = CarModel::active()
            ->where('brand_id', $company->id)
            ->where('slug', $carSlug)
            ->firstOrFail();

        $variant = ModelVariant::active()
            ->where('car_model_id', $car->id)
            ->where('slug', $variantSlug)
            ->firstOrFail();

        // Query only products explicitly mapped to this variant!
        $query = Product::active()
            ->where('model_variant_id', $variant->id)
            ->with(['images', 'brand', 'carModel', 'modelVariant', 'primaryCategory']);

        // Search within this variant's catalog
        if ($request->filled('q')) {
            $searchTerm = trim($request->q);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('short_description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Category filter (support both primary category and pivot categories)
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $catIds = $category->children->pluck('id')->push($category->id)->toArray();
                $query->where(function ($q) use ($catIds) {
                    $q->whereIn('primary_category_id', $catIds)
                      ->orWhereHas('categories', function ($cq) use ($catIds) {
                          $cq->whereIn('categories.id', $catIds);
                      });
                });
            }
        }

        // Placement filter (flexible match)
        if ($request->filled('placement')) {
            $placement = trim($request->placement);
            $query->where(function ($q) use ($placement) {
                $q->where('placement', $placement)
                  ->orWhere('placement', 'LIKE', "%{$placement}%");
            });
        }

        // Finish filter (flexible match)
        if ($request->filled('finish')) {
            $finish = trim($request->finish);
            $query->where(function ($q) use ($finish) {
                $q->where('finish', $finish)
                  ->orWhere('finish', 'LIKE', "%{$finish}%");
            });
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('regular_price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('regular_price', '<=', (float) $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
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
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Sibling trims for this car
        $siblingVariants = ModelVariant::active()
            ->where('car_model_id', $car->id)
            ->withCount(['products' => function ($q) {
                $q->where('status', true);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Sibling cars for this brand
        $siblingCars = CarModel::active()
            ->where('brand_id', $company->id)
            ->withCount(['products' => function ($q) {
                $q->where('status', true);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $selectedCategory = $request->filled('category') ? Category::where('slug', $request->category)->first() : null;

        $categories = Category::active()
            ->whereNull('parent_id')
            ->with(['children' => function ($cq) use ($variant) {
                $cq->withCount(['products' => function ($q) use ($variant) {
                    $q->where('status', true)->where('model_variant_id', $variant->id);
                }]);
            }])
            ->withCount(['products' => function ($q) use ($variant) {
                $q->where('status', true)->where('model_variant_id', $variant->id);
            }])
            ->orderBy('sort_order')
            ->get();

        $minCatalogPrice = (int) (Product::active()->where('model_variant_id', $variant->id)->min('regular_price') ?: 0);
        $maxCatalogPrice = (int) (Product::active()->where('model_variant_id', $variant->id)->max('regular_price') ?: 5000);

        return view('frontend.vehicles.products', compact(
            'company',
            'car',
            'variant',
            'siblingVariants',
            'siblingCars',
            'products',
            'categories',
            'selectedCategory',
            'minCatalogPrice',
            'maxCatalogPrice'
        ));
    }
}
