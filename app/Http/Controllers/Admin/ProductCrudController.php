<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ModelVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\FileUploadService;

class ProductCrudController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'carModel', 'modelVariant', 'primaryCategory', 'images']);

        if ($request->filled('q')) {
            $searchTerm = trim($request->q);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('compatible_models_text', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('modelVariant', function ($vq) use ($searchTerm) {
                      $vq->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('car_model_id')) {
            $query->where('car_model_id', $request->car_model_id);
        }

        if ($request->filled('variant_id')) {
            $query->where('model_variant_id', $request->variant_id);
        }

        if ($request->filled('category_id')) {
            $query->where('primary_category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock_quantity', '<=', 5);
            }
        }

        $products = $query->latest('id')->paginate(15)->withQueryString();

        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $models = $request->filled('brand_id')
            ? CarModel::where('brand_id', $request->brand_id)->orderBy('name')->get()
            : CarModel::orderBy('name')->get();
        $variants = $request->filled('car_model_id')
            ? ModelVariant::where('car_model_id', $request->car_model_id)->orderBy('name')->get()
            : collect();

        return view('admin.products.index', compact('products', 'brands', 'categories', 'models', 'variants'));
    }

    public function create()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();
        $models = CarModel::active()->orderBy('name')->get();
        $variants = ModelVariant::active()->orderBy('name')->get();

        return view('admin.products.create', compact('brands', 'categories', 'models', 'variants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:regular_price',
            'stock_quantity' => 'required|integer|min:0',
            'brand_id' => 'nullable|exists:brands,id',
            'car_model_id' => 'nullable|exists:car_models,id',
            'model_variant_id' => 'nullable|exists:model_variants,id',
            'primary_category_id' => 'nullable|exists:categories,id',
            'product_type' => 'required|string',
            'placement' => 'nullable|string',
            'finish' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $brandId = $request->brand_id;
        $carModelId = $request->car_model_id;
        $variantId = $request->model_variant_id;

        // Auto-resolve hierarchy if variant is given
        if ($variantId) {
            $variant = ModelVariant::with('carModel')->find($variantId);
            if ($variant) {
                if ($carModelId && $variant->car_model_id != $carModelId) {
                    return back()->withErrors(['model_variant_id' => 'Selected variant does not belong to the selected car model.'])->withInput();
                }
                $carModelId = $variant->car_model_id;
                if ($brandId && $variant->carModel && $variant->carModel->brand_id != $brandId) {
                    return back()->withErrors(['model_variant_id' => 'Selected variant does not belong to the selected company/brand.'])->withInput();
                }
                $brandId = $variant->carModel?->brand_id ?? $brandId;
            }
        } elseif ($carModelId && $brandId) {
            // Verify car model belongs to brand
            $modelValid = CarModel::where('id', $carModelId)->where('brand_id', $brandId)->exists();
            if (!$modelValid) {
                return back()->withErrors(['car_model_id' => 'Selected car model does not belong to the selected company/brand.'])->withInput();
            }
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $product = Product::create([
            'brand_id' => $brandId,
            'car_model_id' => $carModelId,
            'model_variant_id' => $variantId,
            'primary_category_id' => $request->primary_category_id,
            'name' => $request->name,
            'slug' => $slug,
            'sku' => strtoupper(trim($request->sku)),
            'product_type' => $request->product_type,
            'placement' => $request->placement,
            'finish' => $request->finish,
            'compatible_models_text' => $request->compatible_models_text ?: 'All Models',
            'short_description' => $request->short_description,
            'full_description' => $request->full_description,
            'regular_price' => $request->regular_price,
            'sale_price' => $request->sale_price,
            'cost_price' => $request->cost_price,
            'stock_quantity' => $request->stock_quantity,
            'min_stock_alert' => $request->min_stock_alert ?? 5,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'shipping_class' => $request->shipping_class ?? 'standard',
            'tax_rate' => $request->tax_rate ?? 18.00,
            'hsn_sac' => $request->hsn_sac ?? '8708',
            'is_featured' => $request->boolean('is_featured'),
            'is_new_arrival' => $request->boolean('is_new_arrival'),
            'is_best_seller' => $request->boolean('is_best_seller'),
            'is_monogram' => $request->boolean('is_monogram') || $request->product_type === 'monogram',
            'status' => $request->boolean('status', true),
            'sort_order' => $request->sort_order ?? 0,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description ?: $request->short_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        if ($request->filled('category_ids')) {
            $product->categories()->sync($request->category_ids);
        } elseif ($request->primary_category_id) {
            $product->categories()->sync([$request->primary_category_id]);
        }

        // Upload Product Images
        if ($request->hasFile('images')) {
            $isPrimary = true;
            $order = 0;
            foreach ($request->file('images') as $file) {
                $imagePath = FileUploadService::upload($file, 'products');
                $product->images()->create([
                    'image_path' => $imagePath,
                    'is_primary' => $isPrimary,
                    'sort_order' => $order++,
                ]);
                $isPrimary = false;
            }
        }

        // Create variants if supplied
        if ($request->filled('variants') && is_array($request->variants)) {
            foreach ($request->variants as $var) {
                if (!empty($var['name']) && !empty($var['sku'])) {
                    $product->variants()->create([
                        'name' => $var['name'],
                        'sku' => strtoupper(trim($var['sku'])),
                        'regular_price' => $var['regular_price'] ?? $product->regular_price,
                        'sale_price' => $var['sale_price'] ?? $product->sale_price,
                        'stock_quantity' => $var['stock_quantity'] ?? $product->stock_quantity,
                        'placement' => $var['placement'] ?? $product->placement,
                        'finish' => $var['finish'] ?? $product->finish,
                        'status' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' created successfully!");
    }

    public function edit(int $id)
    {
        $product = Product::with(['images', 'variants', 'categories', 'brand', 'carModel', 'modelVariant'])->findOrFail($id);
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $models = $product->brand_id 
            ? CarModel::where('brand_id', $product->brand_id)->orderBy('name')->get() 
            : CarModel::orderBy('name')->get();
        $variants = $product->car_model_id 
            ? ModelVariant::where('car_model_id', $product->car_model_id)->orderBy('name')->get() 
            : collect();

        return view('admin.products.edit', compact('product', 'brands', 'categories', 'models', 'variants'));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => "required|string|max:50|unique:products,sku,{$id}",
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:regular_price',
            'stock_quantity' => 'required|integer|min:0',
            'brand_id' => 'nullable|exists:brands,id',
            'car_model_id' => 'nullable|exists:car_models,id',
            'model_variant_id' => 'nullable|exists:model_variants,id',
            'primary_category_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ]);

        $brandId = $request->brand_id;
        $carModelId = $request->car_model_id;
        $variantId = $request->model_variant_id;

        // Auto-resolve hierarchy if variant is given
        if ($variantId) {
            $variant = ModelVariant::with('carModel')->find($variantId);
            if ($variant) {
                if ($carModelId && $variant->car_model_id != $carModelId) {
                    return back()->withErrors(['model_variant_id' => 'Selected variant does not belong to the selected car model.'])->withInput();
                }
                $carModelId = $variant->car_model_id;
                if ($brandId && $variant->carModel && $variant->carModel->brand_id != $brandId) {
                    return back()->withErrors(['model_variant_id' => 'Selected variant does not belong to the selected company/brand.'])->withInput();
                }
                $brandId = $variant->carModel?->brand_id ?? $brandId;
            }
        } elseif ($carModelId && $brandId) {
            $modelValid = CarModel::where('id', $carModelId)->where('brand_id', $brandId)->exists();
            if (!$modelValid) {
                return back()->withErrors(['car_model_id' => 'Selected car model does not belong to the selected company/brand.'])->withInput();
            }
        }

        $product->update([
            'brand_id' => $brandId,
            'car_model_id' => $carModelId,
            'model_variant_id' => $variantId,
            'primary_category_id' => $request->primary_category_id,
            'name' => $request->name,
            'sku' => strtoupper(trim($request->sku)),
            'product_type' => $request->product_type,
            'placement' => $request->placement,
            'finish' => $request->finish,
            'compatible_models_text' => $request->compatible_models_text ?: 'All Models',
            'short_description' => $request->short_description,
            'full_description' => $request->full_description,
            'regular_price' => $request->regular_price,
            'sale_price' => $request->sale_price,
            'cost_price' => $request->cost_price,
            'stock_quantity' => $request->stock_quantity,
            'min_stock_alert' => $request->min_stock_alert ?? 5,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'shipping_class' => $request->shipping_class ?? 'standard',
            'tax_rate' => $request->tax_rate ?? 18.00,
            'hsn_sac' => $request->hsn_sac ?? '8708',
            'is_featured' => $request->boolean('is_featured'),
            'is_new_arrival' => $request->boolean('is_new_arrival'),
            'is_best_seller' => $request->boolean('is_best_seller'),
            'is_monogram' => $request->boolean('is_monogram') || $request->product_type === 'monogram',
            'status' => $request->boolean('status', true),
            'sort_order' => $request->sort_order ?? 0,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description ?: $request->short_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        $catIds = $request->category_ids ?? [];
        if ($request->filled('primary_category_id') && !in_array($request->primary_category_id, $catIds)) {
            $catIds[] = (int) $request->primary_category_id;
        }
        if (!empty($catIds)) {
            $product->categories()->sync($catIds);
        }

        // Upload additional images
        if ($request->hasFile('images')) {
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            $order = ($product->images()->max('sort_order') ?? 0) + 1;
            foreach ($request->file('images') as $file) {
                $imagePath = FileUploadService::upload($file, 'products');
                $product->images()->create([
                    'image_path' => $imagePath,
                    'is_primary' => !$hasPrimary,
                    'sort_order' => $order++,
                ]);
                $hasPrimary = true;
            }
        }

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' updated successfully!");
    }

    public function deleteImage(int $id)
    {
        $image = ProductImage::findOrFail($id);
        $productId = $image->product_id;
        $image->delete();

        // If primary image was deleted, assign primary to next image
        $product = Product::find($productId);
        if ($product && !$product->images()->where('is_primary', true)->exists()) {
            $first = $product->images()->first();
            if ($first) {
                $first->update(['is_primary' => true]);
            }
        }

        return redirect()->back()->with('success', 'Image removed.');
    }

    public function duplicate(int $id)
    {
        $original = Product::with(['images', 'variants', 'categories'])->findOrFail($id);

        $newProduct = $original->replicate([
            'sku', 'slug', 'created_at', 'updated_at'
        ]);

        $newProduct->name = $original->name . ' (Copy)';
        $newProduct->sku = $original->sku . '-COPY-' . rand(100, 999);
        $newProduct->slug = Str::slug($newProduct->name) . '-' . rand(100, 999);
        $newProduct->save();

        // Copy categories
        $newProduct->categories()->sync($original->categories->pluck('id'));

        // Copy images
        foreach ($original->images as $img) {
            $newProduct->images()->create([
                'image_path' => $img->image_path,
                'is_primary' => $img->is_primary,
                'sort_order' => $img->sort_order,
            ]);
        }

        // Copy variants
        foreach ($original->variants as $var) {
            $newProduct->variants()->create([
                'name' => $var->name,
                'sku' => $var->sku . '-COPY-' . rand(100, 999),
                'regular_price' => $var->regular_price,
                'sale_price' => $var->sale_price,
                'stock_quantity' => $var->stock_quantity,
                'weight' => $var->weight,
                'placement' => $var->placement,
                'finish' => $var->finish,
                'status' => $var->status,
            ]);
        }

        return redirect()->back()->with('success', "Product duplicated as '{$newProduct->name}'!");
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return redirect()->back()->with('success', "Product '{$name}' deleted successfully!");
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No products selected.');
        }

        if ($action === 'delete') {
            Product::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', count($ids) . ' products deleted.');
        }

        if ($action === 'activate') {
            Product::whereIn('id', $ids)->update(['status' => true]);
            return redirect()->back()->with('success', count($ids) . ' products activated.');
        }

        if ($action === 'deactivate') {
            Product::whereIn('id', $ids)->update(['status' => false]);
            return redirect()->back()->with('success', count($ids) . ' products deactivated.');
        }

        return redirect()->back();
    }
}
