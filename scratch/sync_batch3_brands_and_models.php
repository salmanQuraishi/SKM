<?php

require 'D:/Laravel/skm/vendor/autoload.php';
$app = require 'D:/Laravel/skm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelVariant;
use App\Models\Category;
use App\Models\Product;
use Database\Seeders\BrandsAndModelsSeeder;
use Database\Seeders\CategoriesSeeder;
use Illuminate\Support\Str;

echo "=== STEP 1: RUNNING OFFICIAL SEEDERS ===\n";
(new BrandsAndModelsSeeder())->run();
echo "  [OK] BrandsAndModelsSeeder completed.\n";

(new CategoriesSeeder())->run();
echo "  [OK] CategoriesSeeder completed.\n";

echo "\n=== STEP 2: DEFINING MODELS & VARIANTS FOR HYUNDAI, KIA, FIAT, DATSUN, CHEVROLET ===\n";

// Target categories for products
$catFront = Category::where('slug', 'front-logo')->first() 
    ?? Category::where('slug', 'front-rear-emblems')->first() 
    ?? Category::where('slug', 'car-logos')->first();

$catRear = Category::where('slug', 'rear-logo')->first() 
    ?? Category::where('slug', 'front-rear-emblems')->first() 
    ?? Category::where('slug', 'car-logos')->first();

$catLetters = Category::where('slug', 'bonnet-tailgate-letters')->first() 
    ?? Category::where('slug', 'letters-numbers')->first();

$catKit = Category::where('slug', 'kit-full-monogram')->first() 
    ?? Category::where('slug', '3d-chrome-emblems')->first();

$catBadge = Category::where('slug', 'single-name-badge')->first() 
    ?? Category::where('slug', 'model-logos-badges')->first();

