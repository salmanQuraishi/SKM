<?php

require 'D:/Laravel/skm/vendor/autoload.php';
$app = require 'D:/Laravel/skm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelVariant;
use App\Models\Product;
use App\Models\User;

echo "=== VERIFYING DATABASE STATUS ===\n\n";

// 1. Check Brands & Models
$brands = ['hyundai', 'kia', 'fiat', 'datsun', 'chevrolet'];
foreach ($brands as $bSlug) {
    $b = Brand::where('slug', $bSlug)->first();
    if (!$b) {
        echo "[ERROR] Brand {$bSlug} not found!\n";
        continue;
    }
    $models = CarModel::where('brand_id', $b->id)->get();
    echo "Brand: {$b->name} (slug: {$b->slug}) | Total Models: {$models->count()}\n";
    foreach ($models as $m) {
        $pCount = Product::where('car_model_id', $m->id)->count();
        $vCount = ModelVariant::where('car_model_id', $m->id)->count();
        echo "   - {$m->name} (slug: {$m->slug}) | Variants: {$vCount} | Products: {$pCount}\n";
    }
    echo "\n";
}

// 2. Check Users Count
$users = User::all();
echo "Total Users: {$users->count()}\n";
foreach ($users as $u) {
    echo "   - {$u->email} (Role: " . ($u->role->name ?? 'None') . ")\n";
}
echo "\n";

// 3. Check tiago-xe empty state invariant
$tiagoXe = ModelVariant::where('slug', 'tiago-xe')->first();
if ($tiagoXe) {
    $txpCount = $tiagoXe->products()->count();
    echo "Tiago-XE variant product count: {$txpCount} (Expected: 0)\n";
} else {
    echo "Tiago-XE variant not found!\n";
}

echo "\n=== ALL VERIFICATIONS COMPLETE ===\n";
