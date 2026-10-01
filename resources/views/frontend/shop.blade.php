@extends('layouts.app')

@section('title', 'Shop Car Logos, Monograms & Emblems - AutoLogos')

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Shop Catalog</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    @php
        $activeFilterCount = 0;
        if(request()->filled('brand')) $activeFilterCount++;
        if(request()->filled('model')) $activeFilterCount++;
        if(request()->filled('variant')) $activeFilterCount++;
        if(request()->filled('category')) $activeFilterCount++;
        if(request()->filled('placement')) $activeFilterCount++;
        if(request()->filled('finish')) $activeFilterCount++;
        if(request()->filled('min_price') || request()->filled('max_price')) $activeFilterCount++;
        if(request()->filled('q')) $activeFilterCount++;
    @endphp

    <!-- Mobile Filter & Sort Bar (Visible on mobile screens) -->
    <div class="d-lg-none mb-3">
        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-dark flex-grow-1 py-2 font-sm fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterDrawer">
                <i class="fa-solid fa-sliders text-danger"></i>
                <span>FILTERS</span>
                @if($activeFilterCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $activeFilterCount }}</span>
                @endif
            </button>
            <div class="flex-grow-1">
                <select class="form-select form-select-sm py-2 font-sm" onchange="location = this.value;">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Desktop Filter Sidebar -->
        <div class="col-lg-3 d-none d-lg-block">
            <aside class="shop-filter-sidebar">
                @include('frontend.partials.shop_filters')
            </aside>
        </div>

        <!-- Products Grid & Controls Area -->
        <div class="col-lg-9">
            <!-- Top Controls Bar -->
            <div class="card border-0 bg-white shadow-sm rounded-3 p-3 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <span class="text-muted font-sm">
                            Showing <strong class="text-dark">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</strong> of <strong class="text-dark">{{ $products->total() }}</strong> products
                        </span>
                        @if(request('q'))
                            <span class="badge bg-secondary ms-2">Search: "{{ request('q') }}"</span>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end d-none d-lg-block">
                        <div class="d-inline-flex align-items-center gap-2">
                            <label class="text-muted font-sm mb-0 text-nowrap"><i class="fa-solid fa-arrow-down-short-wide text-danger me-1"></i> Sort By:</label>
                            <select class="form-select form-select-sm" style="width: 190px;" onchange="location = this.value;">
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filter Chips / Removable Pills -->
            @if($activeFilterCount > 0)
                <div class="card border-0 bg-light-subtle rounded-3 p-2 mb-4 border">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="font-sm text-uppercase fw-bold text-muted me-1" style="font-size: 11px;">
                            <i class="fa-solid fa-filter text-danger me-1"></i> Active Filters:
                        </span>

                        @if(request()->filled('brand'))
                            <a href="{{ request()->fullUrlWithQuery(['brand' => null, 'model' => null, 'variant' => null]) }}" class="active-filter-chip">
                                <span>Brand: <strong>{{ $selectedBrand->name ?? request('brand') }}</strong></span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                        @if(request()->filled('model'))
                            <a href="{{ request()->fullUrlWithQuery(['model' => null, 'variant' => null]) }}" class="active-filter-chip">
                                <span>Model: <strong>{{ $selectedModel->name ?? request('model') }}</strong></span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                        @if(request()->filled('variant'))
                            <a href="{{ request()->fullUrlWithQuery(['variant' => null]) }}" class="active-filter-chip">
                                <span>Variant: <strong>{{ $selectedVariant->name ?? request('variant') }}</strong></span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                        @if(request()->filled('category'))
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="active-filter-chip">
                                <span>Category: <strong>{{ $selectedCategory->name ?? request('category') }}</strong></span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                        @if(request()->filled('placement'))
                            <a href="{{ request()->fullUrlWithQuery(['placement' => null]) }}" class="active-filter-chip">
                                <span>Placement: <strong>{{ request('placement') }}</strong></span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                        @if(request()->filled('finish'))
                            <a href="{{ request()->fullUrlWithQuery(['finish' => null]) }}" class="active-filter-chip">
                                <span>Finish: <strong>{{ request('finish') }}</strong></span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                        @if(request()->filled('min_price') || request()->filled('max_price'))
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="active-filter-chip">
                                <span>Price: <strong>&#x20B9;{{ request('min_price', 0) }} - &#x20B9;{{ request('max_price', $maxCatalogPrice) }}</strong></span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                        @if(request()->filled('q'))
                            <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="active-filter-chip">
                                <span>Search: <strong>"{{ request('q') }}"</strong></span>
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif

                        <a href="{{ route('shop') }}" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill font-sm ms-auto fw-semibold" style="font-size: 11px;">
                            <i class="fa-solid fa-rotate-left me-1"></i> Clear All
                        </a>
                    </div>
                </div>
            @endif

            <!-- Products List -->
            @if($products->isEmpty())
                <div class="card border-0 bg-white shadow-sm p-5 text-center my-4 rounded-3">
                    <i class="fa-solid fa-box-open text-muted display-3 mb-3"></i>
                    <h4 class="fw-bold">No Products Found</h4>
                    <p class="text-muted">We couldn't find any products matching your selected vehicle or filters.</p>
                    <div class="mt-3">
                        <a href="{{ route('shop') }}" class="btn btn-theme-red px-4 py-2">
                            <i class="fa-solid fa-rotate-left me-2"></i> Reset All Filters
                        </a>
                    </div>
                </div>
            @else
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mb-4">
                    @foreach($products as $prod)
                        <div class="col">
                            <div class="product-card">
                                <div class="img-wrapper">
                                    <a href="{{ route('product.detail', $prod->slug) }}">
                                        <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/placeholder-emblem.svg') }}';">
                                    </a>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('product.detail', $prod->slug) }}" class="product-title">
                                        {{ $prod->name }}
                                    </a>
                                    <div class="product-subtitle">
                                        @if($prod->modelVariant)
                                            <i class="fa-solid fa-car text-danger me-1"></i> {{ $prod->modelVariant->full_name }}
                                        @elseif($prod->carModel)
                                            <i class="fa-solid fa-car text-danger me-1"></i> {{ $prod->carModel->brand->name ?? '' }} {{ $prod->carModel->name }}
                                        @else
                                            {{ $prod->compatible_models_text ?: 'All Models' }}
                                        @endif
                                    </div>

                                    <div class="mt-auto pt-2 border-top">
                                        <div class="d-flex align-items-baseline mb-2">
                                            <span class="price-tag">&#x20B9;{{ number_format($prod->active_price, 0) }}</span>
                                            @if($prod->has_discount)
                                                <span class="old-price">&#x20B9;{{ number_format($prod->regular_price, 0) }}</span>
                                                <span class="badge bg-success-subtle text-success ms-auto font-sm">{{ $prod->discount_percentage }}% OFF</span>
                                            @endif
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="button" onclick="addToCartAjax({{ $prod->id }})" class="btn btn-theme-red w-100 py-2 d-flex align-items-center justify-content-center gap-1 font-sm">
                                                <i class="fa-solid fa-cart-plus me-1"></i> <span>ADD TO CART</span>
                                            </button>
                                            <button type="button" onclick="toggleWishlist({{ $prod->id }}, this)" class="btn btn-outline-secondary px-2 py-2" title="Wishlist">
                                                <i class="{{ in_array($prod->id, $globalWishlistProductIds ?? []) ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart' }}"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Mobile Filter Offcanvas Drawer -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilterDrawer" aria-labelledby="mobileFilterDrawerLabel">
    <div class="offcanvas-header bg-dark text-white border-bottom">
        <h5 class="offcanvas-title font-sm fw-bold brand-font text-uppercase" id="mobileFilterDrawerLabel">
            <i class="fa-solid fa-sliders text-danger me-2"></i> FILTER CATALOG
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        @include('frontend.partials.shop_filters', ['isMobile' => true])
    </div>
