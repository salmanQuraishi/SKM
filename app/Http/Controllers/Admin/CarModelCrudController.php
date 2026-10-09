<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FileUploadService;

class CarModelCrudController extends Controller
{
    public function index(Request $request)
    {
        $query = CarModel::with(['brand'])->withCount(['products', 'variants']);

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function ($bq) use ($search) {
                      $bq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $models = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();
        $brands = Brand::orderBy('name')->get();

        return view('admin.models.index', compact('models', 'brands'));
    }

    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();
        return view('admin.models.create', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:100',
            'badge_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ]);

        $slug = Str::slug($request->name);

        $badgePath = null;
        if ($request->hasFile('badge_image')) {
            $badgePath = FileUploadService::upload($request->file('badge_image'), 'models');
        }

        CarModel::create([
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'slug' => $slug,
            'badge_image' => $badgePath,
            'is_popular' => $request->boolean('is_popular', true),
            'status' => $request->boolean('status', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.models.index')->with('success', 'Car Model created successfully!');
    }

    public function edit(int $id)
    {
        $model = CarModel::findOrFail($id);
        $brands = Brand::active()->orderBy('name')->get();

        return view('admin.models.edit', compact('model', 'brands'));
    }

    public function update(Request $request, int $id)
    {
        $model = CarModel::findOrFail($id);

        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:100',
            'badge_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ]);

        $badgePath = $model->badge_image;
        if ($request->hasFile('badge_image')) {
            $badgePath = FileUploadService::upload($request->file('badge_image'), 'models');
        }

        $model->update([
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'badge_image' => $badgePath,
            'is_popular' => $request->boolean('is_popular'),
            'status' => $request->boolean('status', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.models.index')->with('success', 'Car Model updated successfully!');
    }

    public function destroy(int $id)
    {
        $model = CarModel::withCount(['products', 'variants'])->findOrFail($id);

        if ($model->variants_count > 0 || $model->products_count > 0) {
            return redirect()->route('admin.models.index')
                ->with('error', "Cannot delete model '{$model->name}' because it contains {$model->variants_count} variant(s) and {$model->products_count} product(s). Please remove or reassign child items first.");
        }

        $model->delete();

        return redirect()->route('admin.models.index')->with('success', 'Car Model deleted successfully!');
    }
}
