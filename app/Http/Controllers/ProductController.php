<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\ModelVariant;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['images', 'brand', 'carModel', 'primaryCategory']);

        // Smart Search Filter
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $selectedCategory = null;

        // Category Filter (supports real category models, subcategories, and smart navigation alias categories)
        if ($request->filled('category')) {
            $categorySlug = trim($request->category);
            $category = Category::where('slug', $categorySlug)->first();

            if ($category) {
                $catIds = $category->children->pluck('id')->push($category->id)->toArray();
                $query->where(function ($cq) use ($catIds, $categorySlug) {
                    $cq->whereIn('primary_category_id', $catIds)
                       ->orWhereHas('categories', function ($sq) use ($catIds) {
                           $sq->whereIn('categories.id', $catIds);
                       });

                    // Contextual fallback for standard/legacy product types and placements
                    if ($categorySlug === 'front-logo') {
                        $cq->orWhere('product_type', 'front_logo')
                           ->orWhere(function ($sq) {
                               $sq->where('product_type', 'logo')
                                  ->where(function ($pq) {
                                      $pq->where('placement', 'like', '%Front%')
                                         ->orWhere('placement', 'like', '%Grille%');
                                  });
                           });
                    } elseif ($categorySlug === 'rear-logo') {
                        $cq->orWhere('product_type', 'rear_logo')
                           ->orWhere(function ($sq) {
                               $sq->where('product_type', 'logo')
                                  ->where(function ($pq) {
                                      $pq->where('placement', 'like', '%Rear%')
                                         ->orWhere('placement', 'like', '%Trunk%');
                                  });
                           });
                    } elseif ($categorySlug === 'bonnet-tailgate-letters') {
                        $cq->orWhereIn('product_type', ['bonnet_letters', 'monogram', 'letters', 'lettering'])
                           ->orWhere('placement', 'like', '%Bonnet%');
                    } elseif ($categorySlug === 'kit-full-monogram') {
                        $cq->orWhereIn('product_type', ['full_monogram_kit', 'combo'])
                           ->orWhere('placement', 'like', '%Exterior Kit%');
                    } elseif ($categorySlug === 'single-name-badge') {
                        $cq->orWhereIn('product_type', ['single_badge', 'badge', 'emblem']);
                    } elseif ($categorySlug === 'new-arrivals') {
                        $cq->orWhere('is_new_arrival', 1)
                           ->orWhere('product_type', 'new_arrival');
                    }
                });
                $selectedCategory = $category;
            } else {
                // Smart Aliases / Dynamic Virtual Categories from Navigation and Themes
                switch ($categorySlug) {
                    case 'front-logo':
                        $query->where(function ($q) {
                            $q->where('placement', 'like', '%Front%')
                              ->orWhere('placement', 'like', '%Front Grille%')
                              ->orWhere('name', 'like', '%Front%');
                        });
                        $selectedCategory = (object) [
                            'id' => null,
                            'name' => 'Front Logo',
                            'slug' => 'front-logo',
                        ];
                        break;

                    case 'rear-logo':
                        $query->where(function ($q) {
                            $q->where('placement', 'like', '%Rear%')
                              ->orWhere('placement', 'like', '%Rear Trunk%')
                              ->orWhere('name', 'like', '%Rear%');
                        });
                        $selectedCategory = (object) [
                            'id' => null,
                            'name' => 'Rear Logo',
                            'slug' => 'rear-logo',
                        ];
                        break;

                    case 'bonnet-tailgate-letters':
                        $query->where(function ($q) {
                            $q->where('placement', 'like', '%Bonnet%')
                              ->orWhere('name', 'like', '%bonnet%')
                              ->orWhere('name', 'like', '%letters%')
                              ->orWhere('primary_category_id', 12)
                              ->orWhereIn('product_type', ['letters', 'lettering'])
                              ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'letters-numbers'));
                        });
                        $selectedCategory = (object) [
                            'id' => null,
                            'name' => 'Bonnet & Letters',
                            'slug' => 'bonnet-tailgate-letters',
                        ];
                        break;

                    case 'kit-full-monogram':
                        $query->where(function ($q) {
                            $q->where('is_monogram', 1)
                              ->orWhere('placement', 'like', '%Exterior Kit%')
                              ->orWhereIn('product_type', ['monogram', 'combo'])
                              ->orWhere('primary_category_id', 11)
                              ->orWhere('name', 'like', '%monogram%')
                              ->orWhere('name', 'like', '%combo%')
                              ->orWhere('name', 'like', '%kit%')
                              ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'car-monograms'));
                        });
                        $selectedCategory = (object) [
                            'id' => null,
                            'name' => 'Full Monogram Kits',
                            'slug' => 'kit-full-monogram',
                        ];
                        break;

                    case 'single-name-badge':
                        $query->where(function ($q) {
                            $q->where('product_type', 'badge')
                              ->orWhere('name', 'like', '%badge%')
                              ->orWhere('primary_category_id', 10)
                              ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'model-logos-badges'));
                        });
                        $selectedCategory = (object) [
                            'id' => null,
                            'name' => 'Single Badges',
                            'slug' => 'single-name-badge',
                        ];
                        break;

                    case 'new-arrivals':
                        $query->where('is_new_arrival', 1);
                        $selectedCategory = (object) [
                            'id' => null,
                            'name' => 'New Arrivals',
                            'slug' => 'new-arrivals',
                        ];
                        break;

                    default:
                        $selectedCategory = (object) [
                            'id' => null,
                            'name' => ucwords(str_replace('-', ' ', $categorySlug)),
                            'slug' => $categorySlug,
                        ];
                        break;
                }
            }
        }

        // Direct New Arrivals Filter (via ?new_arrivals=1)
        if ($request->boolean('new_arrivals') || $request->new_arrivals == '1') {
            $query->where('is_new_arrival', 1);
            if (!$selectedCategory) {
                $selectedCategory = (object) [
                    'id' => null,
                    'name' => 'New Arrivals',
                    'slug' => 'new-arrivals',
                ];
            }
        }

        // Brand Filter
        if ($request->filled('brand')) {
            $brand = Brand::where('slug', $request->brand)->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        // Car Model Filter
        if ($request->filled('model')) {
            $model = CarModel::where('slug', $request->model)->first();
            if ($model) {
                $query->where('car_model_id', $model->id);
            }
        }

        // Car Variant Filter
        if ($request->filled('variant')) {
            $variant = ModelVariant::where('slug', $request->variant)->first();
            if ($variant) {
                $query->where('model_variant_id', $variant->id);
            }
        } elseif ($request->filled('variant_id')) {
            $query->where('model_variant_id', $request->variant_id);
        }

        // Product Type Filter
        if ($request->filled('type')) {
            $type = $request->type;
            if ($type === 'front_logo' || $type === 'front-logo') {
                $query->where(function ($q) {
                    $q->where('product_type', 'front_logo')
                      ->orWhere('placement', 'like', '%Front%')
                      ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'front-logo'));
                });
            } elseif ($type === 'rear_logo' || $type === 'rear-logo') {
                $query->where(function ($q) {
                    $q->where('product_type', 'rear_logo')
                      ->orWhere('placement', 'like', '%Rear%')
                      ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'rear-logo'));
                });
            } elseif ($type === 'bonnet_letters' || $type === 'bonnet-letters') {
                $query->where(function ($q) {
                    $q->where('product_type', 'bonnet_letters')
                      ->orWhere('placement', 'like', '%Bonnet%')
                      ->orWhereIn('product_type', ['letters', 'lettering'])
                      ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'bonnet-tailgate-letters'));
                });
            } elseif ($type === 'full_monogram_kit' || $type === 'kit-full-monogram') {
                $query->where(function ($q) {
                    $q->where('product_type', 'full_monogram_kit')
                      ->orWhere('is_monogram', 1)
                      ->orWhere('placement', 'like', '%Exterior Kit%')
                      ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'kit-full-monogram'));
                });
            } elseif ($type === 'single_badge' || $type === 'single-badge') {
                $query->where(function ($q) {
                    $q->whereIn('product_type', ['single_badge', 'badge'])
                      ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'single-name-badge'));
                });
            } elseif ($type === 'new_arrival' || $type === 'new-arrival') {
                $query->where(function ($q) {
                    $q->where('product_type', 'new_arrival')
                      ->orWhere('is_new_arrival', 1)
                      ->orWhereHas('categories', fn($cq) => $cq->where('slug', 'new-arrivals'));
                });
            } else {
                $query->where('product_type', $type);
            }
        }

        // Smart Placement Filter (Front / Rear / Side / Bonnet / Kit)
        if ($request->filled('placement')) {
            $placement = trim($request->placement);
            $query->where(function ($pq) use ($placement) {
                $pq->where('placement', $placement)
                   ->orWhere('placement', 'like', "%{$placement}%");
                $lower = strtolower($placement);
                if ($lower === 'front') {
                    $pq->orWhere('placement', 'like', '%Front Grille%');
                } elseif ($lower === 'rear') {
                    $pq->orWhere('placement', 'like', '%Rear Trunk%');
                } elseif ($lower === 'side') {
                    $pq->orWhere('placement', 'like', '%Fender%');
                } elseif ($lower === 'bonnet') {
                    $pq->orWhere('placement', 'like', '%Boot%');
                } elseif ($lower === 'kit') {
                    $pq->orWhere('placement', 'like', '%Exterior Kit%');
                }
            });
        }

        // Finish Filter (Chrome / Black / Silver / Carbon / Red)
        if ($request->filled('finish')) {
            $finish = $request->finish;
            $query->where(function ($q) use ($finish) {
                $q->where('finish', $finish)
                  ->orWhere('finish', 'LIKE', "%{$finish}%");
            });
        }

        // Price Filter
        if ($request->filled('min_price')) {
            $query->where('regular_price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('regular_price', '<=', (float) $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', $request->filled('q') ? 'relevance' : 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('regular_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('regular_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('is_best_seller', 'desc')->orderBy('is_featured', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'relevance':
                $query->orderByRelevance($request->q);
                break;
            default:
                if ($request->filled('q')) {
                    $query->orderByRelevance($request->q);
                } else {
                    $query->latest('products.id');
                }
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $brands = Brand::active()->withCount('products')->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::active()->whereNull('parent_id')->with(['children' => fn($q) => $q->withCount('products')])->withCount('products')->orderBy('sort_order')->get();

        $selectedBrand = $request->filled('brand') ? Brand::where('slug', $request->brand)->first() : null;
        $selectedModel = $request->filled('model') ? CarModel::where('slug', $request->model)->first() : null;
        $selectedVariant = $request->filled('variant') ? ModelVariant::where('slug', $request->variant)->first() : null;
        $selectedCategory = $selectedCategory ?: ($request->filled('category') ? Category::where('slug', $request->category)->first() : null);

        // Auto-resolve brand & model if deeper level is requested directly
        if ($selectedVariant && !$selectedModel) {
            $selectedModel = $selectedVariant->carModel;
        }
        if ($selectedModel && !$selectedBrand) {
            $selectedBrand = $selectedModel->brand;
        }

        $models = $selectedBrand 
            ? CarModel::active()->where('brand_id', $selectedBrand->id)->withCount('products')->orderBy('name')->get()
            : collect();

        $variants = $selectedModel
            ? ModelVariant::active()->where('car_model_id', $selectedModel->id)->withCount('products')->orderBy('name')->get()
            : ($selectedBrand 
                ? ModelVariant::active()->whereHas('carModel', fn($q) => $q->where('brand_id', $selectedBrand->id))->withCount('products')->orderBy('name')->get()
                : collect());

        $minCatalogPrice = (int) (Product::active()->min('regular_price') ?: 0);
        $maxCatalogPrice = (int) (Product::active()->max('regular_price') ?: 5000);

        return view('frontend.shop', compact(
            'products', 
            'brands', 
            'categories', 
            'models', 
            'variants', 
            'selectedBrand', 
            'selectedModel', 
            'selectedVariant', 
            'selectedCategory', 
            'minCatalogPrice', 
            'maxCatalogPrice'
        ));
    }

    public function detail(string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['images', 'brand', 'carModel', 'modelVariant.carModel.brand', 'primaryCategory', 'variants', 'reviews.user'])
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                if ($product->brand_id) {
                    $q->where('brand_id', $product->brand_id);
                }
                if ($product->primary_category_id) {
                    $q->orWhere('primary_category_id', $product->primary_category_id);
                }
            })
            ->with('images')
            ->take(4)
            ->get();

        return view('frontend.product_detail', compact('product', 'relatedProducts'));
    }

    public function checkPincode(Request $request)
    {
        $pincode = trim($request->get('pincode', ''));
        if (strlen($pincode) !== 6 || !is_numeric($pincode)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid 6-digit Indian PIN code.'
            ]);
        }

        // Standard delivery check estimation
        $deliveryDays = rand(3, 5);
        $expectedDate = now()->addDays($deliveryDays)->format('d M, Y');

        return response()->json([
            'success' => true,
            'message' => "Delivery available! Expected by {$expectedDate} (Express 3-5 days). Cash on Delivery available.",
            'expected_date' => $expectedDate,
            'cod_available' => true,
        ]);
    }
}
