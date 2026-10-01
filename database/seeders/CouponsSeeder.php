<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CouponsSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'AUTO10',
                'description' => 'Get 10% instant discount on orders above &#x20B9;499.',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_order_amount' => 499.00,
                'max_discount_amount' => 200.00,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addMonths(6),
                'usage_limit' => 1000,
                'per_customer_limit' => 2,
                'status' => true,
            ],
            [
                'code' => 'WELCOME50',
                'description' => 'Flat &#x20B9;50 OFF on your first purchase.',
                'discount_type' => 'fixed',
                'discount_value' => 50,
                'min_order_amount' => 399.00,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addMonths(12),
                'usage_limit' => 5000,
                'per_customer_limit' => 1,
                'status' => true,
            ],
            [
                'code' => 'FESTIVE20',
                'description' => 'Mega Festive 20% discount on orders above &#x20B9;1499.',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'min_order_amount' => 1499.00,
                'max_discount_amount' => 500.00,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addMonths(3),
                'usage_limit' => 500,
                'per_customer_limit' => 1,
                'status' => true,
            ]
        ];

        foreach ($coupons as $c) {
            Coupon::updateOrCreate(['code' => $c['code']], $c);
        }
    }
}
