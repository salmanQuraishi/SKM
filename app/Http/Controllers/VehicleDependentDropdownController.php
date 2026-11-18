<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VehicleDependentDropdownController extends Controller
{
    /**
     * Return cached list of active brands with model counts via AJAX
     * (used by mobile offcanvas menu on-demand so desktop & bot page loads carry zero bloat).
     */
    public function getActiveBrands(): JsonResponse
    {
        $brands = Cache::remember('ajax_active_brands_list', 1800, function () {
            return Brand::active()
                ->withCount(['carModels' => function ($q) {
                    $q->active();
                }])
                ->orderBy('sort_order')
                ->get()
                ->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name,
                        'slug' => $brand->slug,
                        'logo_url' => $brand->logo_url,
                        'cars_url' => route('vehicle.cars', $brand->slug),
                        'models_count' => (int) $brand->car_models_count,
                    ];
                })->values()->all();
        });

        return response()->json([
            'success' => true,
            'data' => $brands,
        ])->header('Cache-Control', 'public, max-age=1800');
    }

    /**
     * Return cached models + variants hierarchy for a single brand via AJAX
     * (used by desktop hover mega-menu & mobile offcanvas accordion to avoid huge HTML payloads).
     */
    public function getBrandHierarchy(string $brandSlug): JsonResponse
    {
        $cacheKey = 'brand_hierarchy_' . $brandSlug;

        $payload = Cache::remember($cacheKey, 1800, function () use ($brandSlug) {
            $brand = Brand::active()
                ->where('slug', $brandSlug)
                ->with([
                    'carModels' => function ($q) {
                        $q->active()->orderBy('sort_order')->orderBy('name')->with([
                            'variants' => function ($vq) {
                                $vq->active()->orderBy('sort_order')->orderBy('name');
                            },
                        ]);
                    },
                ])
                ->first();

            if (!$brand) {
                return null;
            }

            return [
                'success' => true,
                'brand' => [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'logo_url' => $brand->logo_url,
                    'cars_url' => route('vehicle.cars', $brand->slug),
                ],
                'models' => $brand->carModels->map(function ($car) use ($brand) {
                    return [
                        'id' => $car->id,
                        'name' => $car->name,
                        'slug' => $car->slug,
                        'url' => route('vehicle.variants', [$brand->slug, $car->slug]),
                        'variants' => $car->variants->map(function ($variant) use ($brand, $car) {
                            return [
                                'id' => $variant->id,
                                'name' => $variant->name,
                                'slug' => $variant->slug,
                                'url' => route('vehicle.products', [$brand->slug, $car->slug, $variant->slug]),
                            ];
                        })->values()->all(),
                    ];
                })->values()->all(),
            ];
        });

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found',
                'models' => [],
            ], 404);
        }

        return response()->json($payload)
            ->header('Cache-Control', 'public, max-age=1800');
    }

    /**
     * Return active car models for a given brand / company (by ID or slug).
     */
    public function getModelsByBrand(int|string $brandId): JsonResponse
    {
        $brand = is_numeric($brandId)
            ? Brand::find($brandId)
            : Brand::where('slug', $brandId)->first();

        if (!$brand) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Brand not found',
            ]);
        }

        $models = CarModel::active()
            ->where('brand_id', $brand->id)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'brand_id', 'name', 'slug', 'badge_image']);

        return response()->json([
            'success' => true,
            'data' => $models,
        ]);
    }

    /**
     * Return active variants for a given car model (by ID or slug).
     */
    public function getVariantsByModel(int|string $modelId): JsonResponse
    {
        $model = is_numeric($modelId)
            ? CarModel::find($modelId)
            : CarModel::where('slug', $modelId)->first();

        if (!$model) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Car model not found',
            ]);
        }

        $variants = ModelVariant::active()
            ->where('car_model_id', $model->id)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'car_model_id', 'name', 'slug', 'image']);

        return response()->json([
            'success' => true,
            'data' => $variants,
        ]);
    }
}
