<?php

require 'D:/Laravel/skm/vendor/autoload.php';
$app = require 'D:/Laravel/skm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cats = App\Models\Category::whereNull('parent_id')->orderBy('sort_order')->get();
foreach ($cats as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | Slug: {$c->slug} | Status: {$c->status} | Sort: {$c->sort_order} | Products: {$c->products()->count()}\n";
}
