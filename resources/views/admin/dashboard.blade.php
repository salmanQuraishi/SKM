@extends('layouts.admin')

@section('title', 'Admin Dashboard - AutoLogos')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="container-fluid p-0">
    <!-- Dual Dashboard Switcher Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold text-uppercase d-none d-md-inline" style="font-size: 11px; letter-spacing: 1px;">Admin Views:</span>
                <ul class="nav nav-pills gap-2 p-1 bg-light rounded-3 border">
                    <li class="nav-item">
                        <a class="nav-link py-2 px-3 fw-bold font-sm active bg-danger text-white shadow-sm" href="{{ route('admin.dashboard') }}">
                            <i class="fa-solid fa-gauge-high me-1"></i> Sales & Orders Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-2 px-3 fw-bold font-sm text-secondary" href="{{ route('admin.catalog.analytics') }}">
                            <i class="fa-solid fa-chart-pie me-1 text-danger"></i> Catalog & Vehicle Analytics
                            <span class="badge bg-danger ms-1" style="font-size: 8px;">LIVE</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.catalog.analytics') }}" class="btn btn-sm btn-outline-danger fw-semibold">
                    <i class="fa-solid fa-chart-pie me-1"></i> Open Catalog Analytics
                </a>
            </div>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Total Revenue (Paid)</div>
                        <h3 class="fw-bold mb-0 text-dark">&#x20B9;{{ number_format($totalSales, 2) }}</h3>
                        <div class="text-success small mt-2 fw-semibold">
                            <i class="fa-solid fa-arrow-trend-up me-1"></i> Today: &#x20B9;{{ number_format($todaySales, 2) }}
                        </div>
                    </div>
                    <div class="rounded-3 p-3 text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #10b981, #059669); width: 56px; height: 56px;">
                        <i class="fa-solid fa-indian-rupee-sign fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Total Orders</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalOrders) }}</h3>
                        <div class="text-primary small mt-2 fw-semibold">
                            <i class="fa-solid fa-clock me-1"></i> {{ $pendingOrders }} Pending confirmation
                        </div>
                    </div>
                    <div class="rounded-3 p-3 text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); width: 56px; height: 56px;">
                        <i class="fa-solid fa-bag-shopping fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Registered Customers</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalCustomers) }}</h3>
                        <div class="text-muted small mt-2">
                            <i class="fa-solid fa-shield-halved me-1 text-success"></i> Active buyers & accounts
                        </div>
                    </div>
                    <div class="rounded-3 p-3 text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); width: 56px; height: 56px;">
                        <i class="fa-solid fa-users fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Catalog Products</div>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalProducts) }}</h3>
                        <div class="text-danger small mt-2 fw-semibold">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $lowStockProducts }} Low / Out of Stock
                        </div>
                    </div>
                    <div class="rounded-3 p-3 text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #f59e0b, #d97706); width: 56px; height: 56px;">
                        <i class="fa-solid fa-boxes-stacked fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Status Counter Pills -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-md">
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="card text-decoration-none border-0 shadow-sm bg-warning bg-opacity-10 text-warning-emphasis p-3 rounded-3 text-center">
                <div class="fs-4 fw-bold">{{ $pendingOrders }}</div>
                <div class="small fw-semibold">Pending</div>
            </a>
        </div>
        <div class="col-6 col-md">
            <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="card text-decoration-none border-0 shadow-sm bg-info bg-opacity-10 text-info-emphasis p-3 rounded-3 text-center">
                <div class="fs-4 fw-bold">{{ $processingOrders }}</div>
                <div class="small fw-semibold">Processing/Packed</div>
            </a>
        </div>
        <div class="col-6 col-md">
            <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="card text-decoration-none border-0 shadow-sm bg-primary bg-opacity-10 text-primary-emphasis p-3 rounded-3 text-center">
                <div class="fs-4 fw-bold">{{ $shippedOrders }}</div>
                <div class="small fw-semibold">Shipped/In Transit</div>
            </a>
        </div>
        <div class="col-6 col-md">
            <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="card text-decoration-none border-0 shadow-sm bg-success bg-opacity-10 text-success-emphasis p-3 rounded-3 text-center">
                <div class="fs-4 fw-bold">{{ $deliveredOrders }}</div>
                <div class="small fw-semibold">Delivered</div>
            </a>
        </div>
        <div class="col-6 col-md">
            <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="card text-decoration-none border-0 shadow-sm bg-danger bg-opacity-10 text-danger p-3 rounded-3 text-center">
                <div class="fs-4 fw-bold">{{ $cancelledOrders }}</div>
                <div class="small fw-semibold">Cancelled</div>
            </a>
        </div>
    </div>

    <!-- Chart & Top Products Row -->
    <div class="row g-3 mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-chart-area text-danger me-2"></i> Revenue & Order Trend (Last 6 Months)</h6>
                    <span class="badge bg-light text-muted border">Monthly Insights</span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 280px; position: relative;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-trophy text-warning me-2"></i> Top Selling Emblems</h6>
                    <a href="{{ route('admin.products.index') }}" class="small text-decoration-none text-danger fw-semibold">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($topProducts as $tp)
                            <div class="list-group-item d-flex align-items-center gap-3 py-3 px-3">
                                <div class="bg-light rounded p-1 border text-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                    <img src="{{ asset($tp->primary_image_url) }}" alt="{{ $tp->name }}" class="img-fluid h-100 object-fit-contain">
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <a href="{{ route('admin.products.edit', $tp->id) }}" class="text-dark fw-bold text-decoration-none text-truncate d-block small mb-1">
                                        {{ $tp->name }}
                                    </a>
                                    <div class="text-muted" style="font-size: 11px;">
                                        SKU: {{ $tp->sku }} &bull; Stock: <span class="{{ $tp->stock_quantity <= 5 ? 'text-danger fw-bold' : 'text-success' }}">{{ $tp->stock_quantity }}</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger rounded-pill">{{ $tp->total_sold ?? 0 }} sold</span>
                                    <div class="small fw-bold text-dark mt-1">&#x20B9;{{ number_format($tp->final_price) }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted p-4">
                                <i class="fa-solid fa-box-open fs-3 mb-2 d-block"></i> No sales recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-list-check text-danger me-2"></i> Recent Orders</h6>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-danger fw-semibold">
                View All Orders <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Order Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-danger text-decoration-none">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $order->customer_name }}</div>
                                <small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>
                            <td class="text-muted small">
                                {{ $order->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $order->items_count ?? $order->items->count() }} items</span>
                            </td>
                            <td class="fw-bold text-dark">
                                &#x20B9;{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td>
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-circle-check me-1"></i> Paid ({{ strtoupper($order->payment_method) }})</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="fa-solid fa-clock me-1"></i> {{ strtoupper($order->payment_status) }} ({{ strtoupper($order->payment_method) }})</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                        'confirmed' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                                        'processing' => 'bg-primary-subtle text-primary-emphasis border-primary-subtle',
                                        'packed' => 'bg-secondary-subtle text-secondary-emphasis border-secondary-subtle',
                                        'shipped' => 'bg-primary text-white',
                                        'out_for_delivery' => 'bg-info text-white',
                                        'delivered' => 'bg-success text-white',
                                        'cancelled' => 'bg-danger text-white',
                                    ];
                                    $badgeClass = $statusColors[$order->order_status] ?? 'bg-light text-dark border';
                                @endphp
                                <span class="badge {{ $badgeClass }} text-uppercase" style="font-size: 11px;">
                                    {{ str_replace('_', ' ', $order->order_status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-light border" title="View Order Details">
                                    <i class="fa-solid fa-eye text-primary"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No orders received yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const labels = @json($chartLabels);
        const revenueData = @json($chartRevenue);
        const ordersData = @json($chartOrders);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue (&#x20B9;)',
                        data: revenueData,
                        borderColor: '#d90429',
                        backgroundColor: 'rgba(217, 4, 41, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Orders Count',
                        data: ordersData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.35,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return '&#x20B9;' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    });
</script>
@endpush
