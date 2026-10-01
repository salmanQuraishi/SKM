<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingsSeeder::class,
            BrandsAndModelsSeeder::class,
            CategoriesSeeder::class,
            ProductsAndVariantsSeeder::class,
            BannersSeeder::class,
            CouponsSeeder::class,
            CmsPagesSeeder::class,
            OrdersAndReviewsSeeder::class,
        ]);
    }
}
