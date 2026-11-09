@extends('layouts.admin')

@section('title', 'Payment Report - AutoLogos')
@section('page_title', 'Payment Gateway & Transactions Ledger')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Navigation Switcher -->
    @include('admin.reports.partials.report_nav')

    <!-- Header & Action -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-credit-card text-danger me-2"></i> Payment & Settlements Report</h4>
            <p class="text-muted small mb-0">Razorpay online transactions, COD courier settlements, gateway transaction IDs, and settlement ledger.</p>
        </div>
        <a href="{{ route('admin.reports.payment.export', ['preset' => $preset, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-success fw-bold shadow-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Export Payments CSV
        </a>
    </div>

    <!-- Date Filter Form -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.reports.payment') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Period Preset</label>
                    <select name="preset" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="this_month" {{ $preset == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $preset == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ $preset == 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="today" {{ $preset == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $preset == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="custom" {{ $preset == 'custom' ? 'selected' : '' }}>Custom Dates</option>
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
                        <i class="fa-solid fa-filter me-1"></i> Filter Ledger
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total Realized Revenue</div>
                <h3 class="fw-bold text-success mb-0 mt-1">&#x20B9;{{ number_format($totalCollected, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-circle-check text-success me-1"></i> Successfully collected into accounts
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Razorpay Online Collected</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">&#x20B9;{{ number_format($razorpayCollected, 2) }}</h3>
                <div class="small text-primary mt-2">
                    <i class="fa-solid fa-bolt me-1"></i> {{ $razorpayPaidCount }} Captured online transactions
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">COD Collected (Remitted)</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">&#x20B9;{{ number_format($codCollected, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-truck-fast text-dark me-1"></i> {{ $codDeliveredCount }} Delivered parcels remitted
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Pending Collections</div>
                <h3 class="fw-bold text-warning mb-0 mt-1">&#x20B9;{{ number_format($pendingAmount, 2) }}</h3>
                <div class="small text-warning mt-2">
                    <i class="fa-solid fa-clock me-1"></i> Orders awaiting delivery / payment
                </div>
            </div>
        </div>
    </div>

    <!-- Gateway Performance Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-shield-halved text-primary me-2"></i> Razorpay Online Gateway
                        </h6>
                        <small class="text-muted">UPI, Debit/Credit Cards, Netbanking</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1">Instant</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2">
                    <span class="text-muted small">Total Attempted:</span>
                    <span class="fw-bold text-dark">{{ $razorpayOrdersCount }} orders</span>
                </div>
                <div class="d-flex justify-content-between pt-1">
                    <span class="text-muted small">Successful Settlements:</span>
                    <span class="fw-bold text-success">{{ $razorpayPaidCount }} orders (&#x20B9;{{ number_format($razorpayCollected, 2) }})</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-hand-holding-dollar text-warning-emphasis me-2"></i> Cash on Delivery (COD)
                        </h6>
                        <small class="text-muted">Courier cash collection upon doorstep delivery</small>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold px-3 py-1">Doorstep</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2">
                    <span class="text-muted small">Total COD Orders:</span>
                    <span class="fw-bold text-dark">{{ $codOrdersCount }} orders</span>
                </div>
                <div class="d-flex justify-content-between pt-1">
                    <span class="text-muted small">Awaiting Courier Remittance:</span>
                    <span class="fw-bold text-warning">&#x20B9;{{ number_format($codPendingCollection, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Ledger Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-table-list text-danger me-2"></i> Payment Transactions Ledger ({{ $orders->total() }} Records)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Date & Time</th>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Gateway</th>
                        <th>Transaction ID / Reference</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        @php
                            $payment = $o->payments->first();
                            $txnId = $payment->razorpay_payment_id ?? ($payment->transaction_id ?? null);
                        @endphp
                        <tr>
                            <td class="ps-3 small text-muted">{{ $o->created_at->format('d M Y, h:i A') }}</td>
                            <td class="fw-bold">
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="text-decoration-none text-danger">
                                    #{{ $o->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $o->customer_name }}</div>
                                <small class="text-muted">{{ $o->customer_phone }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $o->payment_method === 'razorpay' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ strtoupper($o->payment_method) }}
                                </span>
                            </td>
                            <td>
                                @if($txnId)
                                    <code class="text-dark">{{ $txnId }}</code>
                                @else
                                    <span class="text-muted small">COD / Not Generated</span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">&#x20B9;{{ number_format($o->total_amount, 2) }}</td>
                            <td>
                                @if($o->payment_status === 'paid')
                                    <span class="badge bg-success">PAID / CAPTURED</span>
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
                                    View Order
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No payment transaction records found for this period.</td>
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
