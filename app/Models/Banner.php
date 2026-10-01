<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'badge_text',
        'image_path',
        'car_image_path',
        'button_text',
        'button_url',
        'secondary_button_text',
        'secondary_button_url',
        'position',
        'features_json',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'features_json' => 'array',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeHero($query)
    {
        return $query->where('position', 'hero')->where('status', true)->orderBy('sort_order', 'asc');
    }
}