</div>

@push('styles')
<style>
    /* Professional Shop Filter Styling */
    .shop-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .filter-header-bar {
        background: #ffffff;
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .filter-section-title {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #0f172a;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Vehicle Fitment Card */
    .filter-vehicle-box {
        background: linear-gradient(135deg, #0b0f19 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(11, 15, 25, 0.15);
    }
    .filter-vehicle-box .form-select {
        background-color: #ffffff;
        border-color: #cbd5e1;
        font-size: 13px;
        font-weight: 500;
    }

    /* Active Vehicle Badge */
    .active-vehicle-banner {
        background: rgba(217, 4, 41, 0.15);
        border: 1px solid rgba(217, 4, 41, 0.3);
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 12px;
        font-size: 12px;
    }

    /* Category Filter List */
    .filter-cat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        border-radius: 6px;
        color: #334155;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.15s ease;
        margin-bottom: 2px;
    }
    .filter-cat-item:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .filter-cat-item.active {
        background-color: #fff1f2;
        color: var(--primary-red);
        font-weight: 700;
    }
    .filter-cat-item.active .badge {
        background-color: var(--primary-red) !important;
        color: #ffffff !important;
    }

    /* Visual Finish Swatches */
    .filter-swatch-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .filter-swatch-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        font-size: 12px;
        color: #334155;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .filter-swatch-pill:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #0f172a;
    }
    .filter-swatch-pill.active {
        border-color: var(--primary-red);
        background: #fff1f2;
        color: var(--primary-red);
        font-weight: 700;
        box-shadow: 0 0 0 1px var(--primary-red);
    }
    .swatch-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid rgba(0,0,0,0.15);
    }

    /* Placement Pills */
    .filter-placement-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .filter-placement-pill {
        padding: 5px 11px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        font-size: 12px;
        color: #334155;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .filter-placement-pill:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .filter-placement-pill.active {
        border-color: #0b0f19;
        background: #0b0f19;
        color: #ffffff;
        font-weight: 600;
    }

    /* Price Presets */
    .filter-price-preset-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 10px;
    }
    .filter-price-preset {
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        text-decoration: none;
        transition: all 0.15s;
    }
    .filter-price-preset:hover {
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .filter-price-preset.active {
        border-color: var(--primary-red);
        background: var(--primary-red);
        color: #ffffff;
        font-weight: 600;
    }

    /* Active Filter Chips */
    .active-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        font-size: 12px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .active-filter-chip:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: var(--primary-red);
    }
    .active-filter-chip i {
        font-size: 11px;
        color: #94a3b8;
    }
    .active-filter-chip:hover i {
        color: var(--primary-red);
    }
</style>
@endpush
@endsection
