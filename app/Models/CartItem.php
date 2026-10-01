<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function getUnitPriceAttribute(): float
    {
        if ($this->variant) {
            return (float) $this->variant->active_price;
        }
        return (float) $this->product->active_price;
    }

    public function getTotalPriceAttribute(): float
    {
        return (float) ($this->unit_price * $this->quantity);
    }
}
