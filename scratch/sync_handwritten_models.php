<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelVariant;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

echo "=== SYNCING HANDWRITTEN MODELS FOR MARUTI SUZUKI, SKODA, TOYOTA, TATA ===\n\n";

// Fetch the 5 active categories
$catFront = Category::where('slug', 'front-logo')->firstOrFail();
$catRear = Category::where('slug', 'rear-logo')->firstOrFail();
$catLetters = Category::where('slug', 'bonnet-tailgate-letters')->firstOrFail();
$catKit = Category::where('slug', 'kit-full-monogram')->firstOrFail();
$catBadge = Category::where('slug', 'single-name-badge')->firstOrFail();

$brandModels = [
    // -------------------------------------------------------------
    // 1. MARUTI SUZUKI (30 models from Image 1)
    // -------------------------------------------------------------
    'maruti-suzuki' => [
        1 => ['name' => 'Car 800', 'slug' => 'car-800', 'variants' => ['Standard', 'DX']],
        2 => ['name' => 'A-Star', 'slug' => 'a-star', 'variants' => ['LXi', 'VXi', 'ZXi']],
        3 => ['name' => 'Alto', 'slug' => 'alto', 'variants' => ['Standard', 'LX', 'LXi']],
        4 => ['name' => 'Alto 800', 'slug' => 'alto-800', 'variants' => ['Std', 'LXi', 'VXi']],
        5 => ['name' => 'Alto K10', 'slug' => 'alto-k10', 'variants' => ['Std', 'LXi', 'VXi', 'VXi Plus']],
        6 => ['name' => 'Baleno', 'slug' => 'baleno', 'variants' => ['Sigma', 'Delta', 'Zeta', 'Alpha']],
        7 => ['name' => 'Brezza', 'slug' => 'brezza', 'variants' => ['LXi', 'VXi', 'ZXi', 'ZXi Plus']],
        8 => ['name' => 'Celerio', 'slug' => 'celerio', 'variants' => ['LXi', 'VXi', 'ZXi', 'ZXi Plus']],
        9 => ['name' => 'Ciaz', 'slug' => 'ciaz', 'variants' => ['Sigma', 'Delta', 'Zeta', 'Alpha']],
        10 => ['name' => 'Eeco', 'slug' => 'eeco', 'variants' => ['5 STR Std', '7 STR Std', 'AC']],
        11 => ['name' => 'Ertiga', 'slug' => 'ertiga', 'variants' => ['LXi', 'VXi', 'ZXi', 'ZXi Plus']],
        12 => ['name' => 'Esteem', 'slug' => 'esteem', 'variants' => ['LX', 'LXi', 'VXi']],
        13 => ['name' => 'Fronx', 'slug' => 'fronx', 'variants' => ['Sigma', 'Delta', 'Zeta', 'Alpha']],
        14 => ['name' => 'Grand Vitara', 'slug' => 'grand-vitara', 'variants' => ['Sigma', 'Delta', 'Zeta', 'Alpha']],
        15 => ['name' => 'Gypsy', 'slug' => 'gypsy', 'variants' => ['King', 'Soft Top', 'Hard Top']],
        16 => ['name' => 'Ignis', 'slug' => 'ignis', 'variants' => ['Sigma', 'Delta', 'Zeta', 'Alpha']],
        17 => ['name' => 'Jimny', 'slug' => 'jimny', 'variants' => ['Zeta', 'Alpha']],
        18 => ['name' => 'Omni', 'slug' => 'omni', 'variants' => ['5 STR', '8 STR', 'Cargo']],
        19 => ['name' => 'Ritz', 'slug' => 'ritz', 'variants' => ['LXi', 'VXi', 'ZXi']],
        20 => ['name' => 'S-Cross', 'slug' => 's-cross', 'variants' => ['Sigma', 'Delta', 'Zeta', 'Alpha']],
        21 => ['name' => 'S-Presso', 'slug' => 's-presso', 'variants' => ['Std', 'LXi', 'VXi', 'VXi Plus']],
        22 => ['name' => 'Swift', 'slug' => 'swift', 'variants' => ['LXi', 'VXi', 'ZXi', 'ZXi Plus']],
        23 => ['name' => 'Swift Dzire', 'slug' => 'swift-dzire', 'alt_slug' => 'dzire', 'variants' => ['LXi', 'VXi', 'ZXi', 'ZXi Plus']],
        24 => ['name' => 'SX4', 'slug' => 'sx4', 'variants' => ['VXi', 'ZXi', 'ZXi Leather']],
        25 => ['name' => 'Versa', 'slug' => 'versa', 'variants' => ['DX', 'DX2']],
        26 => ['name' => 'Vitara Brezza', 'slug' => 'vitara-brezza', 'variants' => ['LDi', 'VDi', 'ZDi', 'ZDi Plus']],
        27 => ['name' => 'Wagon-R', 'slug' => 'wagon-r', 'alt_slug' => 'wagonr', 'variants' => ['LXi', 'VXi', 'ZXi']],
        28 => ['name' => 'XL6', 'slug' => 'xl6', 'variants' => ['Zeta', 'Alpha', 'Alpha Plus']],
        29 => ['name' => 'Zen', 'slug' => 'zen', 'variants' => ['LX', 'LXi', 'VX', 'VXi']],
        30 => ['name' => 'Zen Estilo', 'slug' => 'zen-estilo', 'variants' => ['LX', 'LXi', 'VXi']],
    ],

    // -------------------------------------------------------------
    // 2. SKODA (7 models from Image 2)
    // -------------------------------------------------------------
    'skoda' => [
        1 => ['name' => 'Fabia', 'slug' => 'fabia', 'variants' => ['Classic', 'Ambiente', 'Elegance']],
        2 => ['name' => 'Kushaq', 'slug' => 'kushaq', 'variants' => ['Active', 'Ambition', 'Style', 'Monte Carlo']],
        3 => ['name' => 'Laura', 'slug' => 'laura', 'variants' => ['Active', 'Ambition', 'Elegance', 'L&K']],
        4 => ['name' => 'Octavia', 'slug' => 'octavia', 'variants' => ['Ambition', 'Style', 'L&K', 'vRS']],
        5 => ['name' => 'Rapid', 'slug' => 'rapid', 'variants' => ['Rider', 'Ambition', 'Style', 'Monte Carlo']],
        6 => ['name' => 'Slavia', 'slug' => 'slavia', 'variants' => ['Active', 'Ambition', 'Style']],
        7 => ['name' => 'Superb', 'slug' => 'superb', 'variants' => ['Style', 'Sportline', 'L&K']],
    ],

    // -------------------------------------------------------------
    // 3. TOYOTA (9 models from Image 4)
    // -------------------------------------------------------------
    'toyota' => [
        1 => ['name' => 'Corolla Altis', 'slug' => 'corolla-altis', 'variants' => ['G', 'GL', 'VL']],
        2 => ['name' => 'Etios', 'slug' => 'etios', 'variants' => ['G', 'V', 'VX']],
        3 => ['name' => 'Fortuner', 'slug' => 'fortuner', 'variants' => ['4x2-MT', '4x2-AT', '4x4-MT', '4x4-AT', 'Legender', 'GR-S']],
        4 => ['name' => 'Glanza', 'slug' => 'glanza', 'variants' => ['E', 'S', 'G', 'V']],
        5 => ['name' => 'Innova', 'slug' => 'innova', 'variants' => ['G', 'GX', 'VX', 'ZX']],
        6 => ['name' => 'Innova Crysta', 'slug' => 'innova-crysta', 'variants' => ['GX', 'VX', 'ZX']],
        7 => ['name' => 'Innova Hycross', 'slug' => 'innova-hycross', 'variants' => ['GX', 'VX', 'ZX', 'ZX(O)']],
        8 => ['name' => 'Qualis', 'slug' => 'qualis', 'variants' => ['FS', 'GS', 'GST']],
        9 => ['name' => 'Urban Cruiser', 'slug' => 'urban-cruiser', 'variants' => ['Mid', 'High', 'Premium']],
    ],

    // -------------------------------------------------------------
    // 4. TATA (16 models from Image 5)
    // -------------------------------------------------------------
    'tata' => [
        1 => ['name' => 'Altroz', 'slug' => 'altroz', 'variants' => ['XE', 'XM', 'XT', 'XZ', 'XZ Plus']],
        2 => ['name' => 'Bolt', 'slug' => 'bolt', 'variants' => ['XE', 'XM', 'XMS', 'XT']],
        3 => ['name' => 'Curvv', 'slug' => 'curvv', 'variants' => ['Smart', 'Pure', 'Creative', 'Accomplished']],
        4 => ['name' => 'Harrier', 'slug' => 'harrier', 'variants' => ['Smart', 'Pure', 'Adventure', 'Fearless']],
        5 => ['name' => 'Hexa', 'slug' => 'hexa', 'variants' => ['XE', 'XM', 'XT', 'XTA']],
        6 => ['name' => 'Indica', 'slug' => 'indica', 'variants' => ['DLS', 'DLG', 'DLX', 'eV2']],
        7 => ['name' => 'Indigo', 'slug' => 'indigo', 'variants' => ['CS', 'XL', 'eCS']],
        8 => ['name' => 'Nano', 'slug' => 'nano', 'variants' => ['Std', 'CX', 'LX', 'GenX']],
        9 => ['name' => 'Nexon', 'slug' => 'nexon', 'variants' => ['Smart', 'Pure', 'Creative', 'Fearless']],
        10 => ['name' => 'Punch', 'slug' => 'punch', 'variants' => ['Pure', 'Adventure', 'Accomplished', 'Creative']],
        11 => ['name' => 'Safari', 'slug' => 'safari', 'variants' => ['Smart', 'Pure', 'Adventure', 'Accomplished']],
        12 => ['name' => 'Safari Dicor', 'slug' => 'safari-dicor', 'variants' => ['LX', 'EX', 'VX']],
        13 => ['name' => 'Safari Storme', 'slug' => 'safari-storme', 'variants' => ['LX', 'EX', 'VX']],
        14 => ['name' => 'Tiago', 'slug' => 'tiago', 'variants' => ['XE', 'XM', 'XT', 'XZ']],
        15 => ['name' => 'Tigor', 'slug' => 'tigor', 'variants' => ['XE', 'XM', 'XZ', 'XZ Plus']],
        16 => ['name' => 'Zest', 'slug' => 'zest', 'variants' => ['XE', 'XM', 'XMS', 'XT']],
    ],
];

