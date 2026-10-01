@extends('layouts.admin')

@section('title', 'Sales Reports - AutoLogos')
@section('page_title', 'Revenue & Sales Analytics')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Financial & Sales Reports</h5>
            <p class="text-muted small mb-0">Aggregate revenue figures, online vs COD breakdowns, and export transactions to CSV.</p>
        </div>
        <a href="{{ route('admin.reports.sales.export', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-outline-success">
            <i class="fa-solid fa-file-excel me-1"></i> Export to CSV
        </a>
    </div>

    <!-- Date Range Picker Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Start Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">End Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark w-100"><i class="fa-solid fa-chart-pie me-1"></i> Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="text-muted small text-uppercase fw-bold">Period Paid Revenue</div>
                <h3 class="fw-bold text-success mb-0 mt-1">&#x20B9;{{ number_format($totalRevenue, 2) }}</h3>
                <small class="text-muted">{{ $totalOrdersCount }} Total Orders</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="text-muted small text-uppercase fw-bold">Online Prepaid (Razorpay)</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">&#x20B9;{{ number_format($onlineRevenue, 2) }}</h3>
                <small class="text-muted">Instant settlements</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="text-muted small text-uppercase fw-bold">Cash on Delivery (COD)</div>
                <h3 class="fw-bold text-warning-emphasis mb-0 mt-1">&#x20B9;{{ number_format($codRevenue, 2) }}</h3>
                <small class="text-muted">Courier remittance</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="text-muted small text-uppercase fw-bold">Avg Order Value (AOV)</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">
                    &#x20B9;{{ $totalOrdersCount > 0 ? number_format($totalRevenue / $totalOrdersCount, 2) : '0.00' }}
                </h3>
                <small class="text-muted">Per customer transaction</small>
            </div>
        </div>
    </div>

    <!-- Orders in Period Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt text-danger me-2"></i> Transactions within selected dates</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Order #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Tax (GST)</th>
                        <th>Shipping & COD</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $ord)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="fw-bold text-danger text-decoration-none">
                                    #{{ $ord->order_number }}
                                </a>
                            </td>
                            <td class="small text-muted">{{ $ord->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $ord->customer_name }}</div>
                                <small class="text-muted">{{ $ord->customer_phone }}</small>
                            </td>
                            <td>&#x20B9;{{ number_format($ord->subtotal, 2) }}</td>
                            <td class="text-success">- &#x20B9;{{ number_format($ord->discount_amount, 2) }}</td>
                            <td>&#x20B9;{{ number_format($ord->tax_amount, 2) }}</td>
                            <td>&#x20B9;{{ number_format($ord->shipping_amount + $ord->cod_charge, 2) }}</td>
                            <td class="fw-bold text-dark">&#x20B9;{{ number_format($ord->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $ord->payment_status === 'paid' ? 'success' : 'warning' }}-subtle text-{{ $ord->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ strtoupper($ord->payment_status) }} ({{ strtoupper($ord->payment_method) }})
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No orders found within this date range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
