@extends('layouts.admin')

@section('title', 'Catalog & Vehicle Analytics - AutoLogos Admin')
@section('page_title', 'Catalog & Vehicle Analytics')

@push('styles')
<style>
    .analytics-card {
        border-radius: 12px;
        transition: all 0.25s ease;
        border: 1px solid #e2e8f0;
    }
    .analytics-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.08) !important;
    }
    .icon-badge {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .brand-avatar {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 700;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
    }
    .table-container-scroll {
        max-height: 460px;
        overflow-y: auto;
    }
    .table-container-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .table-container-scroll::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .table-container-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .table-sticky-header th {
        position: sticky;
        top: 0;
        background-color: #f8fafc !important;
        z-index: 10;
        box-shadow: inset 0 -1px 0 #e2e8f0;
    }
    .quick-brand-pill {
        transition: all 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .quick-brand-pill:hover, .quick-brand-pill.active {
        background: var(--admin-red);
        color: #ffffff;
        border-color: var(--admin-red);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">

    <!-- 1. TOP HEADER & DUAL DASHBOARD SWITCHER -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Title & Status Badge -->
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="fw-bold mb-0 text-dark">Catalog & Vehicle Analytics</h4>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 font-sm rounded-pill">
                            <i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> Live Storefront Data
                        </span>
                    </div>
                    <p class="text-muted small mb-0">
                        Real-time analytics for vehicle companies, car models, trim variants, and category inventory.
                    </p>
                </div>

                <!-- Navigation Switcher Pills & Action Buttons -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <ul class="nav nav-pills p-1 bg-light rounded-pill border">
                        <li class="nav-item">
                            <a class="nav-link py-2 px-3 fw-bold font-sm text-secondary rounded-pill" href="{{ route('admin.dashboard') }}">
                                <i class="fa-solid fa-gauge-high me-1"></i> Sales Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-2 px-3 fw-bold font-sm active bg-danger text-white rounded-pill shadow-sm" href="{{ route('admin.catalog.analytics') }}">
                                <i class="fa-solid fa-chart-pie me-1"></i> Catalog Analytics
                            </a>
                        </li>
                    </ul>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-theme-red rounded-3 fw-bold px-3 py-2">
                            <i class="fa-solid fa-plus me-1"></i> Add Product
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 fw-semibold px-3 py-2">
                            <i class="fa-solid fa-boxes-stacked me-1"></i> View All
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. HIGH-LEVEL KPI METRIC CARDS -->
    <div class="row g-3 mb-4">
        <!-- 1. Products -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card analytics-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-xl-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Live Products</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalProducts) }}</h3>
                        <div class="small mt-2 d-flex align-items-center gap-1">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-2">
                                <i class="fa-solid fa-check me-1"></i>{{ $activeProducts }} Active
                            </span>
                            @if($inactiveProducts > 0)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">
                                    {{ $inactiveProducts }} Draft
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="icon-badge bg-danger bg-opacity-10 text-danger">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Stock Units & Value -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card analytics-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-xl-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Warehouse Units</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalStock) }} <span class="fs-6 fw-normal text-muted">pcs</span></h3>
                        <div class="small mt-2 text-success fw-semibold">
                            <i class="fa-solid fa-indian-rupee-sign me-1"></i> Est. &#x20B9;{{ number_format($totalInventoryValue, 0) }}
                        </div>
                    </div>
                    <div class="icon-badge bg-success bg-opacity-10 text-success">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Vehicle Companies -->
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card analytics-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-xl-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Car Companies</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalBrands) }}</h3>
                        <div class="small mt-2 text-primary fw-semibold">
                            <i class="fa-solid fa-car-side me-1"></i> {{ $totalModels }} Car Models
                        </div>
                    </div>
                    <div class="icon-badge bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Trim Variants & Coverage -->
        <div class="col-xl col-md-6 col-sm-6">
            <div class="card analytics-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-xl-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Trim Variants</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalVariants) }}</h3>
                        <div class="small mt-2 text-purple fw-semibold" style="color: #8b5cf6;">
                            <i class="fa-solid fa-circle-check me-1"></i> {{ $coveragePercentage }}% Catalog Coverage
                        </div>
                    </div>
                    <div class="icon-badge text-purple" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <i class="fa-solid fa-code-fork"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Categories -->
        <div class="col-xl col-md-6 col-sm-6">
            <div class="card analytics-card shadow-sm h-100 bg-white">
                <div class="card-body p-3 p-xl-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Categories</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalCategories) }}</h3>
                        <div class="small mt-2 text-warning-emphasis fw-semibold">
                            <i class="fa-solid fa-sitemap me-1"></i> {{ $parentCategories }} Main &bull; {{ $subCategories }} Subs
                        </div>
                    </div>
                    <div class="icon-badge bg-warning bg-opacity-10 text-warning-emphasis">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. INVENTORY STOCK HEALTH RIBBON -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">
                        <i class="fa-solid fa-chart-pie text-danger me-2"></i> Inventory Health & Gap Assessment
                    </h6>
                    <span class="text-muted small">Warehouse stock status and vehicle variants that need inventory added</span>
                </div>
                <div class="text-muted font-sm">
                    Total Products: <strong>{{ number_format($totalProducts) }}</strong> &bull; Total Variants: <strong>{{ number_format($totalVariants) }}</strong>
                </div>
            </div>

            <!-- Health Status Segments -->
            <div class="row g-2 mb-3">
                <div class="col-md-3 col-6">
                    <div class="p-3 rounded-3 border bg-success bg-opacity-10 border-success border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-success small fw-bold text-uppercase" style="font-size: 10px;">In Stock (>5 Units)</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($inStockProducts) }}</div>
                            <div class="small text-success opacity-75">Healthy supply</div>
                        </div>
                        <i class="fa-solid fa-circle-check text-success fs-2 opacity-50"></i>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="p-3 rounded-3 border bg-warning bg-opacity-10 border-warning border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-warning-emphasis small fw-bold text-uppercase" style="font-size: 10px;">Low Stock (1-5 Units)</div>
                            <div class="fs-4 fw-bold text-warning-emphasis">{{ number_format($lowStockProducts) }}</div>
                            <div class="small text-warning-emphasis opacity-75">Needs reorder soon</div>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation text-warning fs-2 opacity-50"></i>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="p-3 rounded-3 border bg-danger bg-opacity-10 border-danger border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-danger small fw-bold text-uppercase" style="font-size: 10px;">Out Of Stock (0 Units)</div>
                            <div class="fs-4 fw-bold text-danger">{{ number_format($outOfStockProducts) }}</div>
                            <div class="small text-danger opacity-75">Unavailable in store</div>
                        </div>
                        <i class="fa-solid fa-circle-xmark text-danger fs-2 opacity-50"></i>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="p-3 rounded-3 border bg-primary bg-opacity-10 border-primary border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-primary small fw-bold text-uppercase" style="font-size: 10px;">Variants Missing Products</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($variantsWithoutProducts) }}</div>
                            <div class="small text-primary opacity-75">Catalog expansion gap</div>
                        </div>
                        <i class="fa-solid fa-code-pull-request text-primary fs-2 opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Segmented Progress Bar -->
            @php
                $pctInStock = $totalProducts > 0 ? round(($inStockProducts / $totalProducts) * 100, 1) : 0;
                $pctLowStock = $totalProducts > 0 ? round(($lowStockProducts / $totalProducts) * 100, 1) : 0;
                $pctOutOfStock = $totalProducts > 0 ? round(($outOfStockProducts / $totalProducts) * 100, 1) : 0;
            @endphp
            <div class="progress rounded-pill shadow-none" style="height: 10px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctInStock }}%" title="In Stock: {{ $pctInStock }}%"></div>
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pctLowStock }}%" title="Low Stock: {{ $pctLowStock }}%"></div>
                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $pctOutOfStock }}%" title="Out of Stock: {{ $pctOutOfStock }}%"></div>
            </div>
        </div>
    </div>

    <!-- 4. INTERACTIVE VEHICLE BRAND EXPLORER & QUICK PILLS -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <div class="row align-items-center g-3 mb-3">
                <div class="col-lg-5 col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-car text-danger me-1"></i> Explore by Vehicle Company:
                    </label>
                    <select class="form-select border-secondary-subtle" onchange="location = this.value ? '{{ route('admin.catalog.analytics') }}?brand_id=' + this.value : '{{ route('admin.catalog.analytics') }}';">
                        <option value="">-- All Companies (Overview of 18 Brands) --</option>
                        @foreach($allBrands as $b)
                            <option value="{{ $b->id }}" {{ $selectedBrandId == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-7 col-md-6">
                    <label class="form-label text-muted small text-uppercase mb-2 fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                        Quick Jump:
                    </label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($brandsMatrix->take(6) as $qb)
                            <a href="{{ route('admin.catalog.analytics', ['brand_id' => $qb->id]) }}" class="quick-brand-pill {{ $selectedBrandId == $qb->id ? 'active' : '' }}">
                                <span>{{ $qb->name }}</span>
                                <span class="badge bg-secondary rounded-pill" style="font-size: 9px;">{{ $qb->products_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- If Brand is selected: show its detailed Card & Models breakdown -->
            @if($selectedBrand)
                <div class="p-3 p-md-4 rounded-3 border bg-light mt-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-white p-2 border shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fa-solid fa-car text-danger fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $selectedBrand->name }}</h5>
                                <div class="text-muted small">
                                    <strong>{{ $selectedBrand->car_models_count }}</strong> Models &bull;
                                    <strong>{{ $selectedBrand->products_count }}</strong> Live Products in Store
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products.index', ['brand_id' => $selectedBrand->id]) }}" class="btn btn-sm btn-theme-red fw-bold">
                                <i class="fa-solid fa-filter me-1"></i> View {{ $selectedBrand->name }} Products ({{ $selectedBrand->products_count }})
                            </a>
                            <a href="{{ route('admin.catalog.analytics') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-xmark me-1"></i> Clear Filter
                            </a>
                        </div>
                    </div>

                    <!-- Models List Under This Brand -->
                    @if($selectedBrandModels->isNotEmpty())
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="fa-solid fa-car-side text-danger me-2"></i> {{ $selectedBrand->name }} Models & Products Matrix
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle font-sm mb-0 bg-white">
                                <thead class="table-light">
                                    <tr>
                                        <th>Car Model</th>
                                        <th class="text-center" style="width: 140px;">Variants</th>
                                        <th class="text-center" style="width: 160px;">Products Mapped</th>
                                        <th class="text-center" style="width: 140px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedBrandModels as $bm)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $bm->name }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary rounded-pill">{{ $bm->variants_count }} Variants</span>
                                            </td>
                                            <td class="text-center">
                                                @if($bm->products_count > 0)
                                                    <span class="badge bg-success rounded-pill px-3">{{ $bm->products_count }} Products</span>
                                                @else
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">0 Products (Gap)</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.products.index', ['brand_id' => $selectedBrand->id, 'car_model_id' => $bm->id]) }}" class="btn btn-sm btn-outline-danger py-0 px-2 font-sm">
                                                    Filter Products
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- 5. VISUAL ANALYTICS CHARTS (2x2 GRID) -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Top Vehicle Brands -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            <i class="fa-solid fa-chart-column text-danger me-2"></i> Top Vehicle Companies
                        </h6>
                        <span class="text-muted small">Brands with the highest number of live products</span>
                    </div>
                    <span class="badge bg-light text-muted border font-sm">Top 10</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 310px; position: relative;">
                        <canvas id="brandChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Products by Category -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            <i class="fa-solid fa-chart-pie text-danger me-2"></i> Category Distribution
                        </h6>
                        <span class="text-muted small">Live catalog distribution across primary categories</span>
                    </div>
                    <span class="badge bg-light text-muted border font-sm">{{ $totalCategories }} Categories</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 310px; position: relative;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Vehicle Placement Breakdown -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            <i class="fa-solid fa-arrows-to-dot text-danger me-2"></i> Vehicle Placement Distribution
                        </h6>
                        <span class="text-muted small">Front Grille, Rear Trunk, Fender, Steering, Bonnet</span>
                    </div>
                    <span class="badge bg-light text-muted border font-sm">Position</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 290px; position: relative;">
                        <canvas id="placementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 4: Product Finishes & Chrome Accents -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            <i class="fa-solid fa-wand-magic-sparkles text-danger me-2"></i> Finishes & Color Types
                        </h6>
                        <span class="text-muted small">Chrome, Gloss Black, Matte Black, Carbon Fiber</span>
                    </div>
                    <span class="badge bg-light text-muted border font-sm">Finishes</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 290px; position: relative;">
                        <canvas id="finishChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. TABBED DETAILED MATRICES WITH REAL-TIME SEARCH -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
        <!-- Card Header with Navigation Tabs -->
        <div class="card-header bg-white border-bottom p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <ul class="nav nav-pills gap-1" id="catalogAnalyticsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold font-sm rounded-pill px-3 py-2" id="brands-tab" data-bs-toggle="tab" data-bs-target="#brands-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-shield-halved text-danger me-1"></i> Companies ({{ $brandsMatrix->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold font-sm rounded-pill px-3 py-2 text-secondary" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-layer-group text-danger me-1"></i> Categories ({{ $categoriesMatrix->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold font-sm rounded-pill px-3 py-2 text-secondary" id="top-models-tab" data-bs-toggle="tab" data-bs-target="#top-models-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-star text-warning me-1"></i> Top Car Models
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold font-sm rounded-pill px-3 py-2 text-secondary" id="gaps-tab" data-bs-toggle="tab" data-bs-target="#gaps-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-triangle-exclamation text-danger me-1"></i> Catalog Gaps ({{ $variantsWithoutProducts }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold font-sm rounded-pill px-3 py-2 text-secondary" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-tags text-primary me-1"></i> Price Brackets
                        </button>
                    </li>
                </ul>

                <!-- Live Table Search Input -->
                <div class="position-relative" style="min-width: 240px;">
                    <input type="text" id="analyticsTableSearch" class="form-control form-control-sm ps-4 rounded-pill" placeholder="Instant filter rows...">
                    <i class="fa-solid fa-magnifying-glass position-absolute top-50 translate-middle-y text-muted" style="left: 12px; font-size: 11px;"></i>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="catalogAnalyticsTabContent">

                <!-- TAB 1: BRANDS MATRIX -->
                <div class="tab-pane fade show active" id="brands-tab-pane" role="tabpanel">
                    <div class="table-responsive table-container-scroll">
                        <table class="table table-hover table-bordered align-middle font-sm mb-0 searchable-table">
                            <thead class="table-sticky-header">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Vehicle Company (Brand)</th>
                                    <th class="text-center" style="width: 140px;">Car Models</th>
                                    <th class="text-center" style="width: 140px;">Trim Variants</th>
                                    <th class="text-center" style="width: 160px;">Products Mapped</th>
                                    <th class="text-center" style="width: 180px;">Catalog Share</th>
                                    <th class="text-center" style="width: 130px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brandsMatrix as $idx => $b)
                                    @php
                                        $totalVars = $b->carModels->sum('variants_count');
                                        $sharePct = $totalProducts > 0 ? round(($b->products_count / $totalProducts) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td class="text-muted text-center">{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="brand-avatar">
                                                    {{ strtoupper(substr($b->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-dark">{{ $b->name }}</span>
                                                    <span class="text-muted d-block" style="font-size: 11px;">slug: {{ $b->slug }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold">{{ $b->car_models_count }}</td>
                                        <td class="text-center fw-semibold text-secondary">{{ $totalVars }}</td>
                                        <td class="text-center">
                                            @if($b->products_count > 0)
                                                <span class="badge bg-danger rounded-pill px-3">{{ $b->products_count }} Products</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px; max-width: 90px;">
                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $sharePct }}%"></div>
                                                </div>
                                                <span class="small fw-semibold text-muted">{{ $sharePct }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.products.index', ['brand_id' => $b->id]) }}" class="btn btn-sm btn-outline-danger py-0 px-2 font-sm rounded-pill">
                                                <i class="fa-solid fa-filter me-1"></i> Products
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: CATEGORIES MATRIX -->
                <div class="tab-pane fade" id="categories-tab-pane" role="tabpanel">
                    <div class="table-responsive table-container-scroll">
                        <table class="table table-hover table-bordered align-middle font-sm mb-0 searchable-table">
                            <thead class="table-sticky-header">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Category Name</th>
                                    <th>Type</th>
                                    <th class="text-center" style="width: 160px;">Total Products</th>
                                    <th class="text-center" style="width: 180px;">Share</th>
                                    <th class="text-center" style="width: 130px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoriesMatrix as $idx => $cat)
                                    @php
                                        $catShare = $totalProducts > 0 ? round(($cat->products_count / $totalProducts) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td class="text-muted text-center">{{ $idx + 1 }}</td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $cat->name }}</span>
                                            <span class="text-muted d-block" style="font-size: 11px;">slug: {{ $cat->slug }}</span>
                                        </td>
                                        <td>
                                            @if($cat->parent)
                                                <span class="badge bg-light text-dark border">
                                                    Subcategory &rarr; <strong>{{ $cat->parent->name }}</strong>
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                    Main Category
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($cat->products_count > 0)
                                                <span class="badge bg-success rounded-pill px-3">{{ $cat->products_count }} Products</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">0 Products</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px; max-width: 90px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $catShare }}%"></div>
                                                </div>
                                                <span class="small fw-semibold text-muted">{{ $catShare }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.products.index', ['primary_category_id' => $cat->id]) }}" class="btn btn-sm btn-outline-danger py-0 px-2 font-sm rounded-pill">
                                                <i class="fa-solid fa-filter me-1"></i> Products
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: TOP CAR MODELS -->
                <div class="tab-pane fade" id="top-models-tab-pane" role="tabpanel">
                    <div class="table-responsive table-container-scroll">
                        <table class="table table-hover table-bordered align-middle font-sm mb-0 searchable-table">
                            <thead class="table-sticky-header">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Company</th>
                                    <th>Car Model</th>
                                    <th class="text-center" style="width: 140px;">Variants</th>
                                    <th class="text-center" style="width: 160px;">Products Mapped</th>
                                    <th class="text-center" style="width: 130px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topModels as $idx => $m)
                                    <tr>
                                        <td class="text-muted text-center">{{ $idx + 1 }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $m->brand->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $m->name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary rounded-pill">{{ $m->variants_count }} Variants</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger rounded-pill px-3">{{ $m->products_count }} Products</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.products.index', ['brand_id' => $m->brand_id, 'car_model_id' => $m->id]) }}" class="btn btn-sm btn-outline-danger py-0 px-2 font-sm rounded-pill">
                                                <i class="fa-solid fa-filter me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 4: CATALOG GAP ANALYSIS -->
                <div class="tab-pane fade" id="gaps-tab-pane" role="tabpanel">
                    <div class="alert alert-warning border-0 rounded-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-warning fs-4"></i>
                            <div>
                                <strong>Expansion Opportunity:</strong> There are <strong>{{ $variantsWithoutProducts }}</strong> vehicle variants currently without any products linked. Adding products for these trims directly boosts user search hits on the homepage!
                            </div>
                        </div>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-theme-red fw-bold text-nowrap rounded-pill px-3">
                            <i class="fa-solid fa-plus me-1"></i> Add Product
                        </a>
                    </div>
                    <div class="table-responsive table-container-scroll">
                        <table class="table table-hover table-bordered align-middle font-sm mb-0 searchable-table">
                            <thead class="table-sticky-header">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Company</th>
                                    <th>Model</th>
                                    <th>Variant / Trim Name</th>
                                    <th class="text-center" style="width: 140px;">Years</th>
                                    <th class="text-center" style="width: 150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gapVariants as $idx => $gv)
                                    <tr>
                                        <td class="text-muted text-center">{{ $idx + 1 }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $gv->carModel->brand->name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $gv->carModel->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="fw-bold text-danger">{{ $gv->name }}</span>
                                        </td>
                                        <td class="text-center text-muted small">
                                            {{ $gv->year_from ?? 'Any' }} - {{ $gv->year_to ?? 'Present' }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.products.create', ['brand_id' => $gv->carModel?->brand_id, 'car_model_id' => $gv->car_model_id, 'model_variant_id' => $gv->id]) }}" class="btn btn-sm btn-theme-red py-0 px-2 font-sm rounded-pill">
                                                <i class="fa-solid fa-plus me-1"></i> Add Product
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 5: PRICING TIERS -->
                <div class="tab-pane fade" id="pricing-tab-pane" role="tabpanel">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-3">Product Catalog by Price Brackets</h6>
                            <div class="list-group shadow-none border-0 gap-2">
                                @foreach($priceTiers as $tierLabel => $tierCount)
                                    @php
                                        $tPct = $totalProducts > 0 ? round(($tierCount / $totalProducts) * 100, 1) : 0;
                                    @endphp
                                    <div class="list-group-item rounded-3 border d-flex justify-content-between align-items-center p-3">
                                        <div class="flex-grow-1 me-3">
                                            <div class="fw-bold text-dark">{{ $tierLabel }}</div>
                                            <div class="progress mt-2 rounded-pill" style="height: 6px;">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $tPct }}%"></div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-danger rounded-pill fs-6 px-3">{{ $tierCount }}</span>
                                            <div class="text-muted small mt-1">{{ $tPct }}% share</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light border p-4 rounded-4 h-100">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-lightbulb text-warning me-1"></i> Pricing & Merchandising Strategy
                                </h6>
                                <p class="text-muted font-sm mb-3">
                                    Most emblems and vehicle logo items are positioned in the <strong>{{ array_search(max($priceTiers), $priceTiers) }}</strong> tier. Creating complete vehicle monogram packages (e.g. Front + Rear + Steering combo) is an effective way to drive higher Average Order Value (AOV).
                                </p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-theme-red rounded-3 fw-bold">
                                        <i class="fa-solid fa-list me-1"></i> Manage Products
                                    </a>
                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 fw-semibold">
                                        <i class="fa-solid fa-ticket me-1"></i> Create Coupon
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Live Instant Table Search Filter
    const searchInput = document.getElementById('analyticsTableSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const activePane = document.querySelector('.tab-content .tab-pane.active');
            if (!activePane) return;

            const rows = activePane.querySelectorAll('tbody tr');
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Reset search input when switching tabs
        document.querySelectorAll('#catalogAnalyticsTabs button').forEach(function(tabBtn) {
            tabBtn.addEventListener('shown.bs.tab', function() {
                searchInput.value = '';
                const activePane = document.querySelector('.tab-content .tab-pane.active');
                if (activePane) {
                    activePane.querySelectorAll('tbody tr').forEach(r => r.style.display = '');
                }
            });
        });
    }

    // Chart.js default typography styling
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = "#64748b";

    // 2. Top Brands Chart (Bar)
    const brandCtx = document.getElementById('brandChart');
    if (brandCtx) {
        new Chart(brandCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($brandChartLabels) !!},
                datasets: [{
                    label: 'Products',
                    data: {!! json_encode($brandChartData) !!},
                    backgroundColor: '#d90429',
                    hoverBackgroundColor: '#ef233c',
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, maxRotation: 45, minRotation: 25 }
                    }
                }
            }
        });
    }

    // 3. Categories Chart (Doughnut)
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryChartLabels) !!},
                datasets: [{
                    data: {!! json_encode($categoryChartData) !!},
                    backgroundColor: [
                        '#d90429', '#2563eb', '#10b981', '#f59e0b', 
                        '#8b5cf6', '#ec4899', '#06b6d4', '#64748b'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, padding: 12, font: { size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                cutout: '70%'
            }
        });
    }

    // 4. Placement Distribution Chart (Horizontal Bar)
    const placementCtx = document.getElementById('placementChart');
    if (placementCtx) {
        const placementLabels = {!! json_encode(array_keys($placements)) !!};
        const placementData = {!! json_encode(array_values($placements)) !!};

        new Chart(placementCtx, {
            type: 'bar',
            data: {
                labels: placementLabels,
                datasets: [{
                    label: 'Products',
                    data: placementData,
                    backgroundColor: '#2563eb',
                    hoverBackgroundColor: '#3b82f6',
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 5. Finishes Chart (Pie / Doughnut)
    const finishCtx = document.getElementById('finishChart');
    if (finishCtx) {
        const finishLabels = {!! json_encode(array_keys($finishes)) !!};
        const finishData = {!! json_encode(array_values($finishes)) !!};

        new Chart(finishCtx, {
            type: 'doughnut',
            data: {
                labels: finishLabels,
                datasets: [{
                    data: finishData,
                    backgroundColor: [
                        '#0ea5e9', '#0f172a', '#ef4444', '#f59e0b', '#8b5cf6', '#10b981', '#64748b'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, padding: 12, font: { size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                cutout: '55%'
            }
        });
    }
});
</script>
@endpush
