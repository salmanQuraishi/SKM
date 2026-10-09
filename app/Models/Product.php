<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'car_model_id',
        'model_variant_id',
        'primary_category_id',
        'name',
        'slug',
        'sku',
        'product_type',
        'placement',
        'finish',
        'compatible_models_text',
        'short_description',
        'full_description',
        'specifications',
        'regular_price',
        'sale_price',
        'cost_price',
        'stock_quantity',
        'min_stock_alert',
        'weight',
        'length',
        'width',
        'height',
        'shipping_class',
        'tax_rate',
        'hsn_sac',
        'is_featured',
        'is_new_arrival',
        'is_best_seller',
        'is_monogram',
        'status',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'specifications' => 'array',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock_quantity' => 'integer',
        'min_stock_alert' => 'integer',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_monogram' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function company()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }

    public function modelVariant()
    {
        return $this->belongsTo(ModelVariant::class, 'model_variant_id');
    }

    public function carVariant()
    {
        return $this->belongsTo(ModelVariant::class, 'model_variant_id');
    }

    public function getVehicleHierarchyTextAttribute(): string
    {
        $parts = [];
        if ($this->brand) {
            $parts[] = $this->brand->name;
        }
        if ($this->carModel) {
            $parts[] = $this->carModel->name;
        }
        if ($this->modelVariant) {
            $parts[] = $this->modelVariant->name;
        }

        return !empty($parts) ? implode(' > ', $parts) : ($this->compatible_models_text ?: 'All Models');
    }

    public function scopeForVariant($query, $variantId)
    {
        return $query->where('model_variant_id', $variantId);
    }

    public function primaryCategory()
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('is_primary', 'desc')->orderBy('sort_order', 'asc');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function allReviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getActivePriceAttribute()
    {
        return ($this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->regular_price)
            ? $this->sale_price
            : $this->regular_price;
    }

    public function getFinalPriceAttribute()
    {
        return $this->active_price;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->regular_price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->has_discount && $this->regular_price > 0) {
            return (int) round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
        }
        return 0;
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : 5.0;
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $img = $this->images->first();
        if ($img && !empty($img->image_path)) {
            $path = $img->image_path;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            $cleanPath = ltrim(str_replace('\\', '/', $path), '/');

            if (str_starts_with($cleanPath, 'images/')) {
                return asset($cleanPath);
            }

            if (str_starts_with($cleanPath, 'storage/')) {
                return asset($cleanPath);
            }

            if (str_starts_with($cleanPath, 'products/') || str_starts_with($cleanPath, 'monograms/') || str_starts_with($cleanPath, 'brands/') || str_starts_with($cleanPath, 'categories/')) {
                return asset('storage/' . $cleanPath);
            }

            return asset($cleanPath);
        }
        return asset('images/dummy-product.svg');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeBestSellers($query)
    {
        return $query->where('is_best_seller', true);
    }

    public function scopeMonograms($query)
    {
        return $query->where('is_monogram', true);
    }

    /**
     * Smart tokenized search across product fields and relations.
     * Allows multi-word partial matching without requiring the exact full phrase.
     */
    public function scopeSearch($query, ?string $searchTerm)
    {
        $term = trim($searchTerm ?? '');
        if ($term === '') {
            return $query;
        }

        $tokens = array_filter(preg_split('/\s+/', $term), fn($t) => mb_strlen($t) >= 2);
        if (empty($tokens)) {
            $tokens = [$term];
        }

        foreach ($tokens as $token) {
            $query->where(function ($wq) use ($token) {
                $wq->where('products.name', 'LIKE', "%{$token}%")
                   ->orWhere('products.sku', 'LIKE', "%{$token}%")
                   ->orWhere('products.compatible_models_text', 'LIKE', "%{$token}%")
                   ->orWhere('products.short_description', 'LIKE', "%{$token}%")
                   ->orWhere('products.placement', 'LIKE', "%{$token}%")
                   ->orWhere('products.finish', 'LIKE', "%{$token}%")
                   ->orWhereHas('brand', fn($bq) => $bq->where('name', 'LIKE', "%{$token}%"))
                   ->orWhereHas('carModel', fn($mq) => $mq->where('name', 'LIKE', "%{$token}%"))
                   ->orWhereHas('modelVariant', fn($vq) => $vq->where('name', 'LIKE', "%{$token}%"))
                   ->orWhereHas('categories', fn($cq) => $cq->where('name', 'LIKE', "%{$token}%"));
            });
        }

        return $query;
    }

    /**
     * Rank search results by relevance: exact match in name > all tokens in name > partial matches.
     */
    public function scopeOrderByRelevance($query, ?string $searchTerm)
    {
        $term = trim($searchTerm ?? '');
        if ($term === '') {
            return $query->latest('products.id');
        }

        $tokens = array_filter(preg_split('/\s+/', $term), fn($t) => mb_strlen($t) >= 2);
        if (empty($tokens)) {
            $tokens = [$term];
        }

        $tokenNameConditions = [];
        $tokenBindings = [];
        foreach ($tokens as $t) {
            $tokenNameConditions[] = "CASE WHEN products.name LIKE ? THEN 1 ELSE 0 END";
            $tokenBindings[] = "%{$t}%";
        }
        $tokenScoreSql = implode(' + ', $tokenNameConditions);

        $firstToken = $tokens[0] ?? $term;
        $bindings = array_merge(
            ["{$term}%", "%{$term}%"], 
            $tokenBindings, 
            ["%{$firstToken}%", "%{$firstToken}%"]
        );

        return $query->orderByRaw("
            CASE 
                WHEN products.name LIKE ? THEN 100
                WHEN products.name LIKE ? THEN 80
                ELSE (({$tokenScoreSql}) * 20)
            END + 
            CASE 
                WHEN products.name LIKE ? THEN 10
                WHEN products.sku LIKE ? THEN 5
                ELSE 0
            END DESC",
            $bindings
        )->orderBy('products.id', 'desc');
    }
}
