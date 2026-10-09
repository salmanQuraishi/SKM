<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesData = [
            [
                'name' => 'Car Logos',
                'slug' => 'car-logos',
                'icon' => 'fa-car-side',
                'description' => 'Original front and rear chrome car brand logos and emblems.',
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Hyundai Logos', 'slug' => 'hyundai-logos', 'sort_order' => 1],
                    ['name' => 'Honda Logos', 'slug' => 'honda-logos', 'sort_order' => 2],
                    ['name' => 'Toyota Logos', 'slug' => 'toyota-logos', 'sort_order' => 3],
                    ['name' => 'Tata Logos', 'slug' => 'tata-logos', 'sort_order' => 4],
                    ['name' => 'Maruti Suzuki Logos', 'slug' => 'maruti-suzuki-logos', 'sort_order' => 5],
                    ['name' => 'Mahindra Logos', 'slug' => 'mahindra-logos', 'sort_order' => 6],
                    ['name' => 'Kia Logos', 'slug' => 'kia-logos', 'sort_order' => 7],
                    ['name' => 'BMW Logos', 'slug' => 'bmw-logos', 'sort_order' => 8],
                    ['name' => 'Volkswagen Logos', 'slug' => 'volkswagen-logos', 'sort_order' => 9],
                    ['name' => 'Skoda Logos', 'slug' => 'skoda-logos', 'sort_order' => 10],
                ]
            ],
            [
                'name' => 'Model Logos / Badges',
                'slug' => 'model-logos-badges',
                'icon' => 'fa-certificate',
                'description' => 'Trunk, fender and tailgate car model nameplate badges.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Car Monograms',
                'slug' => 'car-monograms',
                'icon' => 'fa-bolt',
                'description' => 'High performance sports badges like AMG, M Power, RS, Type R, TRD, VIP and S6.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Letters & Numbers',
                'slug' => 'letters-numbers',
                'icon' => 'fa-font',
                'description' => 'Individual 3D chrome letters A-Z and numbers 0-9 for custom trunk naming.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Front & Rear Emblems',
                'slug' => 'front-rear-emblems',
                'icon' => 'fa-shield-halved',
                'description' => 'Direct replacement OE fit front radiator grille and rear boot lid emblems.',
                'sort_order' => 5,
            ],
            [
                'name' => '3D Chrome Emblems',
                'slug' => '3d-chrome-emblems',
                'icon' => 'fa-gem',
                'description' => 'Heavy duty triple-plated 3D chrome emblems with 3M automotive adhesive.',
                'sort_order' => 6,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'icon' => 'fa-wrench',
                'description' => 'Steering wheel center rings, wheel hub caps, keychains and emblem removal tools.',
                'sort_order' => 7,
            ],
        ];

        foreach ($categoriesData as $cData) {
            $children = $cData['children'] ?? [];
            unset($cData['children']);

            $parent = Category::updateOrCreate(['slug' => $cData['slug']], $cData);

            foreach ($children as $ch) {
                Category::updateOrCreate(
                    ['slug' => $ch['slug']],
                    [
                        'name' => $ch['name'],
                        'parent_id' => $parent->id,
                        'sort_order' => $ch['sort_order'] ?? 0,
                        'status' => true,
                    ]
                );
            }
        }
    }
}