$brandsToSync = [
    // -------------------------------------------------------------
    // HYUNDAI (17 models)
    // -------------------------------------------------------------
    'hyundai' => [
        'accent' => [
            'name' => 'Accent',
            'variants' => ['GLE', 'GLS', 'CRDi', 'Executive'],
        ],
        'alcazar' => [
            'name' => 'Alcazar',
            'variants' => ['Prestige', 'Platinum', 'Signature'],
        ],
        'aura' => [
            'name' => 'Aura',
            'variants' => ['E', 'S', 'SX', 'SX Plus'],
        ],
        'creta' => [
            'name' => 'Creta',
            'variants' => ['E', 'EX', 'S', 'SX', 'SX(O)'],
        ],
        'elantra' => [
            'name' => 'Elantra',
            'variants' => ['S', 'SX', 'SX(O)'],
        ],
        'eon' => [
            'name' => 'Eon',
            'variants' => ['D-Lite', 'Era', 'Magna', 'Sportz'],
        ],
        'exter' => [
            'name' => 'Exter',
            'variants' => ['EX', 'S', 'SX', 'SX(O)'],
        ],
        'fluidic-elantra' => [
            'name' => 'Fluidic Elantra',
            'variants' => ['S', 'SX', 'SX(O)', 'CRDi'],
        ],
        'getz' => [
            'name' => 'Getz',
            'variants' => ['GLE', 'GLS', 'GLX', 'CRDi'],
        ],
        'grand-i10' => [
            'name' => 'Grand i10',
            'variants' => ['Era', 'Magna', 'Sportz', 'Asta'],
        ],
        'i10' => [
            'name' => 'i10',
            'variants' => ['D-Lite', 'Era', 'Magna', 'Sportz', 'Asta'],
        ],
        'i20' => [
            'name' => 'i20',
            'variants' => ['Magna', 'Sportz', 'Asta', 'Asta (O)'],
        ],
        'santro' => [
            'name' => 'Santro',
            'variants' => ['Era', 'Magna', 'Sportz', 'Asta', 'Xing'],
        ],
        'tucson' => [
            'name' => 'Tucson',
            'variants' => ['Platinum', 'Signature'],
        ],
        'venue' => [
            'name' => 'Venue',
            'variants' => ['E', 'S', 'SX', 'SX(O)'],
        ],
        'verna' => [
            'name' => 'Verna',
            'variants' => ['EX', 'S', 'SX', 'SX(O)', 'Turbo'],
        ],
        'xcent' => [
            'name' => 'Xcent',
            'variants' => ['Base', 'S', 'SX', 'SX(O)'],
        ],
    ],

    // -------------------------------------------------------------
    // KIA (5 models)
    // -------------------------------------------------------------
    'kia' => [
        'carens' => [
            'name' => 'Carens',
            'variants' => ['Premium', 'Prestige', 'Luxury', 'Luxury Plus'],
        ],
        'carnival' => [
            'name' => 'Carnival',
            'variants' => ['Prestige', 'Limousine', 'Limousine Plus'],
        ],
        'ev6' => [
            'name' => 'EV6',
            'variants' => ['GT Line', 'GT Line AWD'],
        ],
        'seltos' => [
            'name' => 'Seltos',
            'variants' => ['HTE', 'HTK', 'HTX', 'GTX Plus', 'X-Line'],
        ],
        'sonet' => [
            'name' => 'Sonet',
            'variants' => ['HTE', 'HTK', 'HTX', 'GTX Plus', 'X-Line'],
        ],
    ],

    // -------------------------------------------------------------
    // FIAT (4 models)
    // -------------------------------------------------------------
    'fiat' => [
        'grand-punto' => [
            'name' => 'Grand Punto',
            'variants' => ['Active', 'Dynamic', 'Emotion'],
        ],
        'linea' => [
            'name' => 'Linea',
            'variants' => ['Active', 'Dynamic', 'Emotion', 'T-Jet'],
        ],
        'palio' => [
            'name' => 'Palio',
            'variants' => ['EL', 'ELX', '1.2', '1.6', 'Stile'],
        ],
        'punto' => [
            'name' => 'Punto',
            'variants' => ['Active', 'Dynamic', 'Emotion', 'Evo'],
        ],
    ],

    // -------------------------------------------------------------
    // DATSUN (3 models)
    // -------------------------------------------------------------
    'datsun' => [
        'go' => [
            'name' => 'Go',
            'variants' => ['D', 'A', 'T', 'T Option'],
        ],
        'go-plus' => [
            'name' => 'Go+',
            'variants' => ['D', 'A', 'T', 'T Option'],
        ],
        'redigo' => [
            'name' => 'Redigo',
            'variants' => ['D', 'A', 'T', 'T Option', 'S'],
        ],
    ],

    // -------------------------------------------------------------
    // CHEVROLET (8 models)
    // -------------------------------------------------------------
    'chevrolet' => [
        'aveo' => [
            'name' => 'Aveo',
            'variants' => ['1.4', '1.6', 'LT'],
        ],
        'beat' => [
            'name' => 'Beat',
            'variants' => ['PS', 'LS', 'LT', 'LT Option'],
        ],
        'cruze' => [
            'name' => 'Cruze',
            'variants' => ['LT', 'LTZ'],
        ],
        'enjoy' => [
            'name' => 'Enjoy',
            'variants' => ['LS', 'LT', 'LTZ'],
        ],
        'optra' => [
            'name' => 'Optra',
            'variants' => ['Elite', 'Magnum', 'Royale'],
        ],
        'sail' => [
            'name' => 'Sail',
            'variants' => ['Base', 'LS', 'LT', 'LT ABS'],
        ],
        'spark' => [
            'name' => 'Spark',
            'variants' => ['1.0', 'PS', 'LS', 'LT'],
        ],
        'tavera' => [
            'name' => 'Tavera',
            'variants' => ['Neo', 'Elite', 'LT', 'SS'],
        ],
    ],
];