foreach ($brandModels as $brandSlug => $modelsList) {
    $brand = Brand::where('slug', $brandSlug)->first();
    if (!$brand) {
        echo "Brand {$brandSlug} not found!\n";
        continue;
    }

    echo ">>> Processing Brand: {$brand->name} (Total Models: " . count($modelsList) . ")\n";

    foreach ($modelsList as $order => $mData) {
        $slug = $mData['slug'];
        $altSlug = $mData['alt_slug'] ?? null;
        $name = $mData['name'];
        $variants = $mData['variants'];

        // Find existing car model by slug or alt_slug
        $carModel = CarModel::where('brand_id', $brand->id)
            ->where(function ($q) use ($slug, $altSlug) {
                $q->where('slug', $slug);
                if ($altSlug) {
                    $q->orWhere('slug', $altSlug);
                }
            })
            ->first();

        // Check if badge image exists in public/images/models/
        $badgeImage = null;
        if (file_exists(public_path("images/models/{$slug}.svg"))) {
            $badgeImage = "images/models/{$slug}.svg";
        } elseif ($altSlug && file_exists(public_path("images/models/{$altSlug}.svg"))) {
            $badgeImage = "images/models/{$altSlug}.svg";
        }

        if ($carModel) {
            $carModel->update([
                'name' => $name,
                'slug' => $slug,
                'sort_order' => $order,
                'status' => true,
                'is_popular' => true,
                'badge_image' => $badgeImage ?: $carModel->badge_image,
            ]);
            echo "  [Updated #{$order}] {$carModel->name} (ID: {$carModel->id}, slug: {$carModel->slug})\n";
        } else {
            $carModel = CarModel::create([
                'brand_id' => $brand->id,
                'name' => $name,
                'slug' => $slug,
                'sort_order' => $order,
                'status' => true,
                'is_popular' => true,
                'badge_image' => $badgeImage,
            ]);
            echo "  [Created #{$order}] {$carModel->name} (ID: {$carModel->id}, slug: {$carModel->slug})\n";
        }

        // Sync Variants for this model
        $vOrder = 1;
        $firstVariant = null;
        foreach ($variants as $variantName) {
            $vSlug = Str::slug($carModel->slug . ' ' . $variantName);
            // Search existing variant by slug or by name under this car model
            $variant = ModelVariant::where('car_model_id', $carModel->id)
                ->where(function ($q) use ($vSlug, $variantName) {
                    $q->where('slug', $vSlug)->orWhere('name', $variantName);
                })
                ->first();

            if ($variant) {
                $variant->update([
                    'name' => $variantName,
                    'sort_order' => $vOrder++,
                    'status' => true,
                ]);
            } else {
                $variant = ModelVariant::create([
                    'car_model_id' => $carModel->id,
                    'name' => $variantName,
                    'slug' => $vSlug,
                    'sort_order' => $vOrder++,
                    'status' => true,
                ]);
            }

            if (!$firstVariant) {
                $firstVariant = $variant;
            }
        }

        // Check if car model has products. If 0 products mapped, create 5 products for the 5 categories!
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
                // Ensure unique SKU
                $sku = $sp['sku'];
                if (Product::where('sku', $sku)->exists()) {
                    $sku .= '-' . rand(10, 99);
                }

                $prod = Product::create([
                    'brand_id' => $brand->id,
                    'car_model_id' => $carModel->id,
                    'model_variant_id' => $firstVariant->id,
                    'primary_category_id' => $sp['cat']->id,
                    'name' => $sp['name'],
                    'slug' => Str::slug($sp['name'] . '-' . uniqid()),
                    'sku' => $sku,
                    'regular_price' => $sp['price'],
                    'sale_price' => $sp['price'] - 100,
                    'cost_price' => round($sp['price'] * 0.4, 2),
                    'stock_quantity' => 40,
                    'placement' => $sp['placement'],
                    'finish' => $sp['finish'],
                    'product_type' => 'logo',
                    'compatible_models_text' => "{$brand->name} {$carModel->name} ({$firstVariant->name})",
                    'short_description' => "Genuine OEM-spec compatible emblem badge for {$brand->name} {$carModel->name}.",
                    'full_description' => "High grade automotive ABS with triple chrome plating. Pre-applied automotive 3M adhesive tape for effortless peel-and-stick installation.",
                    'status' => true,
                    'is_featured' => true,
                ]);

                $prod->categories()->sync([$sp['cat']->id]);
            }
            echo "    -> Seeded 5 core category products for {$carModel->name}\n";
        }
    }
    echo "\n";
}

echo "=== SYNC COMPLETED SUCCESSFULLY ===\n";
