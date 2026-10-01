<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\ModelVariant;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AllCompaniesProductsSeeder extends Seeder
{
    public function run(): void
    {
        $catLogos = Category::where('slug', 'car-logos')->first() ?? Category::find(1);
        $catBadges = Category::where('slug', 'model-logos-badges')->first() ?? Category::find(10);
        $catMonograms = Category::where('slug', 'car-monograms')->first() ?? Category::find(11);
        $catLetters = Category::where('slug', 'letters-numbers')->first() ?? Category::find(12);
        $catAccessories = Category::where('slug', 'accessories')->first() ?? Category::find(15);

        $brands = [
            // ==========================================
            // 1. HYUNDAI
            // ==========================================
            'hyundai' => [
                'creta' => [
                    'variants' => ['E', 'EX', 'S', 'SX', 'SX(O)'],
                    'products' => [
                        'creta-e' => [
                            [
                                'name' => 'Front Radiator Grille Chrome H Emblem - Hyundai Creta E',
                                'sku' => 'HYU-CRT-E-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 799,
                                'sale_price' => 599,
                                'stock' => 30,
                                'image' => 'images/products/hyundai-logo-front.svg',
                                'short' => 'OE-spec front radiator grille chrome logo designed for Hyundai Creta E.',
                            ],
                            [
                                'name' => 'Steering Wheel Chrome Badge - Hyundai Creta E',
                                'sku' => 'HYU-CRT-E-STR-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 499,
                                'sale_price' => 349,
                                'stock' => 40,
                                'image' => 'images/products/universal-steering-badge.svg',
                                'short' => 'Interior steering wheel center replacement emblem for Hyundai Creta E.',
                            ]
                        ],
                        'creta-ex' => [
                            [
                                'name' => 'Rear Trunk Chrome H Logo - Hyundai Creta EX',
                                'sku' => 'HYU-CRT-EX-RR-LGO',
                                'category_id' => $catLogos->id,
                                'regular_price' => 699,
                                'sale_price' => 499,
                                'stock' => 25,
                                'image' => 'images/products/hyundai-logo-rear.svg',
                                'short' => 'Original specification rear trunk chrome emblem for Hyundai Creta EX.',
                            ]
                        ],
                        'creta-s' => [
                            [
                                'name' => 'CRETA Tailgate Chrome Letters Monogram - Hyundai Creta S',
                                'sku' => 'HYU-CRT-S-LET-MON',
                                'category_id' => $catLetters->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 35,
                                'image' => 'images/products/creta-lettering.svg',
                                'short' => '3D individual chrome letters for rear boot lid of Hyundai Creta S.',
                            ]
                        ],
                        'creta-sxo' => [
                            [
                                'name' => 'High-Gloss Piano Black Grille Emblem - Hyundai Creta SX(O)',
                                'sku' => 'HYU-CRT-SXO-BLK-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 1199,
                                'sale_price' => 899,
                                'stock' => 20,
                                'image' => 'images/products/hyundai-logo-front.svg',
                                'short' => 'Premium piano black front grille emblem designed for Creta SX(O) top trim.',
                            ]
                        ]
                    ]
                ],
                'verna' => [
                    'variants' => ['EX', 'S', 'SX', 'SX(O)'],
                    'products' => [
                        'verna-sx' => [
                            [
                                'name' => 'Dark Chrome Front Emblem - Hyundai Verna SX',
                                'sku' => 'HYU-VRN-SX-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 28,
                                'image' => 'images/products/hyundai-logo-front.svg',
                                'short' => 'Parametric dark chrome front emblem for Hyundai Verna SX.',
                            ],
                            [
                                'name' => 'VERNA 3D Chrome Tailgate Monogram - Hyundai Verna SX',
                                'sku' => 'HYU-VRN-SX-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 799,
                                'sale_price' => 599,
                                'stock' => 32,
                                'image' => 'images/products/verna-badge.svg',
                                'short' => 'Original style rear trunk Verna badge for Hyundai Verna SX.',
                            ]
                        ],
                        'verna-sxo' => [
                            [
                                'name' => 'Turbo Metallic Red Boot Badge - Hyundai Verna SX(O)',
                                'sku' => 'HYU-VRN-SXO-TUR-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 699,
                                'sale_price' => 499,
                                'stock' => 45,
                                'image' => 'images/products/verna-badge.svg',
                                'short' => 'Original Turbo 1.5 TGDi boot badge for Hyundai Verna SX(O).',
                            ]
                        ]
                    ]
                ],
                'venue' => [
                    'variants' => ['E', 'S', 'S-Plus', 'SX', 'SX(O)'],
                    'products' => [
                        'venue-s' => [
                            [
                                'name' => 'Front Radiator Dark Chrome Emblem - Hyundai Venue S',
                                'sku' => 'HYU-VEN-S-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 849,
                                'sale_price' => 649,
                                'stock' => 30,
                                'image' => 'images/products/hyundai-logo-front.svg',
                                'short' => 'Dark chrome radiator grille emblem for Hyundai Venue S.',
                            ]
                        ],
                        'venue-sx' => [
                            [
                                'name' => 'VENUE 3D Chrome Tailgate Badge - Hyundai Venue SX',
                                'sku' => 'HYU-VEN-SX-RR-BDG',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 749,
                                'sale_price' => 549,
                                'stock' => 25,
                                'image' => 'images/products/hyundai-logo-rear.svg',
                                'short' => 'Rear tailgate chrome lettering badge for Hyundai Venue SX.',
                            ]
                        ]
                    ]
                ],
                'i20' => [
                    'variants' => ['Era', 'Magna', 'Sportz', 'Asta', 'N-Line'],
                    'products' => [
                        'i20-sportz' => [
                            [
                                'name' => 'Front Grille Chrome Emblem - Hyundai i20 Sportz',
                                'sku' => 'HYU-I20-SPZ-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 749,
                                'sale_price' => 549,
                                'stock' => 35,
                                'image' => 'images/products/hyundai-logo-front.svg',
                                'short' => 'Direct replacement front radiator grille logo for Hyundai i20 Sportz.',
                            ]
                        ],
                        'i20-n-line' => [
                            [
                                'name' => 'N-Line Red Accented Front Grille Metal Badge - Hyundai i20 N-Line',
                                'sku' => 'HYU-I20-NLN-MET-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 999,
                                'sale_price' => 799,
                                'stock' => 20,
                                'image' => 'images/products/hyundai-logo-front.svg',
                                'short' => 'OEM specification N Line metal badge with bright red enamel inlay for i20 N-Line.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 2. MARUTI SUZUKI
            // ==========================================
            'maruti-suzuki' => [
                'swift' => [
                    'variants' => ['LXi', 'VXi', 'ZXi', 'ZXi-Plus'],
                    'products' => [
                        'swift-vxi' => [
                            [
                                'name' => 'Front Grille Suzuki S 3D Chrome Emblem - Maruti Swift VXi',
                                'sku' => 'MS-SWF-VXI-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 699,
                                'sale_price' => 499,
                                'stock' => 50,
                                'image' => 'images/products/maruti-suzuki-logo.svg',
                                'short' => 'OEM chrome front radiator S logo designed for Maruti Suzuki Swift VXi.',
                            ],
                            [
                                'name' => 'Swift Tailgate Chrome Lettering Badge - Maruti Swift VXi',
                                'sku' => 'MS-SWF-VXI-RR-BDG',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 599,
                                'sale_price' => 399,
                                'stock' => 45,
                                'image' => 'images/products/swift-emblem.svg',
                                'short' => 'Original tailgate Swift chrome insignia for Maruti Swift VXi.',
                            ]
                        ],
                        'swift-zxi' => [
                            [
                                'name' => 'Gloss Black Sport S Front Grille Logo - Maruti Swift ZXi',
                                'sku' => 'MS-SWF-ZXI-BLK-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 849,
                                'sale_price' => 599,
                                'stock' => 35,
                                'image' => 'images/products/maruti-suzuki-logo.svg',
                                'short' => 'Sport edition gloss black front S emblem for Maruti Swift ZXi.',
                            ],
                            [
                                'name' => 'Front Ceramic Brake Pad Set - Maruti Swift ZXi',
                                'sku' => 'MS-SWF-ZXI-BRK-PAD',
                                'category_id' => $catAccessories->id,
                                'regular_price' => 1499,
                                'sale_price' => 1199,
                                'stock' => 20,
                                'image' => 'images/products/maruti-suzuki-logo.svg',
                                'short' => 'High performance ceramic front brake pads for Maruti Swift ZXi.',
                            ]
                        ],
                        'swift-zxi-plus' => [
                            [
                                'name' => 'Wheel Center Hub Caps Set of 4 (54mm) - Maruti Swift ZXi+',
                                'sku' => 'MS-SWF-ZXIP-WHL-CAP',
                                'category_id' => $catAccessories->id,
                                'regular_price' => 799,
                                'sale_price' => 549,
                                'stock' => 30,
                                'image' => 'images/products/wheel-center-caps.svg',
                                'short' => 'Set of 4 chrome Suzuki S wheel center caps for Swift ZXi+ alloy wheels.',
                            ]
                        ]
                    ]
                ],
                'baleno' => [
                    'variants' => ['Sigma', 'Delta', 'Zeta', 'Alpha'],
                    'products' => [
                        'baleno-delta' => [
                            [
                                'name' => 'Front Wave Grille S Chrome Emblem - Maruti Baleno Delta',
                                'sku' => 'MS-BAL-DLT-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 749,
                                'sale_price' => 529,
                                'stock' => 40,
                                'image' => 'images/products/maruti-suzuki-logo.svg',
                                'short' => 'Wave pattern front chrome grille emblem for Maruti Suzuki Baleno Delta.',
                            ]
                        ],
                        'baleno-alpha' => [
                            [
                                'name' => 'BALENO 3D Rear Boot Chrome Monogram - Maruti Baleno Alpha',
                                'sku' => 'MS-BAL-ALP-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 699,
                                'sale_price' => 499,
                                'stock' => 30,
                                'image' => 'images/products/swift-emblem.svg',
                                'short' => 'Tailgate chrome Baleno insignia with pre-applied 3M tape for Baleno Alpha.',
                            ]
                        ]
                    ]
                ],
                'brezza' => [
                    'variants' => ['LXi', 'VXi', 'ZXi', 'ZXi-Plus'],
                    'products' => [
                        'brezza-vxi' => [
                            [
                                'name' => 'Front Radiator S Chrome Logo - Maruti Brezza VXi',
                                'sku' => 'MS-BRZ-VXI-FRT-LGO',
                                'category_id' => $catLogos->id,
                                'regular_price' => 799,
                                'sale_price' => 579,
                                'stock' => 35,
                                'image' => 'images/products/maruti-suzuki-logo.svg',
                                'short' => 'OEM specification front radiator grille chrome logo for Maruti Brezza VXi.',
                            ]
                        ],
                        'brezza-zxi' => [
                            [
                                'name' => 'BREZZA Rear Tailgate Chrome Lettering - Maruti Brezza ZXi',
                                'sku' => 'MS-BRZ-ZXI-RR-LET',
                                'category_id' => $catLetters->id,
                                'regular_price' => 899,
                                'sale_price' => 649,
                                'stock' => 28,
                                'image' => 'images/products/creta-lettering.svg',
                                'short' => 'Chrome wide-spaced Brezza rear tailgate letters for Brezza ZXi.',
                            ]
                        ]
                    ]
                ],
                'grand-vitara' => [
                    'variants' => ['Sigma', 'Delta', 'Zeta', 'Alpha'],
                    'products' => [
                        'grand-vitara-alpha' => [
                            [
                                'name' => 'Grand Vitara Smart Hybrid Rear Chrome Badge - Grand Vitara Alpha',
                                'sku' => 'MS-GVT-ALP-HYB-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 799,
                                'sale_price' => 549,
                                'stock' => 22,
                                'image' => 'images/products/swift-emblem.svg',
                                'short' => 'Dual tone chrome & blue Smart Hybrid tailgate badge for Grand Vitara Alpha.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 3. TATA
            // ==========================================
            'tata' => [
                'harrier' => [
                    'variants' => ['Smart', 'Pure', 'Adventure', 'Fearless'],
                    'products' => [
                        'harrier-adventure' => [
                            [
                                'name' => 'HARRIER 3D Chrome Bonnet Lettering - Tata Harrier Adventure',
                                'sku' => 'TAT-HAR-ADV-BNT-LET',
                                'category_id' => $catLetters->id,
                                'regular_price' => 1299,
                                'sale_price' => 999,
                                'stock' => 25,
                                'image' => 'images/products/creta-lettering.svg',
                                'short' => 'Bold 3D chrome bonnet lettering designed specifically for Tata Harrier Adventure.',
                            ],
                            [
                                'name' => 'Front Radiator Chrome Grille Emblem - Tata Harrier Adventure',
                                'sku' => 'TAT-HAR-ADV-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 30,
                                'image' => 'images/products/tata-logo.svg',
                                'short' => 'Original style front grille chrome emblem for Tata Harrier Adventure.',
                            ]
                        ],
                        'harrier-fearless' => [
                            [
                                'name' => 'Dark Edition Gloss Black Front & Rear Emblems - Tata Harrier Fearless',
                                'sku' => 'TAT-HAR-FRL-BLK-SET',
                                'category_id' => $catLogos->id,
                                'regular_price' => 1599,
                                'sale_price' => 1249,
                                'stock' => 18,
                                'image' => 'images/products/tata-logo.svg',
                                'short' => 'Dark Edition gloss piano black front and tailgate emblem pair for Harrier Fearless.',
                            ]
                        ]
                    ]
                ],
                'safari' => [
                    'variants' => ['Smart', 'Pure', 'Adventure', 'Accomplished'],
                    'products' => [
                        'safari-accomplished' => [
                            [
                                'name' => 'SAFARI Premium 3D Chrome Tailgate Monogram - Tata Safari Accomplished',
                                'sku' => 'TAT-SAF-ACC-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 1199,
                                'sale_price' => 899,
                                'stock' => 20,
                                'image' => 'images/products/creta-lettering.svg',
                                'short' => 'Precision laser cut 3D chrome Safari letters for Tata Safari Accomplished boot lid.',
                            ],
                            [
                                'name' => 'Front Parametric Grille Chrome Emblem - Tata Safari Accomplished',
                                'sku' => 'TAT-SAF-ACC-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 949,
                                'sale_price' => 749,
                                'stock' => 24,
                                'image' => 'images/products/tata-logo.svg',
                                'short' => 'Original specification front chrome logo for Tata Safari Accomplished.',
                            ]
                        ]
                    ]
                ],
                'altroz' => [
                    'variants' => ['XE', 'XM', 'XT', 'XZ', 'XZ-Plus'],
                    'products' => [
                        'altroz-xz' => [
                            [
                                'name' => 'ALTROZ Rear Boot Chrome Monogram Badge - Tata Altroz XZ',
                                'sku' => 'TAT-ALT-XZ-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 799,
                                'sale_price' => 599,
                                'stock' => 35,
                                'image' => 'images/products/tata-logo.svg',
                                'short' => 'High quality chrome tailgate monogram for Tata Altroz XZ.',
                            ],
                            [
                                'name' => 'Front Radiator Grille Chrome Emblem - Tata Altroz XZ',
                                'sku' => 'TAT-ALT-XZ-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 699,
                                'sale_price' => 499,
                                'stock' => 40,
                                'image' => 'images/products/tata-logo.svg',
                                'short' => 'Original style front grille chrome emblem for Tata Altroz XZ.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 4. HONDA
            // ==========================================
            'honda' => [
                'city' => [
                    'variants' => ['SV', 'V', 'VX', 'ZX'],
                    'products' => [
                        'city-v' => [
                            [
                                'name' => 'Front Radiator Grille H Chrome Logo - Honda City V',
                                'sku' => 'HND-CTY-V-FRT-LGO',
                                'category_id' => $catLogos->id,
                                'regular_price' => 799,
                                'sale_price' => 599,
                                'stock' => 40,
                                'image' => 'images/products/honda-logo-front.svg',
                                'short' => 'Triple plated mirror chrome front emblem for Honda City V.',
                            ]
                        ],
                        'city-zx' => [
                            [
                                'name' => 'CITY 3D Chrome Rear Boot Monogram - Honda City ZX',
                                'sku' => 'HND-CTY-ZX-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 749,
                                'sale_price' => 549,
                                'stock' => 35,
                                'image' => 'images/products/honda-logo-rear.svg',
                                'short' => 'Original OE-style City trunk chrome script badge for Honda City ZX.',
                            ],
                            [
                                'name' => 'i-VTEC Chrome Metal Trunk Badge - Honda City ZX',
                                'sku' => 'HND-CTY-ZX-VTC-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 599,
                                'sale_price' => 399,
                                'stock' => 50,
                                'image' => 'images/products/honda-logo-rear.svg',
                                'short' => 'Chrome & red enamel i-VTEC badge for Honda City ZX trunk.',
                            ]
                        ]
                    ]
                ],
                'amaze' => [
                    'variants' => ['E', 'S', 'VX'],
                    'products' => [
                        'amaze-s' => [
                            [
                                'name' => 'Front Grille H Chrome Emblem - Honda Amaze S',
                                'sku' => 'HND-AMZ-S-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 749,
                                'sale_price' => 529,
                                'stock' => 38,
                                'image' => 'images/products/honda-logo-front.svg',
                                'short' => 'Mirror chrome radiator grille H logo for Honda Amaze S.',
                            ]
                        ],
                        'amaze-vx' => [
                            [
                                'name' => 'AMAZE 3D Chrome Trunk Monogram - Honda Amaze VX',
                                'sku' => 'HND-AMZ-VX-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 699,
                                'sale_price' => 499,
                                'stock' => 32,
                                'image' => 'images/products/honda-logo-rear.svg',
                                'short' => 'Rear tailgate Amaze chrome badge for Honda Amaze VX.',
                            ]
                        ]
                    ]
                ],
                'elevate' => [
                    'variants' => ['SV', 'V', 'VX', 'ZX'],
                    'products' => [
                        'elevate-zx' => [
                            [
                                'name' => 'Front Solid Wing Face Chrome H Emblem - Honda Elevate ZX',
                                'sku' => 'HND-ELV-ZX-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 25,
                                'image' => 'images/products/honda-logo-front.svg',
                                'short' => 'Bold solid wing chrome front logo designed for Honda Elevate ZX.',
                            ],
                            [
                                'name' => 'ELEVATE Rear Boot Chrome Lettering - Honda Elevate ZX',
                                'sku' => 'HND-ELV-ZX-RR-LET',
                                'category_id' => $catLetters->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 30,
                                'image' => 'images/products/creta-lettering.svg',
                                'short' => 'Wide spaced 3D chrome tailgate lettering for Honda Elevate ZX.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 5. TOYOTA
            // ==========================================
            'toyota' => [
                'fortuner' => [
                    'variants' => ['4x2-MT', '4x2-AT', '4x4-MT', '4x4-AT', 'Legender', 'GR-S'],
                    'products' => [
                        'fortuner-4x4-at' => [
                            [
                                'name' => 'FORTUNER 3D Chrome Bonnet Lettering - Toyota Fortuner 4x4',
                                'sku' => 'TOY-FOR-4X4-BNT-LET',
                                'category_id' => $catLetters->id,
                                'regular_price' => 1499,
                                'sale_price' => 1199,
                                'stock' => 25,
                                'image' => 'images/products/fortuner-bonnet-letters.svg',
                                'short' => 'Heavy duty 3D chrome bonnet lettering set for Toyota Fortuner 4x4.',
                            ],
                            [
                                'name' => 'Front Radiator Grille Toyota Chrome Emblem - Toyota Fortuner 4x4',
                                'sku' => 'TOY-FOR-4X4-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 1099,
                                'sale_price' => 849,
                                'stock' => 30,
                                'image' => 'images/products/toyota-logo-front.svg',
                                'short' => 'Large front radiator grille chrome emblem for Toyota Fortuner 4x4.',
                            ]
                        ],
                        'fortuner-legender' => [
                            [
                                'name' => 'LEGENDER 3D Dark Chrome Tailgate Monogram - Toyota Fortuner Legender',
                                'sku' => 'TOY-FOR-LGD-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 1299,
                                'sale_price' => 999,
                                'stock' => 20,
                                'image' => 'images/products/fortuner-bonnet-letters.svg',
                                'short' => 'Exclusive dark chrome Legender boot badge for Fortuner Legender.',
                            ]
                        ],
                        'fortuner-gr-s' => [
                            [
                                'name' => 'GR-Sport Gazoo Racing Fender Metal Badges Set - Toyota Fortuner GR-S',
                                'sku' => 'TOY-FOR-GRS-FND-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 28,
                                'image' => 'images/products/toyota-logo-front.svg',
                                'short' => 'Authentic GR Sport zinc alloy fender badges with red and black enamel for Fortuner GR-S.',
                            ]
                        ]
                    ]
                ],
                'innova-crysta' => [
                    'variants' => ['GX', 'VX', 'ZX'],
                    'products' => [
                        'innova-crysta-vx' => [
                            [
                                'name' => 'Front Radiator Grille Chrome Emblem - Toyota Innova Crysta VX',
                                'sku' => 'TOY-INV-VX-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 35,
                                'image' => 'images/products/toyota-logo-front.svg',
                                'short' => 'Direct replacement front radiator grille logo for Innova Crysta VX.',
                            ]
                        ],
                        'innova-crysta-zx' => [
                            [
                                'name' => 'CRYSTA 3D Chrome Rear Trunk Monogram - Toyota Innova Crysta ZX',
                                'sku' => 'TOY-INV-ZX-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 899,
                                'sale_price' => 649,
                                'stock' => 30,
                                'image' => 'images/products/fortuner-bonnet-letters.svg',
                                'short' => 'Rear tailgate Crysta badge in triple plated chrome for Innova Crysta ZX.',
                            ]
                        ]
                    ]
                ],
                'glanza' => [
                    'variants' => ['E', 'S', 'G', 'V'],
                    'products' => [
                        'glanza-v' => [
                            [
                                'name' => 'Front Grille Toyota Chrome Emblem - Toyota Glanza V',
                                'sku' => 'TOY-GLZ-V-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 749,
                                'sale_price' => 549,
                                'stock' => 30,
                                'image' => 'images/products/toyota-logo-front.svg',
                                'short' => 'Original style front grille chrome emblem for Toyota Glanza V.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 6. MAHINDRA
            // ==========================================
            'mahindra' => [
                'scorpio' => [
                    'variants' => ['Classic', 'Z2', 'Z4', 'Z8', 'Z8L'],
                    'products' => [
                        'scorpio-classic' => [
                            [
                                'name' => 'Scorpio S11 Classic 3D Rear Door Chrome Emblem - Mahindra Scorpio Classic',
                                'sku' => 'MAH-SCP-CLS-RR-BDG',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 899,
                                'sale_price' => 649,
                                'stock' => 35,
                                'image' => 'images/products/mahindra-front-logo.svg',
                                'short' => 'Classic script 3D chrome tailgate badge for Mahindra Scorpio Classic.',
                            ]
                        ],
                        'scorpio-z8l' => [
                            [
                                'name' => 'Front Grille Twin Peaks Chrome Emblem - Mahindra Scorpio-N Z8L',
                                'sku' => 'MAH-SCP-Z8L-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 1199,
                                'sale_price' => 899,
                                'stock' => 25,
                                'image' => 'images/products/mahindra-twin-peaks.svg',
                                'short' => 'Original Twin Peaks chrome grille logo for Scorpio-N Z8L.',
                            ],
                            [
                                'name' => 'SCORPIO-N 3D Rear Tailgate Chrome Letters - Mahindra Scorpio-N Z8L',
                                'sku' => 'MAH-SCP-Z8L-RR-LET',
                                'category_id' => $catLetters->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 30,
                                'image' => 'images/products/creta-lettering.svg',
                                'short' => '3D metal-look chrome tailgate lettering for Scorpio-N Z8L.',
                            ]
                        ]
                    ]
                ],
                'xuv700' => [
                    'variants' => ['MX', 'AX3', 'AX5', 'AX7', 'AX7L'],
                    'products' => [
                        'xuv700-ax7l' => [
                            [
                                'name' => 'Twin Peaks Front Radiator Grille Emblem - Mahindra XUV700 AX7L',
                                'sku' => 'MAH-X70-AX7L-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 1299,
                                'sale_price' => 999,
                                'stock' => 20,
                                'image' => 'images/products/mahindra-twin-peaks.svg',
                                'short' => 'Large front Twin Peaks chrome emblem for Mahindra XUV700 AX7L.',
                            ],
                            [
                                'name' => 'AWD All-Wheel-Drive Tailgate Metal Badge - Mahindra XUV700 AX7L',
                                'sku' => 'MAH-X70-AX7L-AWD-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 749,
                                'sale_price' => 499,
                                'stock' => 40,
                                'image' => 'images/products/thar-4x4-badge.svg',
                                'short' => 'Authentic chrome AWD insignia for Mahindra XUV700 AX7L.',
                            ]
                        ]
                    ]
                ],
                'thar' => [
                    'variants' => ['AX', 'LX', 'Earth-Edition'],
                    'products' => [
                        'thar-lx' => [
                            [
                                'name' => 'THAR 4x4 Heavy Duty Metal Trail Rated Fender Badge - Mahindra Thar LX',
                                'sku' => 'MAH-THR-LX-TRL-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 35,
                                'image' => 'images/products/thar-4x4-badge.svg',
                                'short' => 'Heavy duty cast zinc alloy 4x4 fender badge with red inlay for Thar LX.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 7. KIA
            // ==========================================
            'kia' => [
                'seltos' => [
                    'variants' => ['HTE', 'HTK', 'HTX', 'GTX', 'X-Line'],
                    'products' => [
                        'seltos-htx' => [
                            [
                                'name' => 'Kia New Brushed Silver Silhouette Emblem (Front) - Kia Seltos HTX',
                                'sku' => 'KIA-SLT-HTX-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 949,
                                'sale_price' => 729,
                                'stock' => 30,
                                'image' => 'images/products/kia-new-silhouette.svg',
                                'short' => 'New modern Kia signature connected brushed chrome front emblem for Seltos HTX.',
                            ],
                            [
                                'name' => 'SELTOS 3D Tailgate Chrome Monogram - Kia Seltos HTX',
                                'sku' => 'KIA-SLT-HTX-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 799,
                                'sale_price' => 599,
                                'stock' => 35,
                                'image' => 'images/products/kia-new-silhouette.svg',
                                'short' => 'Rear boot Seltos individual chrome letters badge for Kia Seltos HTX.',
                            ]
                        ],
                        'seltos-gtx' => [
                            [
                                'name' => 'GT-Line Red Chrome Front Grille Badge - Kia Seltos GTX',
                                'sku' => 'KIA-SLT-GTX-GTL-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 849,
                                'sale_price' => 649,
                                'stock' => 25,
                                'image' => 'images/products/kia-new-silhouette.svg',
                                'short' => 'Original GT Line chrome & red enamel grille emblem for Kia Seltos GTX.',
                            ]
                        ]
                    ]
                ],
                'sonet' => [
                    'variants' => ['HTE', 'HTK', 'HTX', 'GTX-Plus'],
                    'products' => [
                        'sonet-htx' => [
                            [
                                'name' => 'Kia New Silhouette Chrome Rear Tailgate Emblem - Kia Sonet HTX',
                                'sku' => 'KIA-SNT-HTX-RR-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 849,
                                'sale_price' => 649,
                                'stock' => 30,
                                'image' => 'images/products/kia-new-silhouette.svg',
                                'short' => 'Authentic new design Kia rear trunk emblem for Kia Sonet HTX.',
                            ]
                        ],
                        'sonet-gtx-plus' => [
                            [
                                'name' => 'Turbo GDI Tailgate Chrome Badge - Kia Sonet GTX+',
                                'sku' => 'KIA-SNT-GTXP-TRB-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 699,
                                'sale_price' => 499,
                                'stock' => 35,
                                'image' => 'images/products/kia-new-silhouette.svg',
                                'short' => 'Turbo GDI chrome & red enamel metal badge for Sonet GTX+.',
                            ]
                        ]
                    ]
                ],
                'carens' => [
                    'variants' => ['Premium', 'Prestige', 'Luxury', 'Luxury-Plus'],
                    'products' => [
                        'carens-luxury-plus' => [
                            [
                                'name' => 'CARENS Rear Boot Chrome Lettering Monogram - Kia Carens Luxury+',
                                'sku' => 'KIA-CRN-LXP-RR-LET',
                                'category_id' => $catLetters->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 25,
                                'image' => 'images/products/creta-lettering.svg',
                                'short' => 'Original 3D chrome Carens tailgate letters for Kia Carens Luxury Plus.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 8. FORD
            // ==========================================
            'ford' => [
                'endeavour' => [
                    'variants' => ['Titanium', 'Titanium-Plus', 'Sport'],
                    'products' => [
                        'endeavour-titanium-plus' => [
                            [
                                'name' => 'ENDEAVOUR 3D Chrome Bonnet Lettering - Ford Endeavour Titanium+',
                                'sku' => 'FRD-END-TNT-BNT-LET',
                                'category_id' => $catLetters->id,
                                'regular_price' => 1399,
                                'sale_price' => 1099,
                                'stock' => 22,
                                'image' => 'images/products/fortuner-bonnet-letters.svg',
                                'short' => 'Bold 3D chrome bonnet lettering for Ford Endeavour Titanium+.',
                            ],
                            [
                                'name' => 'Front Radiator Blue Oval Chrome Grille Emblem - Ford Endeavour Titanium+',
                                'sku' => 'FRD-END-TNT-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 25,
                                'image' => 'images/products/universal-steering-badge.svg',
                                'short' => 'Classic blue oval front radiator chrome logo for Ford Endeavour Titanium+.',
                            ]
                        ],
                        'endeavour-sport' => [
                            [
                                'name' => 'Sport Edition Gloss Black Tailgate Badge - Ford Endeavour Sport',
                                'sku' => 'FRD-END-SPT-RR-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 20,
                                'image' => 'images/products/universal-steering-badge.svg',
                                'short' => 'Gloss black Sport badge for Ford Endeavour Sport edition tailgate.',
                            ]
                        ]
                    ]
                ],
                'ecosport' => [
                    'variants' => ['Ambiente', 'Trend', 'Titanium', 'Titanium-Plus', 'S'],
                    'products' => [
                        'ecosport-titanium' => [
                            [
                                'name' => 'Front Radiator Grille Blue Oval Logo - Ford EcoSport Titanium',
                                'sku' => 'FRD-ECO-TTN-FRT-LGO',
                                'category_id' => $catLogos->id,
                                'regular_price' => 799,
                                'sale_price' => 599,
                                'stock' => 30,
                                'image' => 'images/products/universal-steering-badge.svg',
                                'short' => 'Original style blue oval radiator emblem for Ford EcoSport Titanium.',
                            ]
                        ],
                        'ecosport-s' => [
                            [
                                'name' => 'ECOSPORT Rear Tailgate Chrome Monogram - Ford EcoSport S',
                                'sku' => 'FRD-ECO-S-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 699,
                                'sale_price' => 499,
                                'stock' => 35,
                                'image' => 'images/products/swift-emblem.svg',
                                'short' => 'Rear tailgate EcoSport chrome insignia badge for Ford EcoSport S.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 9. VOLKSWAGEN
            // ==========================================
            'volkswagen' => [
                'virtus' => [
                    'variants' => ['Comfortline', 'Highline', 'Topline', 'GT-Plus'],
                    'products' => [
                        'virtus-topline' => [
                            [
                                'name' => 'Volkswagen Chrome New Minimal Flat Grille Emblem - VW Virtus Topline',
                                'sku' => 'VW-VRT-TPL-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 1199,
                                'sale_price' => 899,
                                'stock' => 20,
                                'image' => 'images/products/universal-steering-badge.svg',
                                'short' => 'New 2D flat minimalist chrome front grille VW emblem for Virtus Topline.',
                            ],
                            [
                                'name' => 'VIRTUS Tailgate 3D Chrome Lettering - VW Virtus Topline',
                                'sku' => 'VW-VRT-TPL-RR-LET',
                                'category_id' => $catLetters->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 28,
                                'image' => 'images/products/creta-lettering.svg',
                                'short' => 'Original centered trunk chrome Virtus lettering for VW Virtus Topline.',
                            ]
                        ],
                        'virtus-gt-plus' => [
                            [
                                'name' => 'GT Red & Chrome Fender Metal Badges Pair - VW Virtus GT Plus',
                                'sku' => 'VW-VRT-GTP-FND-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 25,
                                'image' => 'images/products/swift-emblem.svg',
                                'short' => 'Genuine GT emblem pair in red & chrome for front wings of VW Virtus GT Plus.',
                            ]
                        ]
                    ]
                ],
                'taigun' => [
                    'variants' => ['Comfortline', 'Highline', 'Topline', 'GT-Plus'],
                    'products' => [
                        'taigun-topline' => [
                            [
                                'name' => 'VW New Flat Chrome Tailgate Emblem - VW Taigun Topline',
                                'sku' => 'VW-TGN-TPL-RR-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 22,
                                'image' => 'images/products/universal-steering-badge.svg',
                                'short' => 'Rear tailgate chrome VW badge for Volkswagen Taigun Topline.',
                            ]
                        ],
                        'taigun-gt-plus' => [
                            [
                                'name' => 'GT Front Grille Inset Chrome & Red Badge - VW Taigun GT Plus',
                                'sku' => 'VW-TGN-GTP-GRL-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 899,
                                'sale_price' => 699,
                                'stock' => 25,
                                'image' => 'images/products/swift-emblem.svg',
                                'short' => 'Sporty red and chrome GT grille badge for Volkswagen Taigun GT Plus.',
                            ]
                        ]
                    ]
                ],
                'polo' => [
                    'variants' => ['Trendline', 'Comfortline', 'Highline', 'GT-TSI'],
                    'products' => [
                        'polo-gt-tsi' => [
                            [
                                'name' => 'Polo GT TSI Chrome & Red Tailgate Monogram - VW Polo GT TSI',
                                'sku' => 'VW-POL-GTT-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 799,
                                'sale_price' => 599,
                                'stock' => 35,
                                'image' => 'images/products/swift-emblem.svg',
                                'short' => 'Classic Polo GT TSI boot lid badge for Volkswagen Polo GT TSI.',
                            ],
                            [
                                'name' => 'Front Radiator Grille VW Chrome Emblem - VW Polo GT TSI',
                                'sku' => 'VW-POL-GTT-FRT-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 849,
                                'sale_price' => 649,
                                'stock' => 30,
                                'image' => 'images/products/universal-steering-badge.svg',
                                'short' => 'Radiator grille VW roundel chrome logo for VW Polo GT TSI.',
                            ]
                        ]
                    ]
                ]
            ],

            // ==========================================
            // 10. BMW
            // ==========================================
            'bmw' => [
                '3-series' => [
                    'variants' => ['320d', '330i', 'M340i'],
                    'products' => [
                        '3-series-330i' => [
                            [
                                'name' => 'BMW 82mm Hood Bonnet Roundel Emblem (Blue & White) - BMW 3 Series 330i',
                                'sku' => 'BMW-3SR-330-HOD-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 1499,
                                'sale_price' => 1199,
                                'stock' => 20,
                                'image' => 'images/products/bmw-roundel-badge.svg',
                                'short' => 'High quality UV resistant OE-spec 82mm hood bonnet roundel for BMW 3 Series 330i.',
                            ],
                            [
                                'name' => '330i Chrome Tailgate Monogram Letters - BMW 3 Series 330i',
                                'sku' => 'BMW-3SR-330-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 25,
                                'image' => 'images/products/bmw-roundel-badge.svg',
                                'short' => 'Gloss chrome 330i rear trunk letters for BMW 3 Series 330i.',
                            ]
                        ],
                        '3-series-m340i' => [
                            [
                                'name' => 'M340i Gloss Black Metal Rear Trunk Badge - BMW 3 Series M340i',
                                'sku' => 'BMW-3SR-M34-BLK-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 1699,
                                'sale_price' => 1299,
                                'stock' => 15,
                                'image' => 'images/products/bmw-roundel-badge.svg',
                                'short' => 'OEM specification gloss shadowline black M340i trunk badge for BMW M340i.',
                            ],
                            [
                                'name' => 'M Performance Tri-Color Fender Metal Badges Pair - BMW 3 Series M340i',
                                'sku' => 'BMW-3SR-M34-FND-BDG',
                                'category_id' => $catBadges->id,
                                'regular_price' => 1199,
                                'sale_price' => 899,
                                'stock' => 30,
                                'image' => 'images/products/bmw-roundel-badge.svg',
                                'short' => 'Classic tri-color M fender badges in chrome & gloss black for BMW M340i.',
                            ]
                        ]
                    ]
                ],
                '5-series' => [
                    'variants' => ['520d', '530i', 'M550i'],
                    'products' => [
                        '5-series-520d' => [
                            [
                                'name' => 'BMW 74mm Trunk Boot Roundel Emblem - BMW 5 Series 520d',
                                'sku' => 'BMW-5SR-520-TRK-EMB',
                                'category_id' => $catLogos->id,
                                'regular_price' => 1399,
                                'sale_price' => 1099,
                                'stock' => 20,
                                'image' => 'images/products/bmw-roundel-badge.svg',
                                'short' => 'Rear trunk 74mm replacement roundel emblem for BMW 5 Series 520d.',
                            ]
                        ],
                        '5-series-530i' => [
                            [
                                'name' => '530i Chrome Rear Trunk Monogram - BMW 5 Series 530i',
                                'sku' => 'BMW-5SR-530-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 1099,
                                'sale_price' => 799,
                                'stock' => 25,
                                'image' => 'images/products/bmw-roundel-badge.svg',
                                'short' => 'Original style chrome 530i trunk lettering for BMW 5 Series 530i.',
                            ]
                        ]
                    ]
                ],
                'x1' => [
                    'variants' => ['sDrive18i', 'sDrive18d', 'xLine', 'M-Sport'],
                    'products' => [
                        'x1-xline' => [
                            [
                                'name' => 'X1 Rear Boot Chrome Monogram Badge - BMW X1 xLine',
                                'sku' => 'BMW-X1-XLN-RR-BDG',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 999,
                                'sale_price' => 749,
                                'stock' => 25,
                                'image' => 'images/products/bmw-roundel-badge.svg',
                                'short' => 'OE-style X1 rear boot badge with 3M adhesive for BMW X1 xLine.',
                            ]
                        ],
                        'x1-m-sport' => [
                            [
                                'name' => 'Wheel Center Hub Caps Set of 4 (68mm) - BMW X1 M Sport',
                                'sku' => 'BMW-X1-MSP-WHL-CAP',
                                'category_id' => $catAccessories->id,
                                'regular_price' => 1299,
                                'sale_price' => 999,
                                'stock' => 22,
                                'image' => 'images/products/wheel-center-caps.svg',
                                'short' => 'Complete set of 4 BMW 68mm wheel center caps for X1 M Sport alloy wheels.',
                            ]
                        ]
                    ]
                ],
                'x5' => [
                    'variants' => ['xDrive40i', 'xDrive30d', 'M50d'],
                    'products' => [
                        'x5-xdrive40i' => [
                            [
                                'name' => 'X5 xDrive Rear Boot Metal Monogram Badge - BMW X5 xDrive40i',
                                'sku' => 'BMW-X5-X40-RR-MON',
                                'category_id' => $catMonograms->id,
                                'regular_price' => 1299,
                                'sale_price' => 999,
                                'stock' => 18,
                                'image' => 'images/products/bmw-roundel-badge.svg',
                                'short' => 'X5 & xDrive chrome trunk badge pair for BMW X5 xDrive40i.',
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $totalProductsCreated = 0;
        $totalVariantsCreated = 0;

        foreach ($brands as $brandSlug => $modelsData) {
            $brand = Brand::where('slug', $brandSlug)->first();
            if (!$brand) {
                $this->command->warn("Brand with slug '{$brandSlug}' not found. Skipping.");
                continue;
            }

            foreach ($modelsData as $modelSlug => $modelConfig) {
                $carModel = CarModel::where('brand_id', $brand->id)->where('slug', $modelSlug)->first();
                if (!$carModel) {
                    $carModel = CarModel::create([
                        'brand_id' => $brand->id,
                        'name' => ucwords(str_replace('-', ' ', $modelSlug)),
                        'slug' => $modelSlug,
                        'is_popular' => true,
                        'is_active' => true,
                        'sort_order' => 1,
                    ]);
                }

                // 1. Create / Ensure Variants
                $createdVariants = [];
                $vOrder = 1;
                foreach ($modelConfig['variants'] as $variantName) {
                    $vSlug = Str::slug("{$carModel->slug}-{$variantName}");
                    $variant = ModelVariant::updateOrCreate(
                        ['car_model_id' => $carModel->id, 'slug' => $vSlug],
                        [
                            'name' => $variantName,
                            'status' => true,
                            'sort_order' => $vOrder++,
                        ]
                    );
                    $createdVariants[$vSlug] = $variant;
                    $totalVariantsCreated++;
                }

                // 2. Create Mapped Products
                if (!empty($modelConfig['products'])) {
                    foreach ($modelConfig['products'] as $vSlugKey => $productsList) {
                        $variant = $createdVariants[$vSlugKey] ?? ModelVariant::where('car_model_id', $carModel->id)->where('slug', $vSlugKey)->first();
                        if (!$variant) {
                            continue;
                        }

                        foreach ($productsList as $pData) {
                            $productSlug = Str::slug($pData['name']);
                            $product = Product::updateOrCreate(
                                ['slug' => $productSlug],
                                [
                                    'name' => $pData['name'],
                                    'sku' => $pData['sku'],
                                    'brand_id' => $brand->id,
                                    'car_model_id' => $carModel->id,
                                    'model_variant_id' => $variant->id,
                                    'primary_category_id' => $pData['category_id'],
                                    'regular_price' => $pData['regular_price'],
                                    'sale_price' => $pData['sale_price'],
                                    'stock_quantity' => $pData['stock'],
                                    'is_featured' => true,
                                    'status' => true,
                                    'short_description' => $pData['short'],
                                    'full_description' => '<p>' . $pData['short'] . ' Manufactured to high standards with anti-corrosion chrome plating and weather-resistant backing.</p>',
                                    'specifications' => [
                                        'Company' => $brand->name,
                                        'Car Model' => $carModel->name,
                                        'Variant Fitment' => $variant->name,
                                        'Finish' => 'Triple Plated Chrome / Matte Finish',
                                        'Warranty' => '1 Year Anti-Rust Warranty',
                                    ]
                                ]
                            );

                            if (!empty($pData['image']) && $product->images()->count() === 0) {
                                $product->images()->create([
                                    'image_path' => $pData['image'],
                                    'is_primary' => true,
                                    'sort_order' => 0,
                                ]);
                            }

                            $totalProductsCreated++;
                        }
                    }
                }
            }
        }

        $this->command->info("AllCompaniesProductsSeeder finished successfully: Created/Updated {$totalVariantsCreated} variants and {$totalProductsCreated} products across all 10 companies!");
    }
}
