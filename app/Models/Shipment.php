<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipment_number',
        'courier_name',
        'tracking_number',
        'tracking_url',
        'shipping_date',
        'estimated_delivery',
        'shipment_status',
        'shipping_slip_path',
    ];

    protected $casts = [
        'shipping_date' => 'date',
        'estimated_delivery' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
