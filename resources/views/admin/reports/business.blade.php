@extends('layouts.admin')

@section('title', 'Business Report - AutoLogos')
@section('page_title', 'Business & Revenue Analytics')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Navigation Switcher -->
    @include('admin.reports.partials.report_nav')

    <!-- Header & Action -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-briefcase text-danger me-2"></i> Business Performance Report</h4>
            <p class="text-muted small mb-0">Sales turnover, average order value, top-selling vehicle products, and customer order breakdowns.</p>
        </div>
        <a href="{{ route('admin.reports.business.export', ['preset' => $preset, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-success fw-bold shadow-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Export Business CSV
        </a>
    </div>

    <!-- Date Filter Form -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.reports.business') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Timeframe Preset</label>
                    <select name="preset" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="today" {{ $preset == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $preset == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ $preset == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ $preset == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $preset == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ $preset == 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $preset == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <input type="hidden" name="preset" value="custom">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">
                        <i class="fa-solid fa-filter me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Paid Net Revenue</div>
                <h3 class="fw-bold text-success mb-0 mt-1">&#x20B9;{{ number_format($paidRevenue, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-circle-check text-success me-1"></i> {{ $paidOrdersCount }} Paid orders
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Gross Order Value</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">&#x20B9;{{ number_format($grossSales, 2) }}</h3>
                <div class="small text-primary mt-2">
                    <i class="fa-solid fa-cart-shopping me-1"></i> {{ $totalOrdersCount }} Total Orders Placed
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Average Order Value (AOV)</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">&#x20B9;{{ number_format($aov, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-arrow-trend-up text-success me-1"></i> Per paid customer order
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Pending / Uncollected</div>
                <h3 class="fw-bold text-warning mb-0 mt-1">&#x20B9;{{ number_format($pendingRevenue, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-clock text-warning me-1"></i> COD & unpaid online orders
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Bar -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center">
                <div class="small text-muted fw-bold">Online (Razorpay)</div>
                <h5 class="fw-bold text-primary mb-0 mt-1">&#x20B9;{{ number_format($onlineRevenue, 2) }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center">
                <div class="small text-muted fw-bold">Cash on Delivery (COD)</div>
                <h5 class="fw-bold text-dark mb-0 mt-1">&#x20B9;{{ number_format($codRevenue, 2) }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center">
                <div class="small text-muted fw-bold">Total Discounts Given</div>
                <h5 class="fw-bold text-danger mb-0 mt-1">&#x20B9;{{ number_format($totalDiscount, 2) }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 text-center">
                <div class="small text-muted fw-bold">Shipping Collected</div>
                <h5 class="fw-bold text-secondary mb-0 mt-1">&#x20B9;{{ number_format($totalShipping, 2) }}</h5>
            </div>
        </div>
    </div>

    <!-- Top Selling Products Table -->
    @if($topProducts->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-fire text-danger me-2"></i> Top Selling Vehicle Emblems & Accessories (Selected Period)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th class="text-center">Units Sold</th>
                            <th class="text-end pe-3">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $idx => $tp)
                            <tr>
                                <td class="ps-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $tp->product_name }}</span>
                                </td>
                                <td><code>{{ $tp->product_sku ?: 'N/A' }}</code></td>
                                <td class="text-center">
                                    <span class="badge bg-primary px-3 py-1">{{ $tp->total_qty }} units</span>
                                </td>
                                <td class="text-end pe-3 fw-bold text-success">
                                    &#x20B9;{{ number_format($tp->total_sales, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Orders Audit Log Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-list-check text-danger me-2"></i> Orders Transaction Log ({{ $orders->total() }} Total)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Order Number</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Payment Mode</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        <tr>
                            <td class="ps-3 fw-bold">
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="text-decoration-none text-danger">
                                    #{{ $o->order_number }}
                                </a>
                            </td>
                            <td class="small text-muted">{{ $o->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $o->customer_name }}</div>
                                <small class="text-muted">{{ $o->customer_phone }}</small>
                            </td>
                            <td>{{ $o->items->sum('quantity') }} items</td>
                            <td class="fw-bold">&#x20B9;{{ number_format($o->total_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $o->payment_method === 'razorpay' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ strtoupper($o->payment_method) }}
                                </span>
                            </td>
                            <td>
                                @if($o->payment_status === 'paid')
                                    <span class="badge bg-success">PAID</span>
                                @elseif($o->payment_status === 'failed')
                                    <span class="badge bg-danger">FAILED</span>
                                @else
                                    <span class="badge bg-warning text-dark">PENDING</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ ucfirst($o->order_status) }}</span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="btn btn-sm btn-outline-dark">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No orders found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
