<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleDependentDropdownController extends Controller
{
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
