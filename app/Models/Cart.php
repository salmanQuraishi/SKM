<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'session_id', 'coupon_code'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            $price = $item->variant ? $item->variant->active_price : $item->product->active_price;
            return $price * $item->quantity;
        });
    }

    public function getTotalCountAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
