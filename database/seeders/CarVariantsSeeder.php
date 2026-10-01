<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\ModelVariant;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CarVariantsSeeder extends Seeder
{
    public function run(): void
    {
        $catLogos = Category::where('slug', 'car-logos')->first() ?? Category::first();
        $catBadges = Category::where('slug', 'model-logos-badges')->first() ?? Category::first();
        $catAccessories = Category::where('slug', 'accessories')->first() ?? Category::first();

        // 1. Tata Variants & Products
        $tata = Brand::where('slug', 'tata')->first();
        if ($tata) {
            // Punch
            $punch = CarModel::where('brand_id', $tata->id)->where('slug', 'punch')->first();
            if (!$punch) {
                $punch = CarModel::create([
                    'brand_id' => $tata->id,
                    'name' => 'Punch',
                    'slug' => 'punch',
                    'is_popular' => true,
                    'is_active' => true,
                    'sort_order' => 2,
                ]);
            }

            $punchVariantsData = [
                ['name' => 'Pure', 'slug' => 'punch-pure', 'sort_order' => 1],
                ['name' => 'Adventure', 'slug' => 'punch-adventure', 'sort_order' => 2],
                ['name' => 'Accomplished', 'slug' => 'punch-accomplished', 'sort_order' => 3],
                ['name' => 'Creative', 'slug' => 'punch-creative', 'sort_order' => 4],
            ];

            $punchVariants = [];
            foreach ($punchVariantsData as $vData) {
                $punchVariants[$vData['slug']] = ModelVariant::updateOrCreate(
                    ['car_model_id' => $punch->id, 'slug' => $vData['slug']],
                    [
                        'name' => $vData['name'],
                        'status' => true,
                        'sort_order' => $vData['sort_order'],
                    ]
                );
            }

            // Products for Punch Pure
            $pureVariant = $punchVariants['punch-pure'];
            $this->createVariantProduct([
                'name' => 'Tata Punch Pure Front Grille Chrome Emblem',
                'slug' => 'tata-punch-pure-front-grille-chrome-emblem',
                'sku' => 'TAT-PUN-PUR-EMB',
                'brand_id' => $tata->id,
                'car_model_id' => $punch->id,
                'model_variant_id' => $pureVariant->id,
                'primary_category_id' => $catLogos?->id,
                'regular_price' => 999.00,
                'sale_price' => 799.00,
                'stock_quantity' => 25,
                'is_featured' => true,
                'image' => 'images/products/tata-front-logo.svg',
                'short_description' => 'Original front grille chrome emblem custom fitted for Tata Punch Pure variant.',
                'full_description' => '<p>Direct fit replacement front grille logo specifically engineered for Tata Punch Pure. Features high-quality weather-resistant chrome finish with heavy-duty 3M adhesive backing.</p>',
            ]);

            $this->createVariantProduct([
                'name' => 'Front Ceramic Brake Pad Set - Tata Punch Pure',
                'slug' => 'front-ceramic-brake-pad-set-tata-punch-pure',
                'sku' => 'TAT-PUN-PUR-BRK',
                'brand_id' => $tata->id,
                'car_model_id' => $punch->id,
                'model_variant_id' => $pureVariant->id,
                'primary_category_id' => $catAccessories?->id,
                'regular_price' => 1899.00,
                'sale_price' => 1499.00,
                'stock_quantity' => 18,
                'is_featured' => true,
                'image' => 'images/products/tata-front-logo.svg',
                'short_description' => 'High performance dust-free ceramic front brake pads for Tata Punch Pure variant.',
                'full_description' => '<p>OEM-grade front ceramic brake pads providing maximum stopping power, low noise, and minimal rotor wear for Tata Punch Pure.</p>',
            ]);

            $this->createVariantProduct([
                'name' => 'Steering Wheel Chrome Badge - Tata Punch Pure',
                'slug' => 'steering-wheel-chrome-badge-tata-punch-pure',
                'sku' => 'TAT-PUN-PUR-STR',
                'brand_id' => $tata->id,
                'car_model_id' => $punch->id,
                'model_variant_id' => $pureVariant->id,
                'primary_category_id' => $catBadges?->id,
                'regular_price' => 699.00,
                'sale_price' => 499.00,
                'stock_quantity' => 40,
                'is_featured' => false,
                'image' => 'images/products/tata-steering-logo.svg',
                'short_description' => 'Interior steering wheel center replacement emblem for Tata Punch Pure.',
                'full_description' => '<p>Precision crafted steering wheel center badge with 3D chrome finish, tailored for Tata Punch Pure steering assembly.</p>',
            ]);

            // Products for Punch Adventure
            $advVariant = $punchVariants['punch-adventure'];
            $this->createVariantProduct([
                'name' => 'All-Weather 7D Floor Mats - Tata Punch Adventure',
                'slug' => 'all-weather-7d-floor-mats-tata-punch-adventure',
                'sku' => 'TAT-PUN-ADV-MAT',
                'brand_id' => $tata->id,
                'car_model_id' => $punch->id,
                'model_variant_id' => $advVariant->id,
                'primary_category_id' => $catAccessories?->id,
                'regular_price' => 2999.00,
                'sale_price' => 2499.00,
                'stock_quantity' => 12,
                'is_featured' => true,
                'image' => 'images/products/tata-front-logo.svg',
                'short_description' => 'Custom edge-to-edge laser measured waterproof 7D car foot mats for Tata Punch Adventure.',
                'full_description' => '<p>Custom-fit 7D floor mats contoured specifically for Tata Punch Adventure variant footwells with anti-skid backing and heel pad.</p>',
            ]);

            $this->createVariantProduct([
                'name' => 'Rear Tailgate 3D Chrome Badge - Tata Punch Adventure',
                'slug' => 'rear-tailgate-3d-chrome-badge-tata-punch-adventure',
                'sku' => 'TAT-PUN-ADV-BDG',
                'brand_id' => $tata->id,
                'car_model_id' => $punch->id,
                'model_variant_id' => $advVariant->id,
                'primary_category_id' => $catBadges?->id,
                'regular_price' => 899.00,
                'sale_price' => 699.00,
                'stock_quantity' => 30,
                'is_featured' => false,
                'image' => 'images/products/tata-rear-logo.svg',
                'short_description' => '3D metal alloy chrome tailgate badge for Tata Punch Adventure.',
                'full_description' => '<p>High-gloss chrome tailgate insignia designed to match OEM placement on Tata Punch Adventure.</p>',
            ]);

            // Products for Punch Accomplished
            $accVariant = $punchVariants['punch-accomplished'];
            $this->createVariantProduct([
                'name' => 'Illuminated Door Sill Scuff Plates - Tata Punch Accomplished',
                'slug' => 'illuminated-door-sill-scuff-plates-tata-punch-accomplished',
                'sku' => 'TAT-PUN-ACC-SCF',
                'brand_id' => $tata->id,
                'car_model_id' => $punch->id,
                'model_variant_id' => $accVariant->id,
                'primary_category_id' => $catAccessories?->id,
                'regular_price' => 2299.00,
                'sale_price' => 1899.00,
                'stock_quantity' => 15,
                'is_featured' => false,
                'image' => 'images/products/tata-front-logo.svg',
                'short_description' => 'LED blue illuminated door sill protectors for Tata Punch Accomplished.',
                'full_description' => '<p>Stainless steel scuff plates with LED backlighting compatible with Tata Punch Accomplished door wiring.</p>',
            ]);

            // Nexon
            $nexon = CarModel::where('brand_id', $tata->id)->where('slug', 'nexon')->first();
            if ($nexon) {
                $nexonVariantsData = [
                    ['name' => 'Smart', 'slug' => 'nexon-smart', 'sort_order' => 1],
                    ['name' => 'Pure', 'slug' => 'nexon-pure', 'sort_order' => 2],
                    ['name' => 'Creative', 'slug' => 'nexon-creative', 'sort_order' => 3],
                    ['name' => 'Fearless', 'slug' => 'nexon-fearless', 'sort_order' => 4],
                ];

                $nexonVariants = [];
                foreach ($nexonVariantsData as $nvData) {
                    $nexonVariants[$nvData['slug']] = ModelVariant::updateOrCreate(
                        ['car_model_id' => $nexon->id, 'slug' => $nvData['slug']],
                        [
                            'name' => $nvData['name'],
                            'status' => true,
                            'sort_order' => $nvData['sort_order'],
                        ]
                    );
                }

                $this->createVariantProduct([
                    'name' => 'Aero Dynamic Roof Rail Trim - Tata Nexon Smart',
                    'slug' => 'aero-dynamic-roof-rail-trim-tata-nexon-smart',
                    'sku' => 'TAT-NEX-SMT-ROF',
                    'brand_id' => $tata->id,
                    'car_model_id' => $nexon->id,
                    'model_variant_id' => $nexonVariants['nexon-smart']->id,
                    'primary_category_id' => $catAccessories?->id,
                    'regular_price' => 3999.00,
                    'sale_price' => 3299.00,
                    'stock_quantity' => 10,
                    'is_featured' => true,
                    'image' => 'images/products/tata-front-logo.svg',
                    'short_description' => 'Aerodynamic silver anodized roof rail styling trims for Tata Nexon Smart.',
                    'full_description' => '<p>Drill-free stylish roof rails designed to give Tata Nexon Smart an aggressive SUV stance.</p>',
                ]);
            }

            // Tiago
            $tiago = CarModel::where('brand_id', $tata->id)->where('slug', 'tiago')->first();
            if ($tiago) {
                foreach ([
                    ['name' => 'XE', 'slug' => 'tiago-xe', 'sort_order' => 1],
                    ['name' => 'XM', 'slug' => 'tiago-xm', 'sort_order' => 2],
                    ['name' => 'XT', 'slug' => 'tiago-xt', 'sort_order' => 3],
                    ['name' => 'XZ', 'slug' => 'tiago-xz', 'sort_order' => 4],
                ] as $tvData) {
                    ModelVariant::updateOrCreate(
                        ['car_model_id' => $tiago->id, 'slug' => $tvData['slug']],
                        [
                            'name' => $tvData['name'],
                            'status' => true,
                            'sort_order' => $tvData['sort_order'],
                        ]
                    );
                }
            }
        }

        // 2. Mahindra Variants & Products
        $mahindra = Brand::where('slug', 'mahindra')->first();
        if ($mahindra) {
            $thar = CarModel::where('brand_id', $mahindra->id)->where('slug', 'thar')->first();
            if ($thar) {
                $tharVariantsData = [
                    ['name' => 'AX', 'slug' => 'thar-ax', 'sort_order' => 1],
                    ['name' => 'LX', 'slug' => 'thar-lx', 'sort_order' => 2],
                    ['name' => 'Earth Edition', 'slug' => 'thar-earth-edition', 'sort_order' => 3],
                ];

                $tharVariants = [];
                foreach ($tharVariantsData as $tvData) {
                    $tharVariants[$tvData['slug']] = ModelVariant::updateOrCreate(
                        ['car_model_id' => $thar->id, 'slug' => $tvData['slug']],
                        [
                            'name' => $tvData['name'],
                            'status' => true,
                            'sort_order' => $tvData['sort_order'],
                        ]
                    );
                }

                $this->createVariantProduct([
                    'name' => 'Heavy Duty Front Bumper Emblem - Mahindra Thar AX',
                    'slug' => 'heavy-duty-front-bumper-emblem-mahindra-thar-ax',
                    'sku' => 'MAH-THR-AX-EMB',
                    'brand_id' => $mahindra->id,
                    'car_model_id' => $thar->id,
                    'model_variant_id' => $tharVariants['thar-ax']->id,
                    'primary_category_id' => $catLogos?->id,
                    'regular_price' => 1499.00,
                    'sale_price' => 1199.00,
                    'stock_quantity' => 20,
                    'is_featured' => true,
                    'image' => 'images/products/mahindra-front-logo.svg',
                    'short_description' => 'Heavy duty matte finish twin peaks emblem for Mahindra Thar AX.',
                    'full_description' => '<p>Reinforced off-road grade emblem engineered to withstand extreme trail conditions on Mahindra Thar AX.</p>',
                ]);

                $this->createVariantProduct([
                    'name' => 'Off-Road 4x4 Fender Metal Badge - Mahindra Thar AX',
                    'slug' => 'off-road-4x4-fender-metal-badge-mahindra-thar-ax',
                    'sku' => 'MAH-THR-AX-4X4',
                    'brand_id' => $mahindra->id,
                    'car_model_id' => $thar->id,
                    'model_variant_id' => $tharVariants['thar-ax']->id,
                    'primary_category_id' => $catBadges?->id,
                    'regular_price' => 1099.00,
                    'sale_price' => 899.00,
                    'stock_quantity' => 35,
                    'is_featured' => false,
                    'image' => 'images/products/mahindra-front-logo.svg',
                    'short_description' => 'Cast alloy 4x4 metallic badge for Mahindra Thar AX fenders.',
                    'full_description' => '<p>Authentic 4x4 emblem made from zinc alloy with matte red & silver finish for Thar AX.</p>',
                ]);
            }

            $scorpio = CarModel::where('brand_id', $mahindra->id)->where('slug', 'scorpio')->first();
            if ($scorpio) {
                foreach ([
                    ['name' => 'Classic', 'slug' => 'scorpio-classic', 'sort_order' => 1],
                    ['name' => 'Z2', 'slug' => 'scorpio-z2', 'sort_order' => 2],
                    ['name' => 'Z4', 'slug' => 'scorpio-z4', 'sort_order' => 3],
                    ['name' => 'Z8', 'slug' => 'scorpio-z8', 'sort_order' => 4],
                    ['name' => 'Z8L', 'slug' => 'scorpio-z8l', 'sort_order' => 5],
                ] as $svData) {
                    ModelVariant::updateOrCreate(
                        ['car_model_id' => $scorpio->id, 'slug' => $svData['slug']],
                        [
                            'name' => $svData['name'],
                            'status' => true,
                            'sort_order' => $svData['sort_order'],
                        ]
                    );
                }
            }
        }

        // 3. Hyundai Variants & Products
        $hyundai = Brand::where('slug', 'hyundai')->first();
        if ($hyundai) {
            $creta = CarModel::where('brand_id', $hyundai->id)->where('slug', 'creta')->first();
            if ($creta) {
                $cretaVariantsData = [
                    ['name' => 'E', 'slug' => 'creta-e', 'sort_order' => 1],
                    ['name' => 'EX', 'slug' => 'creta-ex', 'sort_order' => 2],
                    ['name' => 'S', 'slug' => 'creta-s', 'sort_order' => 3],
                    ['name' => 'SX', 'slug' => 'creta-sx', 'sort_order' => 4],
                    ['name' => 'SX(O)', 'slug' => 'creta-sxo', 'sort_order' => 5],
                ];

                $cretaVariants = [];
                foreach ($cretaVariantsData as $cvData) {
                    $cretaVariants[$cvData['slug']] = ModelVariant::updateOrCreate(
                        ['car_model_id' => $creta->id, 'slug' => $cvData['slug']],
                        [
                            'name' => $cvData['name'],
                            'status' => true,
                            'sort_order' => $cvData['sort_order'],
                        ]
                    );
                }

                $this->createVariantProduct([
                    'name' => 'Dual Tone Chrome Rear Monogram - Hyundai Creta SX',
                    'slug' => 'dual-tone-chrome-rear-monogram-hyundai-creta-sx',
                    'sku' => 'HYU-CRT-SX-MON',
                    'brand_id' => $hyundai->id,
                    'car_model_id' => $creta->id,
                    'model_variant_id' => $cretaVariants['creta-sx']->id,
                    'primary_category_id' => $catBadges?->id,
                    'regular_price' => 1299.00,
                    'sale_price' => 999.00,
                    'stock_quantity' => 22,
                    'is_featured' => true,
                    'image' => 'images/products/hyundai-creta-monogram.svg',
                    'short_description' => 'Original style rear trunk dual tone chrome letters monogram for Hyundai Creta SX.',
                    'full_description' => '<p>OEM specification CRETA tailgate lettering monogram in dual-tone silver and chrome finish designed specifically for Creta SX.</p>',
                ]);
            }
        }

        $this->command->info('CarVariantsSeeder completed successfully. Variants & mapped products created.');
    }

    private function createVariantProduct(array $data): Product
    {
        $product = Product::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'name' => $data['name'],
                'sku' => $data['sku'],
                'brand_id' => $data['brand_id'],
                'car_model_id' => $data['car_model_id'],
                'model_variant_id' => $data['model_variant_id'],
                'primary_category_id' => $data['primary_category_id'] ?? null,
                'regular_price' => $data['regular_price'],
                'sale_price' => $data['sale_price'],
                'stock_quantity' => $data['stock_quantity'],
                'is_featured' => $data['is_featured'] ?? false,
                'status' => true,
                'short_description' => $data['short_description'],
                'full_description' => $data['full_description'],
            ]
        );

        if (!empty($data['image']) && $product->images()->count() === 0) {
            $product->images()->create([
                'image_path' => $data['image'],
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        return $product;
    }
}
