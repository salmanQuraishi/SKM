<?php

require 'D:/Laravel/skm/vendor/autoload.php';
$app = require 'D:/Laravel/skm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelVariant;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

echo "1. Syncing 5 Categories from Image 1...\n";

$categoriesData = [
    [
        'name' => 'Front Logo',
        'slug' => 'front-logo',
        'icon' => 'fa-shield-halved',
        'description' => 'Authentic front radiator grille emblems, 3D badges, and nose logos for all car brands.',
        'sort_order' => 1,
    ],
    [
        'name' => 'Rear Logo',
        'slug' => 'rear-logo',
        'icon' => 'fa-car-rear',
        'description' => 'Genuine tailgate, boot lid, and trunk emblems engineered for perfect original fitment.',
        'sort_order' => 2,
    ],
    [
        'name' => 'Bonnet / Tailgate / Letters',
        'slug' => 'bonnet-tailgate-letters',
        'icon' => 'fa-font',
        'description' => 'Premium 3D raised letters and typography monograms for bonnet and rear tailgate.',
        'sort_order' => 3,
    ],
    [
        'name' => 'Kit Full Monogram',
        'slug' => 'kit-full-monogram',
        'icon' => 'fa-boxes-stacked',
        'description' => 'Complete vehicle emblem kits including front, rear, steering wheel, and model badges.',
        'sort_order' => 4,
    ],
    [
        'name' => 'Single Name Badge',
        'slug' => 'single-name-badge',
        'icon' => 'fa-certificate',
        'description' => 'Official model badges, trim nameplates, and engine edition monograms (VXi, Turbo, 4x4).',
        'sort_order' => 5,
    ],
];

$categoryMap = [];
foreach ($categoriesData as $cData) {
    $cat = Category::updateOrCreate(
        ['slug' => $cData['slug']],
        [
            'parent_id' => null,
            'name' => $cData['name'],
            'icon' => $cData['icon'],
            'description' => $cData['description'],
            'sort_order' => $cData['sort_order'],
            'status' => true,
            'is_featured' => true,
        ]
    );
    $categoryMap[$cData['slug']] = $cat;
    echo "  - Category '{$cat->name}' (ID: {$cat->id})\n";
}

echo "\n2. Syncing 18 Brands from Image 2...\n";

$brandsData = [
    ['name' => 'Maruti Suzuki', 'slug' => 'maruti-suzuki', 'logo' => 'images/brands/maruti-suzuki.svg', 'sort_order' => 1],
    ['name' => 'Hyundai', 'slug' => 'hyundai', 'logo' => 'images/brands/hyundai.svg', 'sort_order' => 2],
    ['name' => 'Honda', 'slug' => 'honda', 'logo' => 'images/brands/honda.svg', 'sort_order' => 3],
    ['name' => 'Mahindra', 'slug' => 'mahindra', 'logo' => 'images/brands/mahindra.svg', 'sort_order' => 4],
    ['name' => 'Toyota', 'slug' => 'toyota', 'logo' => 'images/brands/toyota.svg', 'sort_order' => 5],
    ['name' => 'Tata', 'slug' => 'tata', 'logo' => 'images/brands/tata.svg', 'sort_order' => 6],
    ['name' => 'Nissan', 'slug' => 'nissan', 'logo' => 'images/brands/nissan.svg', 'sort_order' => 7],
    ['name' => 'Volkswagen', 'slug' => 'volkswagen', 'logo' => 'images/brands/volkswagen.svg', 'sort_order' => 8],
    ['name' => 'Chevrolet', 'slug' => 'chevrolet', 'logo' => 'images/brands/chevrolet.svg', 'sort_order' => 9],
    ['name' => 'Skoda', 'slug' => 'skoda', 'logo' => 'images/brands/skoda.svg', 'sort_order' => 10],
    ['name' => 'Renault', 'slug' => 'renault', 'logo' => 'images/brands/renault.svg', 'sort_order' => 11],
    ['name' => 'Kia', 'slug' => 'kia', 'logo' => 'images/brands/kia.svg', 'sort_order' => 12],
    ['name' => 'Ford', 'slug' => 'ford', 'logo' => 'images/brands/ford.svg', 'sort_order' => 13],
    ['name' => 'Fiat', 'slug' => 'fiat', 'logo' => 'images/brands/fiat.svg', 'sort_order' => 14],
    ['name' => 'Datsun', 'slug' => 'datsun', 'logo' => 'images/brands/datsun.svg', 'sort_order' => 15],
    ['name' => 'Jeep', 'slug' => 'jeep', 'logo' => 'images/brands/jeep.svg', 'sort_order' => 16],
    ['name' => 'Audi', 'slug' => 'audi', 'logo' => 'images/brands/audi.svg', 'sort_order' => 17],
    ['name' => 'BMW', 'slug' => 'bmw', 'logo' => 'images/brands/bmw.svg', 'sort_order' => 18],
];

