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
            $query->search($request->q);
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
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();
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
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();
        $models = CarModel::active()->orderBy('name')->get();
        $variants = ModelVariant::active()->orderBy('name')->get();

        return view('admin.products.create', compact('brands', 'categories', 'models', 'variants'));
    }

    public function store(Request $request)
    {
        // Auto-generate SKU if left blank by user
        if (!$request->filled('sku') && $request->filled('name')) {
            $words = preg_split('/\s+/', preg_replace('/[^a-zA-Z0-9\s]/', '', strtoupper($request->name)));
            $words = array_values(array_filter($words));
            $w1 = isset($words[0]) ? substr($words[0], 0, 4) : 'PRD';
            $w2 = isset($words[1]) ? substr($words[1], 0, 4) : 'GEN';
            $prefix = $w1 . '-' . $w2;
            $generatedSku = $prefix . '-' . rand(100, 999);
            while (Product::where('sku', $generatedSku)->exists()) {
                $generatedSku = $prefix . '-' . rand(1000, 9999);
            }
            $request->merge(['sku' => $generatedSku]);
        }

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
            'hsn_code' => 'nullable|string|max:50',
            'hsn_sac' => 'nullable|string|max:50',
            'amazon_url' => 'nullable|string|max:1000',
            'flipkart_url' => 'nullable|string|max:1000',
            'images.*' => 'nullable|file|max:20480',
        ]);

        $normalizeMarketplaceUrl = function ($url) {
            if (empty($url)) return null;
            $url = trim($url);
            if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                $url = 'https://' . $url;
            }
            return $url;
        };

        $brandId = $request->brand_id;
        $carModelId = $request->car_model_id;
        $variantId = $request->model_variant_id;

        // Auto-resolve and align hierarchy gracefully without throwing blocking errors
        if ($variantId) {
            $variant = ModelVariant::with('carModel')->find($variantId);
            if ($variant) {
                $carModelId = $variant->car_model_id;
                $brandId = $variant->carModel?->brand_id ?? $brandId;
            }
        } elseif ($carModelId) {
            $carModel = CarModel::find($carModelId);
            if ($carModel) {
                $brandId = $carModel->brand_id;
            }
        }

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $catIds = (array) ($request->category_ids ?? []);
        $primaryCatId = $request->primary_category_id;
        $type = $request->product_type;
        $placement = $request->placement;

        // Smart Mapping from Product Type to Category, Placement & Flags
        $typeToCatSlug = [
            'front_logo' => ['slug' => 'front-logo', 'placement' => 'Front Grille'],
            'rear_logo' => ['slug' => 'rear-logo', 'placement' => 'Rear Trunk'],
            'bonnet_letters' => ['slug' => 'bonnet-tailgate-letters', 'placement' => 'Bonnet / Boot'],
            'full_monogram_kit' => ['slug' => 'kit-full-monogram', 'placement' => 'Full Exterior Kit'],
            'single_badge' => ['slug' => 'single-name-badge', 'placement' => null],
            'new_arrival' => ['slug' => 'new-arrivals', 'placement' => null],
            // Standard / Legacy Types mapped to appropriate Catalog & Header categories
            'logo' => ['slug' => 'car-logos', 'placement' => null],
            'emblem' => ['slug' => 'single-name-badge', 'secondary_slug' => 'model-logos-badges', 'placement' => null],
            'monogram' => ['slug' => 'bonnet-tailgate-letters', 'secondary_slug' => 'car-monograms', 'placement' => 'Bonnet / Boot'],
            'accessory' => ['slug' => 'accessories', 'placement' => 'Universal'],
        ];

        if (isset($typeToCatSlug[$type])) {
            $matchedCat = Category::where('slug', $typeToCatSlug[$type]['slug'])->first();
            if ($matchedCat) {
                if (!$primaryCatId) {
                    $primaryCatId = $matchedCat->id;
                }
                if (!in_array($matchedCat->id, $catIds)) {
                    $catIds[] = $matchedCat->id;
                }
            }
            if (!empty($typeToCatSlug[$type]['secondary_slug'])) {
                $secCat = Category::where('slug', $typeToCatSlug[$type]['secondary_slug'])->first();
                if ($secCat && !in_array($secCat->id, $catIds)) {
                    $catIds[] = $secCat->id;
                }
            }
            if ($typeToCatSlug[$type]['placement'] && (empty($placement) || $placement === 'Universal')) {
                $placement = $typeToCatSlug[$type]['placement'];
            }
        }

        // Contextual auto-linking for 'logo' standard type based on chosen placement
        if ($type === 'logo') {
            if (stripos($placement ?? '', 'Front') !== false) {
                $frontCat = Category::where('slug', 'front-logo')->first();
                if ($frontCat && !in_array($frontCat->id, $catIds)) {
                    $catIds[] = $frontCat->id;
                }
            } elseif (stripos($placement ?? '', 'Rear') !== false) {
                $rearCat = Category::where('slug', 'rear-logo')->first();
                if ($rearCat && !in_array($rearCat->id, $catIds)) {
                    $catIds[] = $rearCat->id;
                }
            }
        }

        if ($primaryCatId && !in_array($primaryCatId, $catIds)) {
            $catIds[] = (int) $primaryCatId;
        }

        $isNewArrival = $request->boolean('is_new_arrival') || $type === 'new_arrival';
        $newArrivalCat = Category::where('slug', 'new-arrivals')->first();
        if ($newArrivalCat && ($primaryCatId == $newArrivalCat->id || in_array($newArrivalCat->id, $catIds))) {
            $isNewArrival = true;
            if (!in_array($newArrivalCat->id, $catIds)) {
                $catIds[] = $newArrivalCat->id;
            }
        }

        $product = Product::create([
            'brand_id' => $brandId,
            'car_model_id' => $carModelId,
            'model_variant_id' => $variantId,
            'primary_category_id' => $primaryCatId,
            'name' => $request->name,
            'slug' => $slug,
            'sku' => strtoupper(trim($request->sku)),
            'product_type' => $type,
            'placement' => $placement,
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
            'hsn_sac' => $request->filled('hsn_code') ? $request->hsn_code : ($request->filled('hsn_sac') ? $request->hsn_sac : '8708'),
            'is_featured' => $request->boolean('is_featured'),
            'is_new_arrival' => $isNewArrival,
            'is_best_seller' => $request->boolean('is_best_seller'),
            'is_monogram' => $request->boolean('is_monogram') || $type === 'monogram' || $type === 'full_monogram_kit',
            'status' => $request->boolean('status'),
            'sort_order' => $request->sort_order ?? 0,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description ?: $request->short_description,
            'meta_keywords' => $request->meta_keywords,
            'amazon_url' => $normalizeMarketplaceUrl($request->amazon_url),
            'flipkart_url' => $normalizeMarketplaceUrl($request->flipkart_url),
        ]);

        if (!empty($catIds)) {
            $product->categories()->sync($catIds);
        }

        // Upload Product Images
        if ($request->hasFile('images')) {
            $isPrimary = true;
            $order = 0;
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $imagePath = FileUploadService::upload($file, 'products');
                    $product->images()->create([
                        'image_path' => $imagePath,
                        'is_primary' => $isPrimary,
                        'sort_order' => $order++,
                    ]);
                    $isPrimary = false;
                }
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
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();
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
            'hsn_code' => 'nullable|string|max:50',
            'hsn_sac' => 'nullable|string|max:50',
            'amazon_url' => 'nullable|string|max:1000',
            'flipkart_url' => 'nullable|string|max:1000',
            'images.*' => 'nullable|file|max:20480',
        ]);

        $normalizeMarketplaceUrl = function ($url) {
            if (empty($url)) return null;
            $url = trim($url);
            if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                $url = 'https://' . $url;
            }
            return $url;
        };

        $brandId = $request->brand_id;
        $carModelId = $request->car_model_id;
        $variantId = $request->model_variant_id;

        // Auto-resolve hierarchy gracefully without throwing blocking errors
        if ($variantId) {
            $variant = ModelVariant::with('carModel')->find($variantId);
            if ($variant) {
                $carModelId = $variant->car_model_id;
                $brandId = $variant->carModel?->brand_id ?? $brandId;
            }
        } elseif ($carModelId) {
            $carModel = CarModel::find($carModelId);
            if ($carModel) {
                $brandId = $carModel->brand_id;
            }
        }

        $type = $request->product_type;
        $placement = $request->placement;
        $primaryCatId = $request->primary_category_id;
        $catIds = (array) ($request->category_ids ?? []);

        // Smart Mapping from Product Type to Category, Placement & Flags
        $typeToCatSlug = [
            'front_logo' => ['slug' => 'front-logo', 'placement' => 'Front Grille'],
            'rear_logo' => ['slug' => 'rear-logo', 'placement' => 'Rear Trunk'],
            'bonnet_letters' => ['slug' => 'bonnet-tailgate-letters', 'placement' => 'Bonnet / Boot'],
            'full_monogram_kit' => ['slug' => 'kit-full-monogram', 'placement' => 'Full Exterior Kit'],
            'single_badge' => ['slug' => 'single-name-badge', 'placement' => null],
            'new_arrival' => ['slug' => 'new-arrivals', 'placement' => null],
            // Standard / Legacy Types mapped to appropriate Catalog & Header categories
            'logo' => ['slug' => 'car-logos', 'placement' => null],
            'emblem' => ['slug' => 'single-name-badge', 'secondary_slug' => 'model-logos-badges', 'placement' => null],
            'monogram' => ['slug' => 'bonnet-tailgate-letters', 'secondary_slug' => 'car-monograms', 'placement' => 'Bonnet / Boot'],
            'accessory' => ['slug' => 'accessories', 'placement' => 'Universal'],
        ];

        if (isset($typeToCatSlug[$type])) {
            $matchedCat = Category::where('slug', $typeToCatSlug[$type]['slug'])->first();
            if ($matchedCat) {
                if (!$primaryCatId) {
                    $primaryCatId = $matchedCat->id;
                }
                if (!in_array($matchedCat->id, $catIds)) {
                    $catIds[] = $matchedCat->id;
                }
            }
            if (!empty($typeToCatSlug[$type]['secondary_slug'])) {
                $secCat = Category::where('slug', $typeToCatSlug[$type]['secondary_slug'])->first();
                if ($secCat && !in_array($secCat->id, $catIds)) {
                    $catIds[] = $secCat->id;
                }
            }
            if ($typeToCatSlug[$type]['placement'] && (empty($placement) || $placement === 'Universal')) {
                $placement = $typeToCatSlug[$type]['placement'];
            }
        }

        // Contextual auto-linking for 'logo' standard type based on chosen placement
        if ($type === 'logo') {
            if (stripos($placement ?? '', 'Front') !== false) {
                $frontCat = Category::where('slug', 'front-logo')->first();
                if ($frontCat && !in_array($frontCat->id, $catIds)) {
                    $catIds[] = $frontCat->id;
                }
            } elseif (stripos($placement ?? '', 'Rear') !== false) {
                $rearCat = Category::where('slug', 'rear-logo')->first();
                if ($rearCat && !in_array($rearCat->id, $catIds)) {
                    $catIds[] = $rearCat->id;
                }
            }
        }

        if ($primaryCatId && !in_array($primaryCatId, $catIds)) {
            $catIds[] = (int) $primaryCatId;
        }

        $isNewArrival = $request->boolean('is_new_arrival') || $type === 'new_arrival';
        $newArrivalCat = Category::where('slug', 'new-arrivals')->first();
        if ($newArrivalCat && ($primaryCatId == $newArrivalCat->id || in_array($newArrivalCat->id, $catIds))) {
            $isNewArrival = true;
            if (!in_array($newArrivalCat->id, $catIds)) {
                $catIds[] = $newArrivalCat->id;
            }
        }

        $product->update([
            'brand_id' => $brandId,
            'car_model_id' => $carModelId,
            'model_variant_id' => $variantId,
            'primary_category_id' => $primaryCatId,
            'name' => $request->name,
            'sku' => strtoupper(trim($request->sku)),
            'product_type' => $type,
            'placement' => $placement,
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
            'hsn_sac' => $request->filled('hsn_code') ? $request->hsn_code : ($request->filled('hsn_sac') ? $request->hsn_sac : '8708'),
            'is_featured' => $request->boolean('is_featured'),
            'is_new_arrival' => $isNewArrival,
            'is_best_seller' => $request->boolean('is_best_seller'),
            'is_monogram' => $request->boolean('is_monogram') || $type === 'monogram' || $type === 'full_monogram_kit',
            'status' => $request->boolean('status'),
            'sort_order' => $request->sort_order ?? 0,
            'meta_title' => $request->meta_title ?: $request->name,
            'meta_description' => $request->meta_description ?: $request->short_description,
            'meta_keywords' => $request->meta_keywords,
            'amazon_url' => $normalizeMarketplaceUrl($request->amazon_url),
            'flipkart_url' => $normalizeMarketplaceUrl($request->flipkart_url),
        ]);

        if (!empty($catIds)) {
            $product->categories()->sync($catIds);
        }

        // Upload additional images
        if ($request->hasFile('images')) {
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            $order = ($product->images()->max('sort_order') ?? 0) + 1;
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $imagePath = FileUploadService::upload($file, 'products');
                    $product->images()->create([
                        'image_path' => $imagePath,
                        'is_primary' => !$hasPrimary,
                        'sort_order' => $order++,
                    ]);
                    $hasPrimary = true;
                }
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
