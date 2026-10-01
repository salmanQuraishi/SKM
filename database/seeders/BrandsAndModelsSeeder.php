<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandsAndModelsSeeder extends Seeder
{
    public function run(): void
    {
        $brandsData = [
            [
                'name' => 'Hyundai',
                'slug' => 'hyundai',
                'logo' => 'images/brands/hyundai.svg',
                'description' => 'Original chrome and matte finish emblems, logos and monograms for Hyundai cars.',
                'sort_order' => 1,
                'models' => [
                    ['name' => 'Creta', 'slug' => 'creta', 'is_popular' => true, 'badge_image' => 'images/models/creta.svg', 'sort_order' => 1],
                    ['name' => 'Verna', 'slug' => 'verna', 'is_popular' => true, 'badge_image' => 'images/models/verna.svg', 'sort_order' => 2],
                    ['name' => 'Venue', 'slug' => 'venue', 'is_popular' => true, 'badge_image' => 'images/models/venue.svg', 'sort_order' => 3],
                    ['name' => 'i20', 'slug' => 'i20', 'is_popular' => true, 'badge_image' => 'images/models/i20.svg', 'sort_order' => 4],
                    ['name' => 'i10', 'slug' => 'i10', 'is_popular' => true, 'badge_image' => 'images/models/i10.svg', 'sort_order' => 5],
                    ['name' => 'Alcazar', 'slug' => 'alcazar', 'is_popular' => false, 'sort_order' => 6],
                    ['name' => 'Tucson', 'slug' => 'tucson', 'is_popular' => false, 'sort_order' => 7],
                ]
            ],
            [
                'name' => 'Maruti Suzuki',
                'slug' => 'maruti-suzuki',
                'logo' => 'images/brands/maruti-suzuki.svg',
                'description' => 'Genuine Suzuki 3D chrome S emblems, Swift, Baleno, Brezza, and WagonR badges.',
                'sort_order' => 2,
                'models' => [
                    ['name' => 'Swift', 'slug' => 'swift', 'is_popular' => true, 'badge_image' => 'images/models/swift.svg', 'sort_order' => 1],
                    ['name' => 'Baleno', 'slug' => 'baleno', 'is_popular' => true, 'badge_image' => 'images/models/baleno.svg', 'sort_order' => 2],
                    ['name' => 'Brezza', 'slug' => 'brezza', 'is_popular' => true, 'badge_image' => 'images/models/brezza.svg', 'sort_order' => 3],
                    ['name' => 'WagonR', 'slug' => 'wagonr', 'is_popular' => true, 'badge_image' => 'images/models/wagonr.svg', 'sort_order' => 4],
                    ['name' => 'Dzire', 'slug' => 'dzire', 'is_popular' => false, 'sort_order' => 5],
                    ['name' => 'Ertiga', 'slug' => 'ertiga', 'is_popular' => false, 'sort_order' => 6],
                    ['name' => 'Grand Vitara', 'slug' => 'grand-vitara', 'is_popular' => false, 'sort_order' => 7],
                ]
            ],
            [
                'name' => 'Tata',
                'slug' => 'tata',
                'logo' => 'images/brands/tata.svg',
                'description' => 'Premium Tata Motors grille emblems, Harrier, Safari, Nexon, Punch and Altroz chrome badges.',
                'sort_order' => 3,
                'models' => [
                    ['name' => 'Nexon', 'slug' => 'nexon', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => 'Punch', 'slug' => 'punch', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'Harrier', 'slug' => 'harrier', 'is_popular' => false, 'sort_order' => 3],
                    ['name' => 'Safari', 'slug' => 'safari', 'is_popular' => false, 'sort_order' => 4],
                    ['name' => 'Altroz', 'slug' => 'altroz', 'is_popular' => false, 'sort_order' => 5],
                    ['name' => 'Tiago', 'slug' => 'tiago', 'is_popular' => false, 'sort_order' => 6],
                ]
            ],
            [
                'name' => 'Honda',
                'slug' => 'honda',
                'logo' => 'images/brands/honda.svg',
                'description' => 'Original chrome and red H logo badges for Honda City, Amaze, Elevate and Civic.',
                'sort_order' => 4,
                'models' => [
                    ['name' => 'Amaze', 'slug' => 'amaze', 'is_popular' => true, 'badge_image' => 'images/models/amaze.svg', 'sort_order' => 1],
                    ['name' => 'City', 'slug' => 'city', 'is_popular' => true, 'badge_image' => 'images/models/city.svg', 'sort_order' => 2],
                    ['name' => 'Elevate', 'slug' => 'elevate', 'is_popular' => false, 'sort_order' => 3],
                    ['name' => 'Civic', 'slug' => 'civic', 'is_popular' => false, 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Toyota',
                'slug' => 'toyota',
                'logo' => 'images/brands/toyota.svg',
                'description' => 'High quality Toyota emblems, Fortuner bonnet letters, Innova Crysta, and Hycross badges.',
                'sort_order' => 5,
                'models' => [
                    ['name' => 'Fortuner', 'slug' => 'fortuner', 'is_popular' => true, 'badge_image' => 'images/models/fortuner.svg', 'sort_order' => 1],
                    ['name' => 'Innova Crysta', 'slug' => 'innova-crysta', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'Urban Cruiser', 'slug' => 'urban-cruiser', 'is_popular' => false, 'sort_order' => 3],
                    ['name' => 'Glanza', 'slug' => 'glanza', 'is_popular' => false, 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Mahindra',
                'slug' => 'mahindra',
                'logo' => 'images/brands/mahindra.svg',
                'description' => 'Mahindra Twin Peaks new chrome logos, Thar 4x4 metal badges, Scorpio-N, and XUV700 lettering.',
                'sort_order' => 6,
                'models' => [
                    ['name' => 'Scorpio', 'slug' => 'scorpio', 'is_popular' => true, 'badge_image' => 'images/models/scorpio.svg', 'sort_order' => 1],
                    ['name' => 'Thar', 'slug' => 'thar', 'is_popular' => true, 'badge_image' => 'images/models/thar.svg', 'sort_order' => 2],
                    ['name' => 'XUV700', 'slug' => 'xuv700', 'is_popular' => false, 'sort_order' => 3],
                    ['name' => 'XUV300', 'slug' => 'xuv300', 'is_popular' => false, 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Kia',
                'slug' => 'kia',
                'logo' => 'images/brands/kia.svg',
                'description' => 'Genuine Kia new font silhouette badges for Seltos, Sonet, Carens and EV6.',
                'sort_order' => 7,
                'models' => [
                    ['name' => 'Seltos', 'slug' => 'seltos', 'is_popular' => true, 'badge_image' => 'images/models/seltos.svg', 'sort_order' => 1],
                    ['name' => 'Sonet', 'slug' => 'sonet', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'Carens', 'slug' => 'carens', 'is_popular' => false, 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'Ford',
                'slug' => 'ford',
                'logo' => 'images/brands/ford.svg',
                'description' => 'Blue Oval emblems and Endeavour / EcoSport 3D chrome badges.',
                'sort_order' => 8,
                'models' => [
                    ['name' => 'Endeavour', 'slug' => 'endeavour', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => 'EcoSport', 'slug' => 'ecosport', 'is_popular' => false, 'sort_order' => 2],
                ]
            ],
            [
                'name' => 'Volkswagen',
                'slug' => 'volkswagen',
                'logo' => 'images/brands/volkswagen.svg',
                'description' => 'Original VW chrome front grille and tailgate badges for Virtus, Taigun and Polo.',
                'sort_order' => 9,
                'models' => [
                    ['name' => 'Virtus', 'slug' => 'virtus', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => 'Taigun', 'slug' => 'taigun', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'Polo', 'slug' => 'polo', 'is_popular' => false, 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'BMW',
                'slug' => 'bmw',
                'logo' => 'images/brands/bmw.svg',
                'description' => 'Original BMW 82mm & 74mm Roundel hood and trunk emblems, M badges and wheel caps.',
                'sort_order' => 10,
                'models' => [
                    ['name' => '3 Series', 'slug' => '3-series', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => '5 Series', 'slug' => '5-series', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'X1', 'slug' => 'x1', 'is_popular' => false, 'sort_order' => 3],
                    ['name' => 'X5', 'slug' => 'x5', 'is_popular' => false, 'sort_order' => 4],
                ]
            ],
        ];

        foreach ($brandsData as $bData) {
            $models = $bData['models'] ?? [];
            unset($bData['models']);

            $brand = Brand::updateOrCreate(['slug' => $bData['slug']], $bData);

            foreach ($models as $mData) {
                CarModel::updateOrCreate(
                    ['brand_id' => $brand->id, 'slug' => $mData['slug']],
                    [
                        'name' => $mData['name'],
                        'is_popular' => $mData['is_popular'] ?? false,
                        'badge_image' => $mData['badge_image'] ?? null,
                        'sort_order' => $mData['sort_order'] ?? 0,
                        'status' => true,
                    ]
                );
            }
        }
    }
}