$brandMap = [];
foreach ($brandsData as $bData) {
    $b = Brand::updateOrCreate(
        ['slug' => $bData['slug']],
        [
            'name' => $bData['name'],
            'logo' => $bData['logo'],
            'sort_order' => $bData['sort_order'],
            'status' => true,
            'is_featured' => true,
        ]
    );
    $brandMap[$bData['slug']] = $b;
    echo "  - Brand '{$b->name}' (ID: {$b->id})\n";
}

echo "\n3. Creating Models & Variants for new brands...\n";

$newModelsAndVariants = [
    'nissan' => [
        'Magnite' => ['XE', 'XL', 'XV', 'XV Premium'],
        'Kicks' => ['XL', 'XV', 'XV Premium'],
    ],
    'chevrolet' => [
        'Cruze' => ['LT', 'LTZ'],
        'Beat' => ['PS', 'LS', 'LT'],
    ],
    'skoda' => [
        'Kushaq' => ['Active', 'Ambition', 'Style', 'Monte Carlo'],
        'Slavia' => ['Active', 'Ambition', 'Style'],
    ],
    'renault' => [
        'Kwid' => ['RXT', 'RXZ', 'Climber'],
        'Kiger' => ['RXT', 'RXZ'],
        'Triber' => ['RXT', 'RXZ'],
    ],
    'fiat' => [
        'Punto' => ['Active', 'Dynamic', 'Emotion'],
        'Linea' => ['Active', 'Dynamic', 'Emotion'],
    ],
    'datsun' => [
        'GO' => ['D', 'A', 'T', 'T Option'],
        'redi-GO' => ['D', 'A', 'T', 'T(O)'],
    ],
    'jeep' => [
        'Compass' => ['Sport', 'Longitude', 'Limited', 'Model S'],
        'Meridian' => ['Limited', 'Limited Plus'],
    ],
    'audi' => [
        'A4' => ['Premium', 'Premium Plus', 'Technology'],
        'Q3' => ['Premium Plus', 'Technology'],
    ],
];

