<?php

require 'D:/Laravel/skm/vendor/autoload.php';
$app = require 'D:/Laravel/skm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Cart;
use App\Models\CartItem;

echo "Carts count: " . Cart::count() . "\n";
foreach (Cart::with('items')->get() as $cart) {
    echo "Cart ID: {$cart->id}, User ID: {$cart->user_id}, Session ID: {$cart->session_id}\n";
    foreach ($cart->items as $item) {
        echo "   Item ID: {$item->id}, Product ID: {$item->product_id}, Qty: {$item->quantity}\n";
    }
}

echo "Orphan CartItems count: " . CartItem::whereDoesntHave('cart')->count() . "\n";
