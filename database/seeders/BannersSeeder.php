<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannersSeeder extends Seeder
{
    public function run(): void
    {
        Banner::updateOrCreate(
            ['title' => 'GENUINE CAR LOGOS, MONOGRAMS & EMBLEMS'],
            [
                'subtitle' => 'FOR ALL BRANDS & MODELS',
                'badge_text' => '100% Genuine Products',
                'image_path' => 'images/hero_car.svg',
                'car_image_path' => 'images/hero_car.svg',
                'button_text' => 'SHOP NOW >',
                'button_url' => '/shop',
                'secondary_button_text' => 'VIEW MONOGRAMS',
                'secondary_button_url' => '/shop?type=monogram',
                'position' => 'hero',
                'features_json' => [
                    '100% Genuine Products',
                    'Perfect Fit For Your Car',
                    'Premium Chrome Finish',
                    'Easy Returns & Refunds'
                ],
                'status' => true,
                'sort_order' => 1,
            ]
        );

        Banner::updateOrCreate(
            ['title' => "CAN'T FIND YOUR LOGO?"],
            [
                'subtitle' => 'We have 1000+ logos, monograms and emblems for all cars. If you can\'t find, just contact us.',
                'badge_text' => 'Custom Badges Available',
                'button_text' => 'REQUEST YOUR LOGO',
                'button_url' => '/contact',
                'position' => 'promo',
                'status' => true,
                'sort_order' => 2,
            ]
        );
    }
}
