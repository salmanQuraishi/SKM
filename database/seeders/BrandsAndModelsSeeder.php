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
                'status' => true,
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
                'status' => true,
                'models' => [
                    ['name' => 'Car 800', 'slug' => 'car-800', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => 'A-Star', 'slug' => 'a-star', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'Alto', 'slug' => 'alto', 'is_popular' => true, 'sort_order' => 3],
                    ['name' => 'Alto 800', 'slug' => 'alto-800', 'is_popular' => true, 'sort_order' => 4],
                    ['name' => 'Alto K10', 'slug' => 'alto-k10', 'is_popular' => true, 'sort_order' => 5],
                    ['name' => 'Baleno', 'slug' => 'baleno', 'is_popular' => true, 'badge_image' => 'images/models/baleno.svg', 'sort_order' => 6],
                    ['name' => 'Brezza', 'slug' => 'brezza', 'is_popular' => true, 'badge_image' => 'images/models/brezza.svg', 'sort_order' => 7],
                    ['name' => 'Celerio', 'slug' => 'celerio', 'is_popular' => true, 'sort_order' => 8],
                    ['name' => 'Ciaz', 'slug' => 'ciaz', 'is_popular' => true, 'sort_order' => 9],
                    ['name' => 'Eeco', 'slug' => 'eeco', 'is_popular' => false, 'sort_order' => 10],
                    ['name' => 'Ertiga', 'slug' => 'ertiga', 'is_popular' => true, 'sort_order' => 11],
                    ['name' => 'Esteem', 'slug' => 'esteem', 'is_popular' => false, 'sort_order' => 12],
                    ['name' => 'Fronx', 'slug' => 'fronx', 'is_popular' => true, 'sort_order' => 13],
                    ['name' => 'Grand Vitara', 'slug' => 'grand-vitara', 'is_popular' => true, 'sort_order' => 14],
                    ['name' => 'Gypsy', 'slug' => 'gypsy', 'is_popular' => false, 'sort_order' => 15],
                    ['name' => 'Ignis', 'slug' => 'ignis', 'is_popular' => false, 'sort_order' => 16],
                    ['name' => 'Jimny', 'slug' => 'jimny', 'is_popular' => true, 'sort_order' => 17],
                    ['name' => 'Omni', 'slug' => 'omni', 'is_popular' => false, 'sort_order' => 18],
                    ['name' => 'Ritz', 'slug' => 'ritz', 'is_popular' => false, 'sort_order' => 19],
                    ['name' => 'S-Cross', 'slug' => 's-cross', 'is_popular' => false, 'sort_order' => 20],
                    ['name' => 'S-Presso', 'slug' => 's-presso', 'is_popular' => false, 'sort_order' => 21],
                    ['name' => 'Swift', 'slug' => 'swift', 'is_popular' => true, 'badge_image' => 'images/models/swift.svg', 'sort_order' => 22],
                    ['name' => 'Swift Dzire', 'slug' => 'dzire', 'is_popular' => true, 'sort_order' => 23],
                    ['name' => 'SX4', 'slug' => 'sx4', 'is_popular' => false, 'sort_order' => 24],
                    ['name' => 'Versa', 'slug' => 'versa', 'is_popular' => false, 'sort_order' => 25],
                    ['name' => 'Vitara Brezza', 'slug' => 'vitara-brezza', 'is_popular' => true, 'sort_order' => 26],
                    ['name' => 'WagonR', 'slug' => 'wagonr', 'is_popular' => true, 'badge_image' => 'images/models/wagonr.svg', 'sort_order' => 27],
                    ['name' => 'XL6', 'slug' => 'xl6', 'is_popular' => false, 'sort_order' => 28],
                    ['name' => 'Zen', 'slug' => 'zen', 'is_popular' => false, 'sort_order' => 29],
                    ['name' => 'Zen Estilo', 'slug' => 'zen-estilo', 'is_popular' => false, 'sort_order' => 30],
                ]
            ],
            [
                'name' => 'Tata',
                'slug' => 'tata',
                'logo' => 'images/brands/tata.svg',
                'description' => 'Premium Tata Motors grille emblems, Harrier, Safari, Nexon, Punch and Altroz chrome badges.',
                'sort_order' => 3,
                'status' => true,
                'models' => [
                    ['name' => 'Altroz', 'slug' => 'altroz', 'is_popular' => true, 'sort_order' => 1],
                    ['name' => 'Bolt', 'slug' => 'bolt', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'Curvv', 'slug' => 'curvv', 'is_popular' => true, 'sort_order' => 3],
                    ['name' => 'Harrier', 'slug' => 'harrier', 'is_popular' => true, 'sort_order' => 4],
                    ['name' => 'Hexa', 'slug' => 'hexa', 'is_popular' => false, 'sort_order' => 5],
                    ['name' => 'Indica', 'slug' => 'indica', 'is_popular' => false, 'sort_order' => 6],
                    ['name' => 'Indigo', 'slug' => 'indigo', 'is_popular' => false, 'sort_order' => 7],
                    ['name' => 'Nano', 'slug' => 'nano', 'is_popular' => false, 'sort_order' => 8],
                    ['name' => 'Nexon', 'slug' => 'nexon', 'is_popular' => true, 'sort_order' => 9],
                    ['name' => 'Punch', 'slug' => 'punch', 'is_popular' => true, 'sort_order' => 10],
                    ['name' => 'Safari', 'slug' => 'safari', 'is_popular' => true, 'sort_order' => 11],
                    ['name' => 'Safari Dicor', 'slug' => 'safari-dicor', 'is_popular' => false, 'sort_order' => 12],
                    ['name' => 'Safari Storme', 'slug' => 'safari-storme', 'is_popular' => false, 'sort_order' => 13],
                    ['name' => 'Tiago', 'slug' => 'tiago', 'is_popular' => true, 'sort_order' => 14],
                    ['name' => 'Tigor', 'slug' => 'tigor', 'is_popular' => false, 'sort_order' => 15],
                    ['name' => 'Zest', 'slug' => 'zest', 'is_popular' => false, 'sort_order' => 16],
                ]
            ],
            [
                'name' => 'Honda',
                'slug' => 'honda',
                'logo' => 'images/brands/honda.svg',
                'description' => 'Original chrome and red H logo badges for Honda City, Amaze, Elevate and Civic.',
                'sort_order' => 4,
                'status' => true,
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
                'status' => true,
                'models' => [
                    ['name' => 'Corolla Altis', 'slug' => 'corolla-altis', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => 'Etios', 'slug' => 'etios', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'Fortuner', 'slug' => 'fortuner', 'is_popular' => true, 'badge_image' => 'images/models/fortuner.svg', 'sort_order' => 3],
                    ['name' => 'Glanza', 'slug' => 'glanza', 'is_popular' => true, 'sort_order' => 4],
                    ['name' => 'Innova', 'slug' => 'innova', 'is_popular' => true, 'sort_order' => 5],
                    ['name' => 'Innova Crysta', 'slug' => 'innova-crysta', 'is_popular' => true, 'sort_order' => 6],
                    ['name' => 'Innova Hycross', 'slug' => 'innova-hycross', 'is_popular' => true, 'sort_order' => 7],
                    ['name' => 'Qualis', 'slug' => 'qualis', 'is_popular' => false, 'sort_order' => 8],
                    ['name' => 'Urban Cruiser', 'slug' => 'urban-cruiser', 'is_popular' => true, 'sort_order' => 9],
                ]
            ],
            [
                'name' => 'Mahindra',
                'slug' => 'mahindra',
                'logo' => 'images/brands/mahindra.svg',
                'description' => 'Mahindra Twin Peaks new chrome logos, Thar 4x4 metal badges, Scorpio-N, and XUV700 lettering.',
                'sort_order' => 6,
                'status' => true,
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
                'status' => true,
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
                'status' => true,
                'models' => [
                    ['name' => 'Endeavour', 'slug' => 'endeavour', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => 'EcoSport', 'slug' => 'ecosport', 'is_popular' => false, 'sort_order' => 2],
                ]
            ],
            [
                'name' => 'Volkswagen',
                'slug' => 'volkswagen',
                'logo' => 'images/brands/volkswagen.svg',
                'description' => 'Original VW chrome front grille and tailgate badges for Virtus, Taigun, Polo, Vento and more.',
                'sort_order' => 9,
                'status' => true,
                'models' => [
                    ['name' => 'Ameo', 'slug' => 'ameo', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => 'Cross Polo', 'slug' => 'cross-polo', 'is_popular' => false, 'sort_order' => 2],
                    ['name' => 'Jetta', 'slug' => 'jetta', 'is_popular' => false, 'sort_order' => 3],
                    ['name' => 'Passat', 'slug' => 'passat', 'is_popular' => false, 'sort_order' => 4],
                    ['name' => 'Polo', 'slug' => 'polo', 'is_popular' => true, 'sort_order' => 5],
                    ['name' => 'Vento', 'slug' => 'vento', 'is_popular' => true, 'sort_order' => 6],
                    ['name' => 'Virtus', 'slug' => 'virtus', 'is_popular' => true, 'sort_order' => 7],
                    ['name' => 'Taigun', 'slug' => 'taigun', 'is_popular' => true, 'sort_order' => 8],
                ]
            ],
            [
                'name' => 'Skoda',
                'slug' => 'skoda',
                'logo' => 'images/brands/skoda.svg',
                'description' => 'Original chrome and black emblems, logos and monograms for Skoda vehicles.',
                'sort_order' => 10,
                'status' => true,
                'models' => [
                    ['name' => 'Fabia', 'slug' => 'fabia', 'is_popular' => false, 'sort_order' => 1],
                    ['name' => 'Kushaq', 'slug' => 'kushaq', 'is_popular' => true, 'sort_order' => 2],
                    ['name' => 'Laura', 'slug' => 'laura', 'is_popular' => false, 'sort_order' => 3],
                    ['name' => 'Octavia', 'slug' => 'octavia', 'is_popular' => true, 'sort_order' => 4],
                    ['name' => 'Rapid', 'slug' => 'rapid', 'is_popular' => true, 'sort_order' => 5],
                    ['name' => 'Slavia', 'slug' => 'slavia', 'is_popular' => true, 'sort_order' => 6],
                    ['name' => 'Superb', 'slug' => 'superb', 'is_popular' => true, 'sort_order' => 7],
                ]
            ],
            [
                'name' => 'BMW',
                'slug' => 'bmw',
                'logo' => 'images/brands/bmw.svg',
                'description' => 'Original BMW 82mm & 74mm Roundel hood and trunk emblems, M badges and wheel caps.',
                'sort_order' => 11,
                'status' => true,
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

            $brand = Brand::updateOrCreate(
                ['slug' => $bData['slug']],
                array_merge($bData, ['status' => true, 'is_featured' => true])
            );

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
