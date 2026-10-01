<?php

require 'D:/Laravel/skm/vendor/autoload.php';
$app = require 'D:/Laravel/skm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;

// Deactivate old categories so only the 5 new ones are active
$activeSlugs = [
    'front-logo',
    'rear-logo',
    'bonnet-tailgate-letters',
    'kit-full-monogram',
    'single-name-badge',
];

Category::whereNotIn('slug', $activeSlugs)->update(['status' => false]);
Category::whereIn('slug', $activeSlugs)->update(['status' => true]);

echo "Active Categories:\n";
$activeCats = Category::active()->whereNull('parent_id')->orderBy('sort_order')->get();
foreach ($activeCats as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | Slug: {$c->slug} | Prods: {$c->products()->count()}\n";
}
