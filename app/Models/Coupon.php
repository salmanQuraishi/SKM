<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'used_count',
        'per_customer_limit',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'per_customer_limit' => 'integer',
        'status' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_products');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupon_categories');
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isValidForSubtotal(float $subtotal, ?int $userId = null): array
    {
        if (!$this->status) {
            return ['valid' => false, 'message' => 'This coupon is inactive.'];
        }

        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return ['valid' => false, 'message' => 'This coupon is not active yet.'];
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Coupon usage limit reached.'];
        }

        if ($this->min_order_amount && $subtotal < $this->min_order_amount) {
            return ['valid' => false, 'message' => "Minimum order amount of &#x20B9;{$this->min_order_amount} required to use this coupon."];
        }

        if ($userId && $this->per_customer_limit) {
            $userUsage = $this->usages()->where('user_id', $userId)->count();
            if ($userUsage >= $this->per_customer_limit) {
                return ['valid' => false, 'message' => 'You have reached the maximum allowed usage for this coupon.'];
            }
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully!'];
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = $this->max_discount_amount;
            }
            return round($discount, 2);
        }

        return round(min($this->discount_value, $subtotal), 2);
    }
}
