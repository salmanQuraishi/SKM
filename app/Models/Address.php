<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'alternate_mobile',
        'email',
        'address_line_1',
        'address_line_2',
        'landmark',
        'city',
        'state',
        'pincode',
        'country',
        'address_type',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = [
            $this->address_line_1,
            $this->address_line_2,
            $this->landmark ? 'Near ' . $this->landmark : null,
            $this->city,
            $this->state . ' - ' . $this->pincode,
            $this->country,
        ];

        return implode(', ', array_filter($parts));
    }
}
