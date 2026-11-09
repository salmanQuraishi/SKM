<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Shift existing top-level categories sort_order so header categories appear first
        $existingTopCats = Category::whereNull('parent_id')
            ->whereNotIn('slug', ['front-logo', 'rear-logo', 'bonnet-tailgate-letters', 'kit-full-monogram', 'single-name-badge', 'new-arrivals'])
            ->orderBy('sort_order')
            ->get();

        $offset = 10;
        foreach ($existingTopCats as $cat) {
            $cat->update([
                'sort_order' => $offset++,
                'is_featured' => false,
            ]);
        }

        // 2. Define the 6 Header Categories
        $headerCatsData = [
            [
                'name' => 'Front Logo',
                'slug' => 'front-logo',
                'icon' => 'fa-solid fa-shield-halved',
                'description' => 'Original car front radiator grille logos, monograms and emblems.',
                'sort_order' => 1,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Rear Logo',
                'slug' => 'rear-logo',
                'icon' => 'fa-solid fa-car-rear',
                'description' => 'Rear trunk lid, boot door car logos and chrome monograms.',
                'sort_order' => 2,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Bonnet & Letters',
                'slug' => 'bonnet-tailgate-letters',
                'icon' => 'fa-solid fa-font',
                'description' => 'Premium 3D raised bonnet and tailgate letterings & number sets.',
                'sort_order' => 3,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Full Monogram Kits',
                'slug' => 'kit-full-monogram',
                'icon' => 'fa-solid fa-boxes-stacked',
                'description' => 'Complete exterior monogram combo kits with front, rear, and model badges.',
                'sort_order' => 4,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Single Badges',
                'slug' => 'single-name-badge',
                'icon' => 'fa-solid fa-certificate',
                'description' => 'Model name chrome badges, engine badges, variant emblems.',
                'sort_order' => 5,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'New Arrivals',
                'slug' => 'new-arrivals',
                'icon' => 'fa-solid fa-sparkles',
                'description' => 'Latest added car logos, premium emblems, and new arrivals.',
                'sort_order' => 6,
                'is_featured' => true,
                'status' => true,
            ],
        ];

        $createdCats = [];
        foreach ($headerCatsData as $data) {
            $cat = Category::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
            $cat->update([
                'name' => $data['name'],
                'sort_order' => $data['sort_order'],
                'is_featured' => true,
                'status' => true,
                'icon' => $data['icon'],
            ]);
            $createdCats[$data['slug']] = $cat;
        }

        // 3. Map existing catalog products to these categories without detaching existing categories
        // Front Logo
        if (isset($createdCats['front-logo'])) {
            $frontProdIds = Product::where('placement', 'like', '%Front%')
                ->orWhere('name', 'like', '%Front%')
                ->pluck('id')
                ->toArray();
            if (!empty($frontProdIds)) {
                $createdCats['front-logo']->products()->syncWithoutDetaching($frontProdIds);
            }
        }

        // Rear Logo
        if (isset($createdCats['rear-logo'])) {
            $rearProdIds = Product::where('placement', 'like', '%Rear%')
                ->orWhere('name', 'like', '%Rear%')
                ->pluck('id')
                ->toArray();
            if (!empty($rearProdIds)) {
                $createdCats['rear-logo']->products()->syncWithoutDetaching($rearProdIds);
            }
        }

        // Bonnet & Letters
        if (isset($createdCats['bonnet-tailgate-letters'])) {
            $bonnetProdIds = Product::where('placement', 'like', '%Bonnet%')
                ->orWhere('name', 'like', '%bonnet%')
                ->orWhere('name', 'like', '%letters%')
                ->orWhere('primary_category_id', 12)
                ->orWhereIn('product_type', ['letters', 'lettering'])
                ->pluck('id')
                ->toArray();
            if (!empty($bonnetProdIds)) {
                $createdCats['bonnet-tailgate-letters']->products()->syncWithoutDetaching($bonnetProdIds);
            }
        }

        // Full Monogram Kits
        if (isset($createdCats['kit-full-monogram'])) {
            $kitProdIds = Product::where('is_monogram', 1)
                ->orWhere('placement', 'like', '%Exterior Kit%')
                ->orWhereIn('product_type', ['monogram', 'combo'])
                ->orWhere('primary_category_id', 11)
                ->orWhere('name', 'like', '%monogram%')
                ->orWhere('name', 'like', '%combo%')
                ->pluck('id')
                ->toArray();
            if (!empty($kitProdIds)) {
                $createdCats['kit-full-monogram']->products()->syncWithoutDetaching($kitProdIds);
            }
        }

        // Single Badges
        if (isset($createdCats['single-name-badge'])) {
            $badgeProdIds = Product::where('product_type', 'badge')
                ->orWhere('name', 'like', '%badge%')
                ->orWhere('primary_category_id', 10)
                ->pluck('id')
                ->toArray();
            if (!empty($badgeProdIds)) {
                $createdCats['single-name-badge']->products()->syncWithoutDetaching($badgeProdIds);
            }
        }

        // New Arrivals
        if (isset($createdCats['new-arrivals'])) {
            $newArrivalProdIds = Product::where('is_new_arrival', 1)->pluck('id')->toArray();
            if (!empty($newArrivalProdIds)) {
                $createdCats['new-arrivals']->products()->syncWithoutDetaching($newArrivalProdIds);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe down: do not delete data to preserve user records
    }
};
