@extends('layouts.app')

@section('title', "{$company->name} {$car->name} {$variant->name} Products & Accessories - AutoLogos")
@section('meta_description', "Shop verified compatible car logos, chrome emblems, monograms and badges for {$company->name} {$car->name} {$variant->name}.")

@section('content')
<!-- Breadcrumbs -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles') }}" class="text-decoration-none text-muted">Vehicles</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicle.cars', $company->slug) }}" class="text-decoration-none text-muted">{{ $company->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicle.variants', [$company->slug, $car->slug]) }}" class="text-decoration-none text-muted">{{ $car->name }}</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $variant->name }} Trim</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    @php
        $activeFilterCount = 0;
        if(request()->filled('category')) $activeFilterCount++;
        if(request()->filled('placement')) $activeFilterCount++;
        if(request()->filled('finish')) $activeFilterCount++;
        if(request()->filled('min_price') || request()->filled('max_price')) $activeFilterCount++;
        if(request()->filled('q')) $activeFilterCount++;

        $finishes = [
            ['id' => 'Chrome', 'name' => 'Chrome', 'dot' => 'background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 50%, #94a3b8 100%);'],
            ['id' => 'Gloss Black', 'name' => 'Piano Black', 'dot' => 'background: #0b0f19; box-shadow: inset 0 1px 1px rgba(255,255,255,0.4);'],
            ['id' => 'Matte Black', 'name' => 'Matte Black', 'dot' => 'background: #334155;'],
            ['id' => 'Red', 'name' => 'Racing Red', 'dot' => 'background: #d90429;'],
            ['id' => 'Gold', 'name' => 'Royal Gold', 'dot' => 'background: linear-gradient(135deg, #fef08a 0%, #eab308 100%);'],
        ];

        $placements = [
            ['id' => '', 'name' => 'All', 'icon' => 'fa-border-all'],
            ['id' => 'Front', 'name' => 'Front Grille', 'icon' => 'fa-shield-halved'],
            ['id' => 'Rear', 'name' => 'Rear Trunk', 'icon' => 'fa-car-rear'],
            ['id' => 'Fender', 'name' => 'Side Fender', 'icon' => 'fa-car-side'],
        ];
    @endphp

    <!-- Active Vehicle Hero Banner with Quick Trim Switcher -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #090d16 0%, #172033 100%); border-left: 5px solid var(--primary-red) !important;">
        <div class="row align-items-center g-3 position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-danger text-uppercase px-2 py-1" style="font-size: 10px;">
                        <i class="fa-solid fa-car-side me-1"></i> Selected Vehicle
                    </span>
                    <span class="badge bg-white bg-opacity-10 text-white font-sm" style="font-size: 11px;">
                        <i class="fa-solid fa-circle-check text-success me-1"></i> Exact Fitment Guarantee
                    </span>
                    <span class="text-white-50 small">
                        Showing {{ $products->total() }} Product(s)
                    </span>
                </div>
                <h1 class="fw-black brand-font text-uppercase mb-1" style="font-size: calc(1.3rem + 0.9vw);">
                    {{ $company->name }} <span class="text-danger">{{ $car->name }}</span>
                    <span class="text-light fw-normal fs-4">({{ $variant->name }})</span>
                </h1>
                <p class="text-white-50 font-sm mb-0">Showing products specifically compatible with this vehicle trim.</p>
            </div>

            <div class="col-lg-5 text-lg-end">
                <div class="d-inline-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('shop', ['brand' => $company->slug, 'model' => $car->slug]) }}" class="btn btn-danger btn-sm px-3 shadow-sm">
                        <i class="fa-solid fa-table-cells me-1"></i> All {{ $car->name }} Products
                    </a>
                    <a href="{{ route('vehicle.variants', [$company->slug, $car->slug]) }}" class="btn btn-outline-light btn-sm px-3">
                        <i class="fa-solid fa-code-fork me-1"></i> All Trims
                    </a>
                    <a href="{{ route('vehicle.cars', $company->slug) }}" class="btn btn-outline-light btn-sm px-3">
                        <i class="fa-solid fa-car me-1"></i> All {{ $company->name }}
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Mobile Filter & Sort Bar (Visible on mobile screens) -->
    <div class="d-lg-none mb-3">
        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-dark flex-grow-1 py-2 font-sm fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileVariantFilterDrawer">
                <i class="fa-solid fa-sliders text-danger"></i>
                <span>FILTERS</span>
                @if($activeFilterCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $activeFilterCount }}</span>
                @endif
            </button>
            <div class="flex-grow-1">
                <select class="form-select form-select-sm py-2 font-sm" onchange="location = this.value;">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort', 'popular') == 'popular' ? 'selected' : '' }}>Popular</option>
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
                <div class="shop-filter-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="fw-bold font-sm text-uppercase text-dark">
                            <i class="fa-solid fa-sliders text-danger me-2"></i> FILTERS
                        </span>
                        @if($activeFilterCount > 0)
                            <a href="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" class="text-danger small text-decoration-none fw-semibold">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset All
                            </a>
                        @endif
                    </div>

                    <!-- 1. VEHICLE TRIM CONTEXT -->
                    <div class="filter-vehicle-box mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold font-sm text-uppercase text-white d-flex align-items-center">
                                <i class="fa-solid fa-car-side text-danger me-2"></i> VEHICLE TRIM
                            </span>
                            <span class="badge bg-danger font-sm px-2 py-1" style="font-size: 10px;">EXACT FIT</span>
                        </div>
                        <div class="p-2 rounded bg-white bg-opacity-10 mb-3">
                            <div class="fw-bold text-white font-sm">{{ $company->name }} {{ $car->name }}</div>
                            <div class="text-white-50 small"><i class="fa-solid fa-code-fork text-danger me-1"></i> {{ $variant->name }} Trim</div>
                        </div>

                        <!-- Sibling Trims List -->
                        <div class="mb-1">
                            <label class="font-sm text-white-50 text-uppercase fw-bold mb-2 d-block" style="font-size: 10px; letter-spacing: 0.5px;">Switch {{ $car->name }} Trim:</label>
                            <div class="d-flex flex-column gap-1">
                                @foreach($siblingVariants as $sv)
                                    <a href="{{ route('vehicle.products', [$company->slug, $car->slug, $sv->slug]) }}" class="d-flex justify-content-between align-items-center py-1 px-2 rounded text-decoration-none font-sm {{ $sv->slug == $variant->slug ? 'bg-danger text-white fw-bold shadow-sm' : 'text-white-50 hover-bg-white-10' }}" style="font-size: 12px; transition: all 0.15s;">
                                        <span>
                                            @if($sv->slug == $variant->slug)
                                                <i class="fa-solid fa-circle-dot text-white me-1"></i>
                                            @else
                                                <i class="fa-regular fa-circle text-white-50 me-1"></i>
                                            @endif
                                            {{ $sv->name }}
                                        </span>
                                        <span class="badge {{ $sv->slug == $variant->slug ? 'bg-white text-danger' : 'bg-secondary bg-opacity-50 text-white' }} rounded-pill" style="font-size: 10px;">
                                            {{ $sv->products_count }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 2. SEARCH WITHIN VARIANT -->
                    <div class="mb-4">
                        <div class="filter-section-title">
                            <span><i class="fa-solid fa-magnifying-glass text-danger me-1"></i> Search in {{ $variant->name }}</span>
                            @if(request()->filled('q'))
                                <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
                            @endif
                        </div>
                        <form action="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" method="GET" class="filter-form-clean">
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            @if(request('placement')) <input type="hidden" name="placement" value="{{ request('placement') }}"> @endif
                            @if(request('finish')) <input type="hidden" name="finish" value="{{ request('finish') }}"> @endif
                            @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
                            @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                            <div class="input-group input-group-sm">
                                <input type="text" name="q" class="form-control" placeholder="Search emblems, logos..." value="{{ request('q') }}">
                                <button type="submit" class="btn btn-dark"><i class="fa-solid fa-search"></i></button>
                            </div>
                        </form>
                    </div>

                    <!-- 3. CATEGORIES FILTER -->
                    <div class="mb-4">
                        <div class="filter-section-title">
                            <span><i class="fa-solid fa-shapes text-danger me-1"></i> Categories</span>
                            @if(request()->filled('category'))
                                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
                            @endif
                        </div>
                        <div class="d-flex flex-column">
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="filter-cat-item {{ !request()->filled('category') ? 'active' : '' }}">
                                <span><i class="fa-solid fa-border-all me-2 text-muted" style="width: 14px;"></i> All Categories</span>
                                <span class="badge bg-light text-muted rounded-pill">{{ $categories->sum('products_count') }}</span>
                            </a>
                            @foreach($categories as $cat)
                                @php
                                    $catIcon = 'fa-certificate';
                                    if (str_contains($cat->slug, 'logo')) $catIcon = 'fa-shield-halved';
                                    elseif (str_contains($cat->slug, 'monogram')) $catIcon = 'fa-bolt';
                                    elseif (str_contains($cat->slug, 'letter') || str_contains($cat->slug, 'number')) $catIcon = 'fa-font';
                                    elseif (str_contains($cat->slug, 'access')) $catIcon = 'fa-wrench';
                                @endphp
                                <a href="{{ request('category') == $cat->slug ? request()->fullUrlWithQuery(['category' => null]) : request()->fullUrlWithQuery(['category' => $cat->slug]) }}" class="filter-cat-item {{ request('category') == $cat->slug ? 'active' : '' }}">
                                    <span><i class="fa-solid {{ $catIcon }} me-2 text-muted" style="width: 14px;"></i> {{ $cat->name }}</span>
                                    <span class="badge bg-light text-muted rounded-pill">{{ $cat->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- 4. PRICE RANGE FILTER -->
                    <div class="mb-4">
                        <div class="filter-section-title">
                            <span><i class="fa-solid fa-indian-rupee-sign text-danger me-1"></i> Price Range</span>
                            @if(request()->filled('min_price') || request()->filled('max_price'))
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
                            @endif
                        </div>

                        <!-- Quick Price Presets -->
                        <div class="filter-price-preset-grid">
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="filter-price-preset {{ !request()->filled('min_price') && !request()->filled('max_price') ? 'active' : '' }}">
                                All
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => 500]) }}" class="filter-price-preset {{ request('max_price') == 500 && !request()->filled('min_price') ? 'active' : '' }}">
                                Under &#x20B9;500
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => 500, 'max_price' => 1000]) }}" class="filter-price-preset {{ request('min_price') == 500 && request('max_price') == 1000 ? 'active' : '' }}">
                                &#x20B9;500 - 1K
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => 1000, 'max_price' => 2000]) }}" class="filter-price-preset {{ request('min_price') == 1000 && request('max_price') == 2000 ? 'active' : '' }}">
                                &#x20B9;1K - 2K
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => 2000, 'max_price' => null]) }}" class="filter-price-preset {{ request('min_price') == 2000 && !request()->filled('max_price') ? 'active' : '' }}">
                                &#x20B9;2,000+
                            </a>
                        </div>

                        <!-- Custom Price Range Inputs -->
                        <form action="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" method="GET" class="mt-2 filter-form-clean">
                            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            @if(request('placement')) <input type="hidden" name="placement" value="{{ request('placement') }}"> @endif
                            @if(request('finish')) <input type="hidden" name="finish" value="{{ request('finish') }}"> @endif
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                            <div class="d-flex gap-2 align-items-center mb-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted">&#x20B9;</span>
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}" min="0">
                                </div>
                                <span class="text-muted font-sm">-</span>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted">&#x20B9;</span>
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}" min="0">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-dark btn-sm w-100 font-sm py-1">
                                Apply Price
                            </button>
                        </form>
                    </div>

                    <!-- 5. FINISH & COLOR SWATCH FILTER -->
                    <div class="mb-4">
                        <div class="filter-section-title">
                            <span><i class="fa-solid fa-palette text-danger me-1"></i> Finish / Color</span>
                            @if(request()->filled('finish'))
                                <a href="{{ request()->fullUrlWithQuery(['finish' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
                            @endif
                        </div>
                        <div class="filter-swatch-grid">
                            @foreach($finishes as $f)
                                <a href="{{ request('finish') == $f['id'] ? request()->fullUrlWithQuery(['finish' => null]) : request()->fullUrlWithQuery(['finish' => $f['id']]) }}" class="filter-swatch-pill {{ request('finish') == $f['id'] ? 'active' : '' }}">
                                    <span class="swatch-dot" style="{{ $f['dot'] }}"></span>
                                    <span>{{ $f['name'] }}</span>
                                    @if(request('finish') == $f['id'])
                                        <i class="fa-solid fa-check ms-1 font-sm"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- 6. PLACEMENT FILTER -->
                    <div class="mb-3">
                        <div class="filter-section-title">
                            <span><i class="fa-solid fa-arrows-to-dot text-danger me-1"></i> Placement</span>
                            @if(request()->filled('placement'))
                                <a href="{{ request()->fullUrlWithQuery(['placement' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
                            @endif
                        </div>
                        <div class="filter-placement-grid">
                            @foreach($placements as $p)
                                @if($p['id'] == '')
                                    <a href="{{ request()->fullUrlWithQuery(['placement' => null]) }}" class="filter-placement-pill {{ !request()->filled('placement') ? 'active' : '' }}">
                                        <i class="fa-solid {{ $p['icon'] }} me-1 text-muted"></i> {{ $p['name'] }}
                                    </a>
                                @else
                                    <a href="{{ request('placement') == $p['id'] ? request()->fullUrlWithQuery(['placement' => null]) : request()->fullUrlWithQuery(['placement' => $p['id']]) }}" class="filter-placement-pill {{ request('placement') == $p['id'] ? 'active' : '' }}">
                                        <i class="fa-solid {{ $p['icon'] }} me-1 text-muted"></i> {{ $p['name'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- 7. SIBLING CARS QUICK LINK -->
                    <div class="pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-sm fw-bold text-dark text-uppercase"><i class="fa-solid fa-car text-danger me-1"></i> Other {{ $company->name }} Models</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($siblingCars as $sc)
                                <a href="{{ route('vehicle.variants', [$company->slug, $sc->slug]) }}" class="badge {{ $sc->slug == $car->slug ? 'bg-danger text-white' : 'bg-light text-dark border' }} text-decoration-none py-1 px-2 font-sm" style="font-weight: 500;">
                                    {{ $sc->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Products Grid & Controls Area -->
        <div class="col-lg-9">
            <!-- Top Controls Bar -->
            <div class="card border-0 bg-white shadow-sm rounded-3 p-3 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <span class="text-muted font-sm">
                            Showing <strong class="text-dark">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</strong> of <strong class="text-dark">{{ $products->total() }}</strong> products for <strong class="text-danger">{{ $car->name }} {{ $variant->name }}</strong>
                        </span>
                        @if(request()->filled('q'))
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

                        <a href="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill font-sm ms-auto fw-semibold" style="font-size: 11px;">
                            <i class="fa-solid fa-rotate-left me-1"></i> Clear All
                        </a>
                    </div>
                </div>
            @endif

            <!-- Products List -->
            @if($products->isEmpty())
                <div class="card border-0 bg-white shadow-sm p-5 text-center my-4 rounded-4">
                    <i class="fa-solid fa-car-tunnel text-muted display-3 mb-3"></i>
                    <h4 class="fw-bold">No Products Available for This Variant</h4>
                    <p class="text-muted">
                        We currently do not have specific products matching your filters for <strong>{{ $company->name }} {{ $car->name }} &bull; {{ $variant->name }}</strong>.
                    </p>
                    <div class="d-flex justify-content-center flex-wrap gap-2 mt-3">
                        @if($activeFilterCount > 0)
                            <a href="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" class="btn btn-outline-danger px-3 py-2">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset Active Filters
                            </a>
                        @endif
                        <a href="{{ route('shop', ['brand' => $company->slug, 'model' => $car->slug]) }}" class="btn btn-theme-red px-4 py-2">
                            <i class="fa-solid fa-box-open me-2"></i> View All {{ $car->name }} Products
                        </a>
                        <a href="{{ route('vehicle.variants', [$company->slug, $car->slug]) }}" class="btn btn-outline-secondary px-3 py-2">
                            <i class="fa-solid fa-code-fork me-1"></i> Other Trims
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
                                    <div class="mb-1 d-flex align-items-center gap-1">
                                        <span class="badge bg-danger-subtle text-danger font-sm" style="font-size: 10px;">
                                            <i class="fa-solid fa-circle-check me-1"></i> {{ $variant->name }}
                                        </span>
                                        @if($prod->placement)
                                            <span class="badge bg-light text-muted border font-sm" style="font-size: 10px;">
                                                {{ $prod->placement }}
                                            </span>
                                        @endif
                                        @if($prod->finish)
                                            <span class="badge bg-light text-muted border font-sm" style="font-size: 10px;">
                                                {{ $prod->finish }}
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ route('product.detail', $prod->slug) }}" class="product-title">
                                        {{ $prod->name }}
                                    </a>
                                    <div class="product-subtitle">{{ $company->name }} {{ $car->name }} &bull; {{ $variant->name }} Trim</div>

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
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileVariantFilterDrawer" aria-labelledby="mobileVariantFilterDrawerLabel">
    <div class="offcanvas-header bg-dark text-white border-bottom">
        <h5 class="offcanvas-title font-sm fw-bold brand-font text-uppercase" id="mobileVariantFilterDrawerLabel">
            <i class="fa-solid fa-sliders text-danger me-2"></i> FILTER {{ $car->name }} {{ $variant->name }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        <!-- Reusable clean mobile filter controls -->
        <div class="shop-filter-card p-3 border-0 shadow-none">
            <!-- Search -->
            <div class="mb-4">
                <label class="fw-bold font-sm text-dark text-uppercase mb-2 d-block">Search</label>
                <form action="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" method="GET" class="filter-form-clean">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ request('q') }}">
                        <button type="submit" class="btn btn-dark"><i class="fa-solid fa-search"></i></button>
                    </div>
                </form>
            </div>

            <!-- Categories -->
            <div class="mb-4">
                <label class="fw-bold font-sm text-dark text-uppercase mb-2 d-block">Categories</label>
                <div class="d-flex flex-column">
                    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="filter-cat-item {{ !request()->filled('category') ? 'active' : '' }}">
                        <span>All Categories</span>
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ request('category') == $cat->slug ? request()->fullUrlWithQuery(['category' => null]) : request()->fullUrlWithQuery(['category' => $cat->slug]) }}" class="filter-cat-item {{ request('category') == $cat->slug ? 'active' : '' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="badge bg-light text-muted rounded-pill">{{ $cat->products_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Finishes -->
            <div class="mb-4">
                <label class="fw-bold font-sm text-dark text-uppercase mb-2 d-block">Finish / Color</label>
                <div class="filter-swatch-grid">
                    @foreach($finishes as $f)
                        <a href="{{ request('finish') == $f['id'] ? request()->fullUrlWithQuery(['finish' => null]) : request()->fullUrlWithQuery(['finish' => $f['id']]) }}" class="filter-swatch-pill {{ request('finish') == $f['id'] ? 'active' : '' }}">
                            <span class="swatch-dot" style="{{ $f['dot'] }}"></span>
                            <span>{{ $f['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Placements -->
            <div class="mb-4">
                <label class="fw-bold font-sm text-dark text-uppercase mb-2 d-block">Placement</label>
                <div class="filter-placement-grid">
                    @foreach($placements as $p)
                        <a href="{{ request('placement') == $p['id'] ? request()->fullUrlWithQuery(['placement' => null]) : request()->fullUrlWithQuery(['placement' => $p['id']]) }}" class="filter-placement-pill {{ request('placement') == $p['id'] ? 'active' : '' }}">
                            <span>{{ $p['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Price Range -->
            <div class="mb-4">
                <label class="fw-bold font-sm text-dark text-uppercase mb-2 d-block">Price Range</label>
                <div class="filter-price-preset-grid mb-2">
                    <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="filter-price-preset {{ !request()->filled('min_price') && !request()->filled('max_price') ? 'active' : '' }}">
                        All
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => 500]) }}" class="filter-price-preset {{ request('max_price') == 500 && !request()->filled('min_price') ? 'active' : '' }}">
                        Under &#x20B9;500
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['min_price' => 500, 'max_price' => 1000]) }}" class="filter-price-preset {{ request('min_price') == 500 && request('max_price') == 1000 ? 'active' : '' }}">
                        &#x20B9;500 - 1K
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['min_price' => 1000, 'max_price' => 2000]) }}" class="filter-price-preset {{ request('min_price') == 1000 && request('max_price') == 2000 ? 'active' : '' }}">
                        &#x20B9;1K - 2K
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['min_price' => 2000, 'max_price' => null]) }}" class="filter-price-preset {{ request('min_price') == 2000 && !request()->filled('max_price') ? 'active' : '' }}">
                        &#x20B9;2,000+
                    </a>
                </div>

                <form action="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" method="GET" class="filter-form-clean">
                    @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('placement')) <input type="hidden" name="placement" value="{{ request('placement') }}"> @endif
                    @if(request('finish')) <input type="hidden" name="finish" value="{{ request('finish') }}"> @endif
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                    <div class="d-flex gap-2 align-items-center mb-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted">&#x20B9;</span>
                            <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}" min="0">
                        </div>
                        <span class="text-muted font-sm">-</span>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted">&#x20B9;</span>
                            <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}" min="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-dark btn-sm w-100 font-sm py-1">
                        Apply Price
                    </button>
                </form>
            </div>

            @if($activeFilterCount > 0)
                <div class="pt-3 border-top">
                    <a href="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" class="btn btn-outline-danger btn-sm w-100 font-sm">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset All Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Professional Filter Sidebar & Components */
    .shop-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        overflow: hidden;
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

    /* Vehicle Box */
    .filter-vehicle-box {
        background: linear-gradient(135deg, #0b0f19 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 10px;
        padding: 14px;
        box-shadow: 0 4px 12px rgba(11, 15, 25, 0.15);
    }
    .hover-bg-white-10:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff !important;
    }

    /* Category Filter */
    .filter-cat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 10px;
        border-radius: 6px;
        font-size: 13px;
        color: #334155;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
        margin-bottom: 2px;
    }
    .filter-cat-item:hover {
        background: #f8fafc;
        color: var(--primary-red);
    }
    .filter-cat-item.active {
        background: rgba(217, 4, 41, 0.08);
        color: var(--primary-red);
        font-weight: 700;
    }
    .filter-cat-item.active .badge {
        background-color: var(--primary-red) !important;
        color: #ffffff !important;
    }

    /* Color / Finish Swatches */
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
        transition: all 0.15s;
    }
    .filter-swatch-pill:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .filter-swatch-pill.active {
        border-color: var(--primary-red);
        background: rgba(217, 4, 41, 0.05);
        color: var(--primary-red);
        font-weight: 700;
    }
    .swatch-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid rgba(0,0,0,0.1);
    }

    /* Placement Filter */
    .filter-placement-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .filter-placement-pill {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        text-decoration: none;
        transition: all 0.15s;
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.filter-form-clean').forEach(function(form) {
            form.addEventListener('submit', function() {
                form.querySelectorAll('input').forEach(function(el) {
                    if (!el.value) {
                        el.disabled = true;
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
