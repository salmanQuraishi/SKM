<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FileUploadService;

class CategoryCrudController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent', 'products'])->withCount('products')->orderBy('sort_order')->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'sort_order' => 'nullable|integer',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = FileUploadService::upload($request->file('image'), 'categories');
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
            'icon' => $request->icon,
            'image' => $imagePath,
            'description' => $request->description,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description,
            'status' => $request->boolean('status'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(int $id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $id)->orderBy('name')->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            $imagePath = FileUploadService::upload($request->file('image'), 'categories');
        }

        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'icon' => $request->icon,
            'image' => $imagePath,
            'description' => $request->description,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description,
            'status' => $request->boolean('status'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }

    public function toggleStatus(int $id)
    {
        $category = Category::findOrFail($id);
        $category->update(['status' => !$category->status]);

        $statusText = $category->status ? 'activated' : 'deactivated';

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $category->status,
                'message' => "Category '{$category->name}' has been {$statusText}."
            ]);
        }

        return redirect()->back()->with('success', "Category '{$category->name}' has been {$statusText}.");
    }
}
