<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsAndVariantsSeeder extends Seeder
{
    public function run(): void
    {
        $hyundai = Brand::where('slug', 'hyundai')->first();
        $suzuki = Brand::where('slug', 'maruti-suzuki')->first();
        $tata = Brand::where('slug', 'tata')->first();
        $honda = Brand::where('slug', 'honda')->first();
        $toyota = Brand::where('slug', 'toyota')->first();
        $mahindra = Brand::where('slug', 'mahindra')->first();
        $kia = Brand::where('slug', 'kia')->first();
        $bmw = Brand::where('slug', 'bmw')->first();

        $catCarLogos = Category::where('slug', 'car-logos')->first();
        $catModelBadges = Category::where('slug', 'model-logos-badges')->first();
        $catMonograms = Category::where('slug', 'car-monograms')->first();
        $catLetters = Category::where('slug', 'letters-numbers')->first();
        $catAccessories = Category::where('slug', 'accessories')->first();

        $productsData = [
            // POPULAR CAR LOGOS (From Reference Image)
            [
                'name' => 'Hyundai Logo (Front)',
                'slug' => 'hyundai-logo-front',
                'sku' => 'HYU-LG-FRT-01',
                'brand_id' => $hyundai?->id,
                'primary_category_id' => $catCarLogos?->id,
                'product_type' => 'logo',
                'placement' => 'Front',
                'finish' => 'Chrome',
                'compatible_models_text' => 'All Models',
                'regular_price' => 699.00,
                'sale_price' => 499.00,
                'stock_quantity' => 45,
                'is_featured' => true,
                'is_best_seller' => true,
                'image' => 'images/products/hyundai-logo-front.svg',
                'short_description' => 'Original OE-spec replacement front radiator grille chrome emblem badge for all Hyundai cars.',
                'full_description' => '<p>Upgrade your Hyundai car with this genuine OEM-specification front chrome logo badge. Engineered with high-grade electroplated ABS chrome that does not fade, tarnish or peel under harsh Indian sunlight and weather.</p><ul><li>Direct replacement for lost or damaged front logos</li><li>Pre-fitted with authentic heavy-duty 3M automotive adhesive tape and guide pins</li><li>Triple-layered mirror chrome plating</li><li>100% weather and car-wash resistant</li></ul>',
                'specifications' => [
                    'Material' => 'Electroplated Automotive ABS Chrome',
                    'Fitment' => 'Front Radiator Grille',
                    'Dimensions' => '150mm x 75mm',
                    'Attachment' => '3M Double Sided Tape + Guide Alignment Pins',
                    'Warranty' => '1 Year Anti-Rust/Anti-Fade Replacement Guarantee'
                ],
                'variants' => [
                    ['name' => 'Standard Silver Chrome', 'sku' => 'HYU-LG-FRT-01-CHR', 'regular_price' => 699, 'sale_price' => 499, 'stock_quantity' => 30, 'finish' => 'Chrome'],
                    ['name' => 'Gloss Piano Black', 'sku' => 'HYU-LG-FRT-01-BLK', 'regular_price' => 799, 'sale_price' => 549, 'stock_quantity' => 15, 'finish' => 'Gloss Black'],
                ]
            ],
            [
                'name' => 'Hyundai Logo (Rear)',
                'slug' => 'hyundai-logo-rear',
                'sku' => 'HYU-LG-RR-02',
                'brand_id' => $hyundai?->id,
                'primary_category_id' => $catCarLogos?->id,
                'product_type' => 'logo',
                'placement' => 'Rear',
                'finish' => 'Chrome',
                'compatible_models_text' => 'All Models',
                'regular_price' => 699.00,
                'sale_price' => 499.00,
                'stock_quantity' => 50,
                'is_featured' => true,
                'is_best_seller' => true,
                'image' => 'images/products/hyundai-logo-rear.svg',
                'short_description' => 'Premium trunk boot-lid chrome H emblem badge compatible with all Hyundai sedan and SUV models.',
                'full_description' => '<p>High precision rear trunk chrome logo emblem for Hyundai vehicles. Perfect curvature and contour matching factory boot-lids.</p>',
                'specifications' => [
                    'Material' => 'Triple Plated Chrome ABS',
                    'Fitment' => 'Rear Trunk / Boot Lid',
                    'Dimensions' => '130mm x 65mm',
                    'Attachment' => 'Pre-applied 3M Automotive Adhesive Tape',
                ]
            ],
            [
                'name' => 'Honda Logo (Front)',
                'slug' => 'honda-logo-front',
                'sku' => 'HON-LG-FRT-01',
                'brand_id' => $honda?->id,
                'primary_category_id' => $catCarLogos?->id,
                'product_type' => 'logo',
                'placement' => 'Front',
                'finish' => 'Chrome',
                'compatible_models_text' => 'All Models',
                'regular_price' => 699.00,
                'sale_price' => 499.00,
                'stock_quantity' => 40,
                'is_featured' => true,
                'image' => 'images/products/honda-logo-front.svg',
                'short_description' => 'Original front grille chrome H emblem for Honda City, Amaze, Elevate, Civic and Jazz.',
                'full_description' => '<p>OEM-grade front grille H logo badge with deep chrome luster and precise alignment pins.</p>',
                'specifications' => [
                    'Material' => 'Chrome Plated ABS Plastic',
                    'Fitment' => 'Front Bumper / Grille',
                    'Dimensions' => '125mm x 100mm',
                ]
            ],
            [
                'name' => 'Honda Logo (Rear)',
                'slug' => 'honda-logo-rear',
                'sku' => 'HON-LG-RR-02',
                'brand_id' => $honda?->id,
                'primary_category_id' => $catCarLogos?->id,
                'product_type' => 'logo',
                'placement' => 'Rear',
                'finish' => 'Chrome',
                'compatible_models_text' => 'All Models',
                'regular_price' => 699.00,
                'sale_price' => 499.00,
                'stock_quantity' => 35,
                'is_featured' => true,
                'image' => 'images/products/honda-logo-rear.svg',
                'short_description' => 'Trunk tailgate chrome H logo emblem for Honda car models.',
                'full_description' => '<p>Precision-molded rear emblem badge with high durability and mirror shine finish.</p>',
                'specifications' => [
                    'Material' => 'Chrome Plated ABS',
                    'Fitment' => 'Rear Boot Lid',
                    'Dimensions' => '95mm x 78mm',
                ]
            ],
            [
                'name' => 'Maruti Suzuki Logo',
                'slug' => 'maruti-suzuki-logo',
                'sku' => 'SUZ-LG-01',
                'brand_id' => $suzuki?->id,
                'primary_category_id' => $catCarLogos?->id,
                'product_type' => 'logo',
                'placement' => 'All',
                'finish' => 'Chrome',
                'compatible_models_text' => 'All Models',
                'regular_price' => 599.00,
                'sale_price' => 399.00,
                'stock_quantity' => 60,
                'is_featured' => true,
                'is_best_seller' => true,
                'image' => 'images/products/maruti-suzuki-logo.svg',
                'short_description' => 'Original 3D chrome S logo emblem for Maruti Suzuki Swift, Baleno, Brezza, Ertiga, WagonR and Dzire.',
                'full_description' => '<p>OEM factory standard 3D S badge with brilliant chrome finish. Comes with pre-applied laser cut adhesive backing.</p>',
                'specifications' => [
                    'Material' => 'ABS Polymer with Chrome Plating',
                    'Fitment' => 'Front / Rear Universal Fit',
                    'Dimensions' => '85mm x 85mm',
                ]
            ],
            [
                'name' => 'Tata Logo',
                'slug' => 'tata-logo',
                'sku' => 'TAT-LG-01',
                'brand_id' => $tata?->id,
                'primary_category_id' => $catCarLogos?->id,
                'product_type' => 'logo',
                'placement' => 'All',
                'finish' => 'Chrome',
                'compatible_models_text' => 'All Models',
                'regular_price' => 599.00,
                'sale_price' => 399.00,
                'stock_quantity' => 55,
                'is_featured' => true,
                'is_best_seller' => true,
                'image' => 'images/products/tata-logo.svg',
                'short_description' => 'Original Tata Motors 3D chrome emblem for Nexon, Harrier, Safari, Punch, Altroz and Tiago.',
                'full_description' => '<p>Genuine Tata Motors chrome ring badge with distinctive inner lines and crystal shine.</p>',
                'specifications' => [
                    'Material' => 'Chrome Plated ABS',
                    'Fitment' => 'Front Grille and Rear Tailgate',
                    'Dimensions' => '115mm x 85mm',
                ]
            ],
            [
                'name' => 'Toyota Logo (Front & Rear)',
                'slug' => 'toyota-logo-front-rear',
                'sku' => 'TOY-LG-01',
                'brand_id' => $toyota?->id,
                'primary_category_id' => $catCarLogos?->id,
                'product_type' => 'logo',
                'placement' => 'Front',
                'finish' => 'Chrome',
                'compatible_models_text' => 'Fortuner, Innova, Urban Cruiser',
                'regular_price' => 699.00,
                'sale_price' => 499.00,
                'stock_quantity' => 38,
                'is_featured' => true,
                'image' => 'images/products/toyota-logo-front.svg',
                'short_description' => 'Toyota 3D interlocking ovals emblem badge in brilliant chrome.',
                'full_description' => '<p>High-end Toyota emblem for Fortuner, Innova Crysta, Hycross, and Glanza.</p>',
                'specifications' => [
                    'Material' => 'Chrome ABS',
                    'Dimensions' => '140mm x 95mm',
                ]
            ],
            [
                'name' => 'Mahindra Twin Peaks Logo',
                'slug' => 'mahindra-twin-peaks-logo',
                'sku' => 'MAH-TP-01',
                'brand_id' => $mahindra?->id,
                'primary_category_id' => $catCarLogos?->id,
                'product_type' => 'logo',
                'placement' => 'All',
                'finish' => 'Chrome',
                'compatible_models_text' => 'XUV700, Scorpio-N, Thar, XUV300',
                'regular_price' => 649.00,
                'sale_price' => 449.00,
                'stock_quantity' => 42,
                'is_featured' => true,
                'image' => 'images/products/mahindra-twin-peaks.svg',
                'short_description' => 'New Mahindra Twin Peaks chrome logo emblem for modern Mahindra SUVs.',
                'full_description' => '<p>Authentic twin-peaks badge in shimmering chrome finish for XUV700, Scorpio-N and Thar.</p>',
                'specifications' => [
                    'Material' => 'Chrome ABS Metalized',
                    'Dimensions' => '130mm x 80mm',
                ]
            ],

            // CAR MONOGRAMS (From Reference Image)
            [
                'name' => 'S6 Sport Monogram',
                'slug' => 's6-sport-monogram',
                'sku' => 'MONO-S6-01',
                'primary_category_id' => $catMonograms?->id,
                'product_type' => 'monogram',
                'finish' => 'Chrome / Red Accent',
                'compatible_models_text' => 'Universal All Cars',
                'regular_price' => 499.00,
                'sale_price' => 349.00,
                'stock_quantity' => 70,
                'is_monogram' => true,
                'is_new_arrival' => true,
                'image' => 'images/monograms/s6.svg',
                'short_description' => 'Sport 3D metallic trunk badge with dynamic red accent styling.',
                'full_description' => '<p>Gives an aggressive sporty aesthetic to any sedan, hatchback or SUV boot lid.</p>',
                'specifications' => ['Material' => 'Zinc Metal Alloy', 'Attachment' => '3M Tape']
            ],
            [
                'name' => 'M Power Monogram',
                'slug' => 'm-power-monogram',
                'sku' => 'MONO-M-02',
                'brand_id' => $bmw?->id,
                'primary_category_id' => $catMonograms?->id,
                'product_type' => 'monogram',
                'finish' => 'Tri-Color Chrome',
                'compatible_models_text' => 'BMW & Universal',
                'regular_price' => 599.00,
                'sale_price' => 399.00,
                'stock_quantity' => 65,
                'is_monogram' => true,
                'is_new_arrival' => true,
                'image' => 'images/monograms/m-power.svg',
                'short_description' => 'Famous Motorsport 3D Tri-color stripes metal trunk emblem.',
                'full_description' => '<p>Heavyweight solid zinc alloy badge with light blue, dark blue and red enamel stripes.</p>',
                'specifications' => ['Material' => 'Zinc Alloy Metal', 'Dimensions' => '85mm x 32mm']
            ],
            [
                'name' => 'AMG Monogram',
                'slug' => 'amg-monogram',
                'sku' => 'MONO-AMG-03',
                'primary_category_id' => $catMonograms?->id,
                'product_type' => 'monogram',
                'finish' => 'Chrome / Silver',
                'compatible_models_text' => 'Universal',
                'regular_price' => 599.00,
                'sale_price' => 399.00,
                'stock_quantity' => 60,
                'is_monogram' => true,
                'is_new_arrival' => true,
                'image' => 'images/monograms/amg.svg',
                'short_description' => '3D Chrome AMG performance lettering emblem badge.',
                'full_description' => '<p>Precision cut AMG letters with triple chrome plating and self-adhesive mounting.</p>',
                'specifications' => ['Material' => 'ABS Chrome Metalized', 'Dimensions' => '185mm x 18mm']
            ],
            [
                'name' => 'RS Monogram',
                'slug' => 'rs-monogram',
                'sku' => 'MONO-RS-04',
                'primary_category_id' => $catMonograms?->id,
                'product_type' => 'monogram',
                'finish' => 'Red / Chrome',
                'compatible_models_text' => 'Universal',
                'regular_price' => 499.00,
                'sale_price' => 349.00,
                'stock_quantity' => 55,
                'is_monogram' => true,
                'is_new_arrival' => true,
                'image' => 'images/monograms/rs.svg',
                'short_description' => 'Sporty RS metal badge in bold red and chrome finish.',
                'full_description' => '<p>Universal fit RS badge for rear boot lid, grille or fenders.</p>',
                'specifications' => ['Material' => 'Solid Metal Alloy', 'Dimensions' => '105mm x 25mm']
            ],
            [
                'name' => 'Type R Monogram',
                'slug' => 'type-r-monogram',
                'sku' => 'MONO-TYPER-05',
                'brand_id' => $honda?->id,
                'primary_category_id' => $catMonograms?->id,
                'product_type' => 'monogram',
                'finish' => 'Chrome / Racing Red',
                'compatible_models_text' => 'Honda & Universal',
                'regular_price' => 599.00,
                'sale_price' => 399.00,
                'stock_quantity' => 50,
                'is_monogram' => true,
                'is_new_arrival' => true,
                'image' => 'images/monograms/type-r.svg',
                'short_description' => 'High performance Type R racing badge in gloss red and chrome.',
                'full_description' => '<p>Classic Type R racing badge with deep red filling and chrome outline.</p>',
                'specifications' => ['Material' => 'Solid Metal Alloy', 'Dimensions' => '120mm x 30mm']
            ],
            [
                'name' => 'TRD Monogram',
                'slug' => 'trd-monogram',
                'sku' => 'MONO-TRD-06',
                'brand_id' => $toyota?->id,
                'primary_category_id' => $catMonograms?->id,
                'product_type' => 'monogram',
                'finish' => 'Chrome / Red',
                'compatible_models_text' => 'Toyota & Universal',
                'regular_price' => 499.00,
                'sale_price' => 349.00,
                'stock_quantity' => 50,
                'is_monogram' => true,
                'is_new_arrival' => true,
                'image' => 'images/monograms/trd.svg',
                'short_description' => 'Toyota Racing Development TRD 3D metal emblem badge.',
                'full_description' => '<p>TRD emblem badge for Toyota Fortuner, Hilux, Innova and Urban Cruiser.</p>',
                'specifications' => ['Material' => 'Solid Metal', 'Dimensions' => '140mm x 22mm']
            ],
            [
                'name' => 'VIP Monogram',
                'slug' => 'vip-monogram',
                'sku' => 'MONO-VIP-07',
                'primary_category_id' => $catMonograms?->id,
                'product_type' => 'monogram',
                'finish' => 'Gold / Chrome',
                'compatible_models_text' => 'Universal',
                'regular_price' => 449.00,
                'sale_price' => 299.00,
                'stock_quantity' => 75,
                'is_monogram' => true,
                'is_new_arrival' => true,
                'image' => 'images/monograms/vip.svg',
                'short_description' => 'Luxury VIP 3D metallic gold/chrome emblem badge for rear pillars or trunk.',
                'full_description' => '<p>Premium VIP badge adds luxury styling to any vehicle.</p>',
                'specifications' => ['Material' => 'Zinc Alloy', 'Dimensions' => '90mm x 25mm']
            ],
            [
                'name' => 'HKS Monogram',
                'slug' => 'hks-monogram',
                'sku' => 'MONO-HKS-08',
                'primary_category_id' => $catMonograms?->id,
                'product_type' => 'monogram',
                'finish' => 'Black / Chrome',
                'compatible_models_text' => 'Universal',
                'regular_price' => 499.00,
                'sale_price' => 349.00,
                'stock_quantity' => 45,
                'is_monogram' => true,
                'is_new_arrival' => true,
                'image' => 'images/monograms/hks.svg',
                'short_description' => 'Tuning badge in solid metal finish with custom color block.',
                'full_description' => '<p>HKS power badge for car enthusiasts.</p>',
                'specifications' => ['Material' => 'Solid Metal', 'Dimensions' => '120mm x 25mm']
            ],

            // MODEL BADGES & ACCESSORIES
            [
                'name' => 'CRETA Chrome Lettering Badge',
                'slug' => 'creta-chrome-lettering-badge',
                'sku' => 'MOD-CRETA-01',
                'brand_id' => $hyundai?->id,
                'primary_category_id' => $catModelBadges?->id,
                'product_type' => 'badge',
                'finish' => 'Chrome',
                'compatible_models_text' => 'Hyundai Creta (All Years)',
                'regular_price' => 699.00,
                'sale_price' => 499.00,
                'stock_quantity' => 50,
                'is_featured' => true,
                'image' => 'images/products/creta-lettering.svg',
                'short_description' => '3D Chrome individual letters spelling CRETA for tailgate placement.',
                'full_description' => '<p>Precision spaced CRETA letters with alignment stencil for easy DIY installation.</p>',
            ],
            [
                'name' => 'VERNA Chrome Trunk Badge',
                'slug' => 'verna-chrome-trunk-badge',
                'sku' => 'MOD-VERNA-02',
                'brand_id' => $hyundai?->id,
                'primary_category_id' => $catModelBadges?->id,
                'product_type' => 'badge',
                'finish' => 'Chrome',
                'compatible_models_text' => 'Hyundai Verna (All Years)',
                'regular_price' => 649.00,
                'sale_price' => 449.00,
                'stock_quantity' => 40,
                'is_featured' => true,
                'image' => 'images/products/verna-badge.svg',
                'short_description' => 'Original 3D chrome VERNA nameplate badge for rear boot lid.',
                'full_description' => '<p>OE replacement VERNA badge matching exact Hyundai factory typography.</p>',
            ],
            [
                'name' => 'SWIFT 3D Chrome Emblem',
                'slug' => 'swift-3d-chrome-emblem',
                'sku' => 'MOD-SWIFT-03',
                'brand_id' => $suzuki?->id,
                'primary_category_id' => $catModelBadges?->id,
                'product_type' => 'badge',
                'finish' => 'Chrome',
                'compatible_models_text' => 'Maruti Suzuki Swift',
                'regular_price' => 549.00,
                'sale_price' => 399.00,
                'stock_quantity' => 65,
                'is_featured' => true,
                'image' => 'images/products/swift-emblem.svg',
                'short_description' => 'Chrome SWIFT badge for boot lid.',
                'full_description' => '<p>High-gloss chrome SWIFT badge with durable adhesive.</p>',
            ],
            [
                'name' => 'FORTUNER 3D Bonnet Chrome Letters',
                'slug' => 'fortuner-3d-bonnet-chrome-letters',
                'sku' => 'MOD-FORT-04',
                'brand_id' => $toyota?->id,
                'primary_category_id' => $catModelBadges?->id,
                'product_type' => 'badge',
                'finish' => 'Chrome',
                'compatible_models_text' => 'Toyota Fortuner',
                'regular_price' => 899.00,
                'sale_price' => 599.00,
                'stock_quantity' => 30,
                'is_featured' => true,
                'image' => 'images/products/fortuner-bonnet-letters.svg',
                'short_description' => 'Bold 3D chrome FORTUNER letters for front bonnet hood with alignment template.',
                'full_description' => '<p>Thick 4mm 3D chrome letters for front bonnet hood giving an imposing road presence.</p>',
            ],
            [
                'name' => 'Thar 4x4 Metal Fender Badge',
                'slug' => 'thar-4x4-metal-fender-badge',
                'sku' => 'MOD-THAR-05',
                'brand_id' => $mahindra?->id,
                'primary_category_id' => $catModelBadges?->id,
                'product_type' => 'badge',
                'finish' => 'Matte Black / Red',
                'compatible_models_text' => 'Mahindra Thar, Scorpio, Jimny',
                'regular_price' => 699.00,
                'sale_price' => 499.00,
                'stock_quantity' => 45,
                'is_featured' => true,
                'image' => 'images/products/thar-4x4-badge.svg',
                'short_description' => 'Solid heavy metal 4x4 emblem badge for off-road fenders and tailgate.',
                'full_description' => '<p>Heavy metal 4x4 badge for rugged off-road vehicles.</p>',
            ],
            [
                'name' => 'Letters & Numbers Pack (A-Z, 0-9)',
                'slug' => 'letters-numbers-pack',
                'sku' => 'ACC-LET-01',
                'primary_category_id' => $catLetters?->id,
                'product_type' => 'letters',
                'finish' => 'Chrome',
                'compatible_models_text' => 'Universal',
                'regular_price' => 249.00,
                'sale_price' => 149.00,
                'stock_quantity' => 200,
                'image' => 'images/products/letters-numbers-pack.svg',
                'short_description' => 'Individual 3D chrome letters and numbers to create custom badges.',
                'full_description' => '<p>Create your custom car name or trim badge with our individual chrome letters and numbers.</p>',
            ],
            [
                'name' => 'Universal Steering Wheel Chrome Ring Emblem',
                'slug' => 'universal-steering-wheel-chrome-ring',
                'sku' => 'ACC-STR-02',
                'primary_category_id' => $catAccessories?->id,
                'product_type' => 'accessory',
                'finish' => 'Chrome',
                'compatible_models_text' => 'Universal',
                'regular_price' => 399.00,
                'sale_price' => 299.00,
                'stock_quantity' => 80,
                'image' => 'images/products/universal-steering-badge.svg',
                'short_description' => 'Decorative 3D chrome inner circle accent ring for steering wheel horn center.',
                'full_description' => '<p>Refurbish your steering wheel center emblem with this chrome accent ring.</p>',
            ],
            [
                'name' => 'Wheel Center Hub Cap Set (4 Pcs)',
                'slug' => 'wheel-center-hub-cap-set-4-pcs',
                'sku' => 'ACC-WHL-03',
                'primary_category_id' => $catAccessories?->id,
                'product_type' => 'accessory',
                'finish' => 'Chrome / Black',
                'compatible_models_text' => 'Universal 56mm / 60mm Alloy Wheels',
                'regular_price' => 899.00,
                'sale_price' => 599.00,
                'stock_quantity' => 40,
                'image' => 'images/products/wheel-center-caps.svg',
                'short_description' => 'Set of 4 metallic chrome center wheel cap hub badges.',
                'full_description' => '<p>Set of 4 center caps to elevate the look of your alloy wheels.</p>',
            ],
        ];

        foreach ($productsData as $pData) {
            $image = $pData['image'] ?? null;
            unset($pData['image']);
            $variants = $pData['variants'] ?? [];
            unset($pData['variants']);

            $product = Product::updateOrCreate(['sku' => $pData['sku']], $pData);

            // Create primary image
            if ($image) {
                $product->images()->delete();
                $product->images()->create([
                    'image_path' => $image,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

            // Sync category
            if ($product->primary_category_id) {
                $product->categories()->sync([$product->primary_category_id]);
            }

            // Create variants
            if (!empty($variants)) {
                $product->variants()->delete();
                foreach ($variants as $var) {
                    $product->variants()->create([
                        'name' => $var['name'],
                        'sku' => $var['sku'],
                        'regular_price' => $var['regular_price'],
                        'sale_price' => $var['sale_price'],
                        'stock_quantity' => $var['stock_quantity'],
                        'finish' => $var['finish'] ?? 'Chrome',
                        'status' => true,
                    ]);
                }
            }
        }
    }
}
