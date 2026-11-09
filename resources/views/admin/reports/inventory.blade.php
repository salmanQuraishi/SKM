@extends('layouts.admin')

@section('title', 'Inventory Report - AutoLogos')
@section('page_title', 'Stock & Inventory Valuation')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Navigation Switcher -->
    @include('admin.reports.partials.report_nav')

    <!-- Header & Action -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-boxes-stacked text-danger me-2"></i> Inventory & Stock Report</h4>
            <p class="text-muted small mb-0">Total stock quantities, warehouse cost vs retail valuation, low stock warnings, and HSN-tagged catalog items.</p>
        </div>
        <a href="{{ route('admin.reports.inventory.export', ['status' => $status, 'brand_id' => $brandId, 'category_id' => $categoryId, 'q' => $search]) }}" class="btn btn-success fw-bold shadow-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Export Inventory CSV
        </a>
    </div>

    <!-- Inventory Valuation KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Stock Units</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalStockUnits) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-cube text-primary me-1"></i> Across {{ number_format($totalProducts) }} unique catalog items
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Retail Stock Valuation</div>
                <h3 class="fw-bold text-success mb-0 mt-1">&#x20B9;{{ number_format($totalRetailValuation, 2) }}</h3>
                <div class="small text-success mt-2">
                    <i class="fa-solid fa-tag me-1"></i> Based on active selling MRP
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Inventory Cost Valuation</div>
                <h3 class="fw-bold text-secondary mb-0 mt-1">&#x20B9;{{ number_format($totalCostValuation, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-coins me-1"></i> Procurement investment
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Potential Gross Margin</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">&#x20B9;{{ number_format($potentialMargin, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-chart-line text-success me-1"></i> Retail vs Cost delta
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Status Quick Filter Pills -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <div class="text-success small fw-bold text-uppercase">In Stock (Healthy)</div>
                    <h4 class="fw-bold text-success mb-0 mt-1">{{ number_format($inStockCount) }} Products</h4>
                </div>
                <div class="bg-success-subtle p-3 rounded-circle text-success fs-5">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <div class="text-warning small fw-bold text-uppercase">Low Stock Alert (&le; 5 units)</div>
                    <h4 class="fw-bold text-warning mb-0 mt-1">{{ number_format($lowStockCount) }} Products</h4>
                </div>
                <div class="bg-warning-subtle p-3 rounded-circle text-warning fs-5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <div class="text-danger small fw-bold text-uppercase">Out of Stock (Zero Units)</div>
                    <h4 class="fw-bold text-danger mb-0 mt-1">{{ number_format($outOfStockCount) }} Products</h4>
                </div>
                <div class="bg-danger-subtle p-3 rounded-circle text-danger fs-5">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.reports.inventory') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Stock Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Stock Levels</option>
                        <option value="in_stock" {{ $status == 'in_stock' ? 'selected' : '' }}>In Stock Only (> 5)</option>
                        <option value="low_stock" {{ $status == 'low_stock' ? 'selected' : '' }}>Low Stock Alert (1-5)</option>
                        <option value="out_of_stock" {{ $status == 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Company / Brand</label>
                    <select name="brand_id" class="form-select form-select-sm">
                        <option value="">All Companies ({{ $brands->count() }})</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ $brandId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Category</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All Categories ({{ $categories->count() }})</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search SKU, HSN, Name...">
                        <button type="submit" class="btn btn-sm btn-dark fw-bold px-3">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Inventory Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-warehouse text-danger me-2"></i> Inventory Ledger (Showing {{ $products->total() }} Products)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Product Name</th>
                        <th>SKU</th>
                        <th>HSN Code</th>
                        <th>Brand & Category</th>
                        <th>Cost Price</th>
                        <th>Selling Price</th>
                        <th class="text-center">Stock Level</th>
                        <th>Stock Valuation</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        @php
                            $cost = $p->cost_price ?: ($p->regular_price * 0.6);
                            $price = $p->sale_price ?: $p->regular_price;
                            $valuation = $p->stock_quantity * $price;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('admin.products.edit', $p->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                    {{ $p->name }}
                                </a>
                            </td>
                            <td><code>{{ $p->sku }}</code></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $p->hsn_sac ?: '8708' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small">{{ $p->brand->name ?? 'Universal' }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $p->primaryCategory->name ?? 'General' }}</div>
                            </td>
                            <td>&#x20B9;{{ number_format($p->cost_price ?? 0, 2) }}</td>
                            <td class="fw-bold text-dark">&#x20B9;{{ number_format($price, 2) }}</td>
                            <td class="text-center">
                                @if($p->stock_quantity <= 0)
                                    <span class="badge bg-danger">OUT OF STOCK (0)</span>
                                @elseif($p->stock_quantity <= ($p->min_stock_alert ?: 5))
                                    <span class="badge bg-warning text-dark">LOW STOCK ({{ $p->stock_quantity }})</span>
                                @else
                                    <span class="badge bg-success">{{ $p->stock_quantity }} units</span>
                                @endif
                            </td>
                            <td class="fw-bold text-success">&#x20B9;{{ number_format($valuation, 2) }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Stock
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No inventory records match your criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
