<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FileUploadService;

class CarVariantCrudController extends Controller
{
    public function index(Request $request)
    {
        $query = ModelVariant::with(['carModel.brand'])->withCount('products');

        if ($request->filled('brand_id')) {
            $brandId = $request->brand_id;
            $query->whereHas('carModel', function ($q) use ($brandId) {
                $q->where('brand_id', $brandId);
            });
        }

        if ($request->filled('car_model_id')) {
            $query->where('car_model_id', $request->car_model_id);
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhereHas('carModel', function ($mq) use ($search) {
                      $mq->where('name', 'LIKE', "%{$search}%")
                         ->orWhereHas('brand', function ($bq) use ($search) {
                             $bq->where('name', 'LIKE', "%{$search}%");
                         });
                  });
            });
        }

        $variants = $query->orderBy('car_model_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $brands = Brand::orderBy('name')->get();
        $selectedBrandId = $request->brand_id;
        $models = $selectedBrandId 
            ? CarModel::where('brand_id', $selectedBrandId)->orderBy('name')->get() 
            : CarModel::orderBy('name')->get();

        return view('admin.variants.index', compact('variants', 'brands', 'models'));
    }

    public function create(Request $request)
    {
        $brands = Brand::active()->orderBy('name')->get();
        $selectedBrandId = $request->brand_id ?? ($brands->first()?->id ?? null);
        $models = $selectedBrandId 
            ? CarModel::where('brand_id', $selectedBrandId)->active()->orderBy('name')->get() 
            : collect();

        return view('admin.variants.create', compact('brands', 'models', 'selectedBrandId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'car_model_id' => 'required|exists:car_models,id',
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Validate that car model actually belongs to selected brand
        $carModel = CarModel::where('id', $request->car_model_id)
            ->where('brand_id', $request->brand_id)
            ->first();

        if (!$carModel) {
            return back()->withErrors(['car_model_id' => 'The selected car model does not belong to the selected company/brand.'])->withInput();
        }

        // Check for duplicate variant name under this car model
        $duplicate = ModelVariant::where('car_model_id', $request->car_model_id)
            ->where('name', trim($request->name))
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['name' => "A variant named '{$request->name}' already exists for {$carModel->name}."])->withInput();
        }

        // Generate clean slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (ModelVariant::where('car_model_id', $request->car_model_id)->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = FileUploadService::upload($request->file('image'), 'variants');
        }

        ModelVariant::create([
            'car_model_id' => $request->car_model_id,
            'name' => trim($request->name),
            'slug' => $slug,
            'image' => $imagePath,
            'status' => $request->boolean('status'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.variants.index', ['brand_id' => $request->brand_id, 'car_model_id' => $request->car_model_id])
            ->with('success', "Variant '{$request->name}' created successfully!");
    }

    public function edit(int $id)
    {
        $variant = ModelVariant::with('carModel.brand')->findOrFail($id);
        $brands = Brand::active()->orderBy('name')->get();
        $models = CarModel::where('brand_id', $variant->carModel->brand_id)->active()->orderBy('name')->get();

        return view('admin.variants.edit', compact('variant', 'brands', 'models'));
    }

    public function update(Request $request, int $id)
    {
        $variant = ModelVariant::findOrFail($id);

        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'car_model_id' => 'required|exists:car_models,id',
            'name' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Validate that car model actually belongs to selected brand
        $carModel = CarModel::where('id', $request->car_model_id)
            ->where('brand_id', $request->brand_id)
            ->first();

        if (!$carModel) {
            return back()->withErrors(['car_model_id' => 'The selected car model does not belong to the selected company/brand.'])->withInput();
        }

        // Check for duplicate variant under same model (excluding current id)
        $duplicate = ModelVariant::where('car_model_id', $request->car_model_id)
            ->where('name', trim($request->name))
            ->where('id', '!=', $id)
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['name' => "A variant named '{$request->name}' already exists for {$carModel->name}."])->withInput();
        }

        $imagePath = $variant->image;
        if ($request->hasFile('image')) {
            $imagePath = FileUploadService::upload($request->file('image'), 'variants');
        }

        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (ModelVariant::where('car_model_id', $request->car_model_id)->where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $variant->update([
            'car_model_id' => $request->car_model_id,
            'name' => trim($request->name),
            'slug' => $slug,
            'image' => $imagePath,
            'status' => $request->boolean('status'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.variants.index', ['car_model_id' => $request->car_model_id])
            ->with('success', "Variant '{$variant->name}' updated successfully!");
    }

    public function destroy(int $id)
    {
        $variant = ModelVariant::withCount('products')->findOrFail($id);

        if ($variant->products_count > 0) {
            return redirect()->route('admin.variants.index')
                ->with('error', "Cannot delete variant '{$variant->name}' because {$variant->products_count} product(s) are currently mapped to it. Please reassign those products first.");
        }

        $variant->delete();

        return redirect()->route('admin.variants.index')
            ->with('success', "Variant '{$variant->name}' deleted successfully!");
    }
}
