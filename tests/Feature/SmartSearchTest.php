<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\ModelVariant;
use App\Models\Product;
use Tests\TestCase;

class SmartSearchTest extends TestCase
{
    public function test_multi_word_tokenized_search_finds_products_without_exact_full_name(): void
    {
        $unique = 'test_' . uniqid();
        $brand = Brand::create(['name' => "Brand {$unique}", 'slug' => "brand-{$unique}", 'status' => true]);
        $car = CarModel::create(['brand_id' => $brand->id, 'name' => "Model {$unique}", 'slug' => "model-{$unique}", 'status' => true]);
        $variant = ModelVariant::create(['car_model_id' => $car->id, 'name' => "Variant {$unique}", 'slug' => "variant-{$unique}", 'status' => true]);

        $product = Product::create([
            'brand_id' => $brand->id,
            'car_model_id' => $car->id,
            'model_variant_id' => $variant->id,
            'name' => "3D Chrome Finish Logo Monogram Compatible Fits {$unique} Name Badge",
            'slug' => "product-{$unique}",
            'sku' => strtoupper(substr("SKU{$unique}", 0, 15)),
            'product_type' => 'emblem',
            'regular_price' => 499,
            'stock_quantity' => 10,
            'status' => true,
        ]);

        try {
            // Search with multi-word: "{$unique} badge"
            $results = Product::active()->search("{$unique} badge")->get();
            $this->assertTrue($results->contains('id', $product->id));

            // Test web route /shop with query
            $response = $this->get('/shop?q=' . urlencode("{$unique} badge"));
            $response->assertStatus(200);
            $response->assertSee($unique);

            // Test search suggestions API
            $apiResponse = $this->getJson('/api/search/suggestions?q=' . urlencode("{$unique} badge"));
            $apiResponse->assertStatus(200);
            $apiResponse->assertJsonFragment(['id' => $product->id]);
        } finally {
            $product->delete();
            $variant->delete();
            $car->delete();
            $brand->delete();
        }
    }
}