foreach ($brandsToSync as $brandSlug => $modelsList) {
    $brand = Brand::where('slug', $brandSlug)->first();
    if (!$brand) {
        echo "[ERROR] Brand {$brandSlug} not found in database!\n";
        continue;
    }

    echo "\n>>> Processing Brand: {$brand->name} (Models: " . count($modelsList) . ")\n";

    $brandChildCat = Category::where('slug', "{$brandSlug}-logos")->first();

    $order = 1;
    foreach ($modelsList as $mSlug => $mData) {
        $name = $mData['name'];
        $variants = $mData['variants'];

        // Get or update CarModel
        $carModel = CarModel::updateOrCreate(
            ['brand_id' => $brand->id, 'slug' => $mSlug],
            [
                'name' => $name,
                'sort_order' => $order++,
                'status' => true,
                'is_popular' => true,
            ]
        );

        echo "  - Model: {$carModel->name} (Slug: {$carModel->slug})\n";

        // Sync Variants
        $vOrder = 1;
        $firstVariant = null;
        foreach ($variants as $variantName) {
            $vSlug = Str::slug($carModel->slug . ' ' . $variantName);
            $variant = ModelVariant::updateOrCreate(
                ['car_model_id' => $carModel->id, 'slug' => $vSlug],
                [
                    'name' => $variantName,
                    'status' => true,
                    'sort_order' => $vOrder++,
                ]
            );

            if (!$firstVariant) {
                $firstVariant = $variant;
            }
        }

        // Check if car model has mapped products
        $existingCount = Product::where('car_model_id', $carModel->id)->count();
        if ($existingCount === 0 && $firstVariant) {
            $cleanModelSlug = strtoupper(substr(str_replace('-', '', $carModel->slug), 0, 3));
            $cleanBrandSlug = strtoupper(substr(str_replace('-', '', $brand->slug), 0, 3));

            $sampleProducts = [
                [
                    'cat' => $catFront,
                    'name' => "{$brand->name} {$carModel->name} Front Grille 3D Chrome Emblem Logo",
                    'placement' => 'Front Grille',
                    'finish' => 'Chrome',
                    'price' => 899,
                    'sku' => "{$cleanBrandSlug}-{$cleanModelSlug}-FRT",
                ],
                [
                    'cat' => $catRear,
                    'name' => "{$brand->name} {$carModel->name} Rear Boot Lid Chrome Emblem Logo",
                    'placement' => 'Rear Trunk',
                    'finish' => 'Chrome',
                    'price' => 799,
                    'sku' => "{$cleanBrandSlug}-{$cleanModelSlug}-RR",
                ],
                [
                    'cat' => $catLetters,
                    'name' => strtoupper($carModel->name) . " Premium 3D Raised Bonnet & Tailgate Letters",
                    'placement' => 'Bonnet / Boot',
                    'finish' => 'Gloss Black',
                    'price' => 1199,
                    'sku' => "{$cleanBrandSlug}-{$cleanModelSlug}-LET",
                ],
                [
                    'cat' => $catKit,
                    'name' => "{$brand->name} {$carModel->name} Full Chrome Emblem & Monogram Combo Kit",
                    'placement' => 'Full Exterior Kit',
                    'finish' => 'Chrome',
                    'price' => 2499,
                    'sku' => "{$cleanBrandSlug}-{$cleanModelSlug}-KIT",
                ],
                [
                    'cat' => $catBadge,
                    'name' => "{$firstVariant->name} Edition 3D Metal Fender & Tailgate Badge",
                    'placement' => 'Fender / Side',
                    'finish' => 'Chrome',
                    'price' => 499,
                    'sku' => "{$cleanBrandSlug}-{$cleanModelSlug}-BDG",
                ],
            ];

            foreach ($sampleProducts as $sp) {
                $sku = $sp['sku'];
                if (Product::where('sku', $sku)->exists()) {
                    $sku .= '-' . rand(10, 99);
                }

                $prod = Product::create([
                    'brand_id' => $brand->id,
                    'car_model_id' => $carModel->id,
                    'model_variant_id' => $firstVariant->id,
                    'primary_category_id' => $sp['cat'] ? $sp['cat']->id : 1,
                    'name' => $sp['name'],
                    'slug' => Str::slug($sp['name'] . '-' . uniqid()),
                    'sku' => $sku,
                    'regular_price' => $sp['price'],
                    'sale_price' => $sp['price'] - 100,
                    'cost_price' => round($sp['price'] * 0.4, 2),
                    'stock_quantity' => 50,
                    'placement' => $sp['placement'],
                    'finish' => $sp['finish'],
                    'product_type' => 'logo',
                    'compatible_models_text' => "{$brand->name} {$carModel->name} ({$firstVariant->name})",
                    'short_description' => "Genuine OEM-spec compatible emblem badge for {$brand->name} {$carModel->name}.",
                    'full_description' => "High grade automotive ABS with triple chrome plating. Pre-applied automotive 3M adhesive tape for effortless peel-and-stick installation.",
                    'status' => true,
                    'is_featured' => true,
                ]);

                $categoryIds = [];
                if ($sp['cat']) {
                    $categoryIds[] = $sp['cat']->id;
                }
                if ($brandChildCat) {
                    $categoryIds[] = $brandChildCat->id;
                }
                $prod->categories()->sync(array_unique($categoryIds));
            }
            echo "    -> Seeded 5 core category products for {$carModel->name} (mapped to {$firstVariant->name})\n";
        }
    }
}

echo "\n=== BATCH 3 SYNC COMPLETED SUCCESSFULLY ===\n";
