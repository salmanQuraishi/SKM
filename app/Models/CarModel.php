<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'badge_image',
        'is_popular',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
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

    public function variants()
    {
        return $this->hasMany(ModelVariant::class)->orderBy('sort_order', 'asc');
    }

    public function carVariants()
    {
        return $this->variants();
    }

    public function activeVariants()
    {
        return $this->hasMany(ModelVariant::class)->where('status', true)->orderBy('sort_order', 'asc');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }
}
