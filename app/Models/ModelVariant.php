<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'car_model_id',
        'name',
        'slug',
        'image',
        'status',
        'sort_order',
        'year_from',
        'year_to',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'year_from' => 'integer',
        'year_to' => 'integer',
    ];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'model_variant_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getBrandAttribute()
    {
        return $this->carModel?->brand;
    }

    public function getCompanyAttribute()
    {
        return $this->carModel?->brand;
    }

    public function getFullNameAttribute(): string
    {
        $brandName = $this->carModel?->brand?->name;
        $modelName = $this->carModel?->name;
        $variantName = $this->name;

        if ($brandName && $modelName) {
            return "{$brandName} {$modelName} - {$variantName}";
        }

        return $modelName ? "{$modelName} - {$variantName}" : $variantName;
    }
}
