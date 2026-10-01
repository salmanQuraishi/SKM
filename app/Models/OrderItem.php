<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'product_sku',
        'variant_name',
        'price',
        'quantity',
        'tax_rate',
        'tax_amount',
        'total',
        'product_image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function getImageUrlAttribute(): string
    {
        // 1. If product relation exists and has a valid primary image
        if ($this->relationLoaded('product') && $this->product) {
            $prodImg = $this->product->primary_image_url;
            if (!empty($prodImg) && !str_contains($prodImg, 'dummy-product') && !str_contains($prodImg, 'placeholder-emblem')) {
                return $prodImg;
            }
        }

        // 2. Check stored product_image in order item
        if (!empty($this->product_image)) {
            $img = $this->product_image;
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                $parsedPath = parse_url($img, PHP_URL_PATH);
                $rel = ltrim($parsedPath ?? '', '/');
                if ($rel && file_exists(public_path($rel))) {
                    return asset($rel);
                }
                return $img;
            }

            $cleanPath = ltrim($img, '/');
            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }
        }

        // 3. Fallback to product primary image
        if ($this->product && !empty($this->product->primary_image_url)) {
            return $this->product->primary_image_url;
        }

        // 4. Default dummy product badge
        return asset('images/dummy-product.svg');
    }

    public function getUnitPriceAttribute()
    {
        return $this->attributes['price'] ?? 0;
    }

    public function getTotalPriceAttribute()
    {
        return $this->attributes['total'] ?? 0;
    }

    public function getSkuAttribute()
    {
        return $this->attributes['product_sku'] ?? null;
    }
}
