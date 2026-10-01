<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'regular_price',
        'sale_price',
        'stock_quantity',
        'weight',
        'placement',
        'finish',
        'status',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock_quantity' => 'integer',
        'status' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getActivePriceAttribute()
    {
        return ($this->sale_price && $this->sale_price > 0 && $this->sale_price < $this->regular_price)
            ? $this->sale_price
            : $this->regular_price;
    }
}
