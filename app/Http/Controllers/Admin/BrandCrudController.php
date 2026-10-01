<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandCrudController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::withCount(['products', 'carModels']);

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        $brands = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:brands,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $slug = Str::slug($request->name);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $logoPath = 'storage/' . $path;
        }

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('brands', 'public');
            $bannerPath = 'storage/' . $path;
        }

        Brand::create([
            'name' => $request->name,
            'slug' => $slug,
            'logo' => $logoPath,
            'banner' => $bannerPath,
            'description' => $request->description,
            'meta_title' => $request->meta_title ?: $request->name . ' Emblems & Logos',
            'meta_description' => $request->meta_description,
            'status' => $request->boolean('status', true),
            'is_featured' => $request->boolean('is_featured', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully!');
    }

    public function edit(int $id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, int $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => "required|string|max:100|unique:brands,name,{$id}",
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ]);

        $logoPath = $brand->logo;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $logoPath = 'storage/' . $path;
        }

        $bannerPath = $brand->banner;
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('brands', 'public');
            $bannerPath = 'storage/' . $path;
        }

        $brand->update([
            'name' => $request->name,
            'logo' => $logoPath,
            'banner' => $bannerPath,
            'description' => $request->description,
            'meta_title' => $request->meta_title ?: $request->name . ' Emblems & Logos',
            'meta_description' => $request->meta_description,
            'status' => $request->boolean('status', true),
            'is_featured' => $request->boolean('is_featured', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully!');
    }

    public function destroy(int $id)
    {
        $brand = Brand::withCount(['products', 'carModels'])->findOrFail($id);

        if ($brand->car_models_count > 0 || $brand->products_count > 0) {
            return redirect()->route('admin.brands.index')
                ->with('error', "Cannot delete brand '{$brand->name}' because it contains {$brand->car_models_count} car model(s) and {$brand->products_count} product(s). Please remove or reassign child items first.");
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully!');
    }
}
