<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hero Slide 1
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
                'secondary_button_url' => '/shop?category=kit-full-monogram',
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

        // 2. Hero Slide 2
        Banner::updateOrCreate(
            ['title' => 'PREMIUM 3D CHROME & GLOSS MONOGRAMS'],
            [
                'subtitle' => 'EXCLUSIVE LETTERING & NAMEPLATES',
                'badge_text' => 'OEM Factory Grade',
                'image_path' => 'images/hero_monogram.svg',
                'car_image_path' => 'images/hero_monogram.svg',
                'button_text' => 'EXPLORE MONOGRAMS >',
                'button_url' => '/shop?category=kit-full-monogram',
                'secondary_button_text' => 'VIEW ALL PRODUCTS',
                'secondary_button_url' => '/shop',
                'position' => 'hero',
                'features_json' => [
                    'Laser-Cut Precision Fitment',
                    'Pre-Applied Genuine 3M Automotive Tape',
                    'UV & High-Pressure Wash Resistant',
                    'Effortless 5-Minute DIY Peel & Stick'
                ],
                'status' => true,
                'sort_order' => 2,
            ]
        );

        // 3. Hero Slide 3
        Banner::updateOrCreate(
            ['title' => 'ORIGINAL CAR EMBLEMS & BADGES'],
            [
                'subtitle' => 'HYUNDAI, TATA, MAHINDRA, SUZUKI & MORE',
                'badge_text' => 'Direct OEM Fitment',
                'image_path' => 'images/hero_suv.svg',
                'car_image_path' => 'images/hero_suv.svg',
                'button_text' => 'SELECT YOUR VEHICLE >',
                'button_url' => '/vehicles',
                'secondary_button_text' => 'CONTACT SUPPORT',
                'secondary_button_url' => '/contact',
                'position' => 'hero',
                'features_json' => [
                    'Exact OEM Dimensions & Clips',
                    'Front Grille & Rear Tailgate Badges',
                    'Mirror Chrome & Piano Gloss Black',
                    'Insured Express Delivery Across India'
                ],
                'status' => true,
                'sort_order' => 3,
            ]
        );

        // 4. Middle Promo Strip
        Banner::updateOrCreate(
            ['title' => "CAN'T FIND YOUR LOGO?"],
            [
                'subtitle' => 'We have 1000+ logos, monograms and emblems for all cars. If you can\'t find, just contact us.',
                'badge_text' => 'Custom Badges Available',
                'image_path' => null,
                'car_image_path' => null,
                'button_text' => 'REQUEST YOUR LOGO',
                'button_url' => '/contact',
                'position' => 'promo',
                'features_json' => null,
                'status' => true,
                'sort_order' => 4,
            ]
        );
    }
}