foreach ($newModelsAndVariants as $brandSlug => $models) {
    $brand = $brandMap[$brandSlug];
    $mOrder = 1;
    foreach ($models as $modelName => $variants) {
        $modelSlug = Str::slug($modelName);
        $carModel = CarModel::updateOrCreate(
            ['brand_id' => $brand->id, 'slug' => $modelSlug],
            [
                'name' => $modelName,
                'status' => true,
                'is_popular' => true,
                'sort_order' => $mOrder++,
            ]
        );

        $vOrder = 1;
        $primaryVariant = null;
        foreach ($variants as $variantName) {
            $vSlug = Str::slug($modelName . ' ' . $variantName);
            $variant = ModelVariant::updateOrCreate(
                ['car_model_id' => $carModel->id, 'slug' => $vSlug],
                [
                    'name' => $variantName,
                    'status' => true,
                    'sort_order' => $vOrder++,
                ]
            );
            if (!$primaryVariant) $primaryVariant = $variant;
        }

        // Add 5 sample products for the model's primary variant (1 for each of the 5 categories)
        if ($primaryVariant && $primaryVariant->products()->count() == 0) {
            $sampleProducts = [
                [
                    'name' => "{$brand->name} {$modelName} 3D Chrome Front Grille Emblem",
                    'cat' => 'front-logo',
                    'placement' => 'Front Grille',
                    'finish' => 'Chrome',
                    'price' => 899,
                    'sku' => strtoupper(substr($brandSlug, 0, 3) . '-' . substr($modelSlug, 0, 3) . '-FRT'),
                ],
                [
                    'name' => "{$brand->name} {$modelName} Rear Trunk Chrome Emblem",
                    'cat' => 'rear-logo',
                    'placement' => 'Rear Trunk',
                    'finish' => 'Chrome',
                    'price' => 799,
                    'sku' => strtoupper(substr($brandSlug, 0, 3) . '-' . substr($modelSlug, 0, 3) . '-RR'),
                ],
                [
                    'name' => strtoupper($modelName) . " Premium 3D Raised Bonnet Letters",
                    'cat' => 'bonnet-tailgate-letters',
                    'placement' => 'Rear Trunk',
                    'finish' => 'Gloss Black',
                    'price' => 1199,
                    'sku' => strtoupper(substr($brandSlug, 0, 3) . '-' . substr($modelSlug, 0, 3) . '-LET'),
                ],
                [
                    'name' => "{$brand->name} {$modelName} Complete Chrome Emblem Combo Kit",
                    'cat' => 'kit-full-monogram',
                    'placement' => 'Universal',
                    'finish' => 'Chrome',
                    'price' => 2499,
                    'sku' => strtoupper(substr($brandSlug, 0, 3) . '-' . substr($modelSlug, 0, 3) . '-KIT'),
                ],
                [
                    'name' => "{$primaryVariant->name} Edition 3D Metal Trim Badge",
                    'cat' => 'single-name-badge',
                    'placement' => 'Fender / Side',
                    'finish' => 'Chrome',
                    'price' => 499,
                    'sku' => strtoupper(substr($brandSlug, 0, 3) . '-' . substr($modelSlug, 0, 3) . '-BDG'),
                ],
            ];

            foreach ($sampleProducts as $sp) {
                $targetCat = $categoryMap[$sp['cat']];
                $prod = Product::create([
                    'brand_id' => $brand->id,
                    'car_model_id' => $carModel->id,
                    'model_variant_id' => $primaryVariant->id,
                    'primary_category_id' => $targetCat->id,
                    'name' => $sp['name'],
                    'slug' => Str::slug($sp['name'] . '-' . uniqid()),
                    'sku' => $sp['sku'],
                    'regular_price' => $sp['price'],
                    'sale_price' => $sp['price'] - 100,
                    'cost_price' => $sp['price'] * 0.4,
                    'stock_quantity' => 50,
                    'placement' => $sp['placement'],
                    'finish' => $sp['finish'],
                    'product_type' => 'logo',
                    'compatible_models_text' => "{$brand->name} {$modelName} ({$primaryVariant->name})",
                    'short_description' => "Genuine OEM-spec compatible emblem badge for {$brand->name} {$modelName}.",
                    'full_description' => "High grade automotive ABS with triple chrome plating. Pre-applied automotive 3M adhesive tape for effortless peel-and-stick installation.",
                    'status' => true,
                    'is_featured' => true,
                ]);
                $prod->categories()->sync([$targetCat->id]);
            }
        }
    }
    echo "  - Added models & variants for {$brand->name}\n";
}

echo "\n4. Categorizing all existing products into the 5 Image 1 Categories...\n";

$allProducts = Product::all();
$catStats = [
    'front-logo' => 0,
    'rear-logo' => 0,
    'bonnet-tailgate-letters' => 0,
    'kit-full-monogram' => 0,
    'single-name-badge' => 0,
];

foreach ($allProducts as $p) {
    $name = $p->name;
    $placement = (string) $p->placement;

    // Decision tree for 5 categories
    if (stripos($name, 'Kit') !== false || stripos($name, 'Full') !== false || stripos($name, 'Combo') !== false || stripos($name, 'Set') !== false || stripos($name, 'Pack') !== false) {
        $assignedSlug = 'kit-full-monogram';
    } elseif (stripos($name, 'Letter') !== false || stripos($name, 'Letters') !== false || stripos($name, 'Bonnet') !== false || stripos($name, 'Alphabet') !== false) {
        $assignedSlug = 'bonnet-tailgate-letters';
    } elseif (stripos($name, 'Front') !== false || stripos($placement, 'Front') !== false || stripos($name, 'Grille') !== false) {
        $assignedSlug = 'front-logo';
    } elseif (stripos($name, 'Rear') !== false || stripos($placement, 'Rear') !== false || stripos($name, 'Boot') !== false || stripos($name, 'Trunk') !== false || stripos($name, 'Tailgate') !== false) {
        $assignedSlug = 'rear-logo';
    } else {
        $assignedSlug = 'single-name-badge';
    }

    $targetCategory = $categoryMap[$assignedSlug];
    $p->primary_category_id = $targetCategory->id;
    $p->save();
    $p->categories()->syncWithoutDetaching([$targetCategory->id]);
    $catStats[$assignedSlug]++;
}

echo "\nCategory product mapping summary:\n";
foreach ($catStats as $slug => $count) {
    echo "  - {$categoryMap[$slug]->name}: {$count} products\n";
}

echo "\nDONE! 18 Brands & 5 Categories successfully synchronized.\n";
