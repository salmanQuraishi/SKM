<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/dummy-product.svg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            $parsedPath = parse_url($this->image_path, PHP_URL_PATH);
            $rel = ltrim($parsedPath ?? '', '/');
            if ($rel && (file_exists(public_path($rel)) || file_exists(storage_path('app/public/' . preg_replace('#^storage/#', '', $rel))))) {
                return asset($rel);
            }
            return $this->image_path;
        }

        return asset(ltrim($this->image_path, '/'));
    }
}
