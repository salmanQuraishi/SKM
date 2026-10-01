<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'billing_address',
        'shipping_address',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'tax_amount',
        'shipping_amount',
        'cod_charge',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'courier_name',
        'tracking_number',
        'expected_delivery_date',
        'customer_notes',
        'admin_notes',
        'cancelled_reason',
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'cod_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expected_delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->order_status) {
            'pending' => 'bg-warning text-dark',
            'confirmed' => 'bg-info text-white',
            'processing' => 'bg-primary text-white',
            'packed' => 'bg-secondary text-white',
            'shipped' => 'bg-info text-dark',
            'out_for_delivery' => 'bg-primary text-white',
            'delivered' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            'returned', 'refunded' => 'bg-dark text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function getPaymentBadgeClassAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-success text-white',
            'pending' => 'bg-warning text-dark',
            'failed' => 'bg-danger text-white',
            'refunded' => 'bg-dark text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function getFormattedShippingAddressAttribute(): string
    {
        if (is_array($this->shipping_address)) {
            $addr = $this->shipping_address;
            $parts = [
                $addr['address_line_1'] ?? '',
                $addr['address_line_2'] ?? '',
                $addr['landmark'] ?? '',
                $addr['city'] ?? '',
                ($addr['state'] ?? '') . ' - ' . ($addr['pincode'] ?? ''),
                $addr['country'] ?? 'India',
            ];
            return implode(', ', array_filter($parts));
        }
        return (string) $this->shipping_address;
    }
}
