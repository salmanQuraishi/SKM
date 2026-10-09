<?php

require 'D:/Laravel/skm/vendor/autoload.php';
$app = require 'D:/Laravel/skm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATABASE INTEGRITY CHECK ===\n";
echo "Brands count:         " . App\Models\Brand::count() . "\n";
echo "Car Models count:     " . App\Models\CarModel::count() . "\n";
echo "Model Variants count: " . App\Models\ModelVariant::count() . "\n";
echo "Categories count:     " . App\Models\Category::count() . "\n";
echo "Products count:       " . App\Models\Product::count() . "\n";
echo "Users count:          " . App\Models\User::count() . "\n";
echo "\nBrands list with models count:\n";
foreach (App\Models\Brand::orderBy('sort_order')->get() as $b) {
    echo "  - {$b->name} (slug: {$b->slug}): {$b->carModels()->count()} models\n";
}
