@extends('layouts.admin')

@section('title', 'Tax Report (GST) - AutoLogos')
@section('page_title', 'GST & Tax Compliance Report')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Navigation Switcher -->
    @include('admin.reports.partials.report_nav')

    <!-- Header & Action -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-receipt text-danger me-2"></i> Tax & GST Audit Report</h4>
            <p class="text-muted small mb-0">GST calculations, HSN/SAC code classifications, CGST/SGST splits, and invoice-level tax registers.</p>
        </div>
        <a href="{{ route('admin.reports.tax.export', ['preset' => $preset, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="btn btn-success fw-bold shadow-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Export GST CSV
        </a>
    </div>

    <!-- Date Filter Form -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.reports.tax') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Tax Period</label>
                    <select name="preset" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="this_month" {{ $preset == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $preset == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ $preset == 'this_year' ? 'selected' : '' }}>This Financial Year</option>
                        <option value="today" {{ $preset == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="custom" {{ $preset == 'custom' ? 'selected' : '' }}>Custom Period</option>
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
                        <i class="fa-solid fa-filter me-1"></i> Generate GST Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tax KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Total GST Collected</div>
                <h3 class="fw-bold text-success mb-0 mt-1">&#x20B9;{{ number_format($totalTaxCollected, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Across {{ $totalPaidOrders }} paid tax invoices
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">Taxable Turnover</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">&#x20B9;{{ number_format($totalTaxableSubtotal, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-calculator me-1"></i> Net assessable product value
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">CGST (Central Tax)</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">&#x20B9;{{ number_format($cgstTotal, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-building-columns me-1"></i> 50% central tax allocation
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="text-muted small text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">SGST (State Tax)</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">&#x20B9;{{ number_format($sgstTotal, 2) }}</h3>
                <div class="small text-muted mt-2">
                    <i class="fa-solid fa-landmark me-1"></i> 50% state tax allocation
                </div>
            </div>
        </div>
    </div>

    <!-- HSN / SAC Summary Table -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-barcode text-danger me-2"></i> HSN / SAC Code Summary Breakdown
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">HSN / SAC</th>
                        <th>Classification Description</th>
                        <th class="text-center">Units Sold</th>
                        <th class="text-end">Taxable Amount</th>
                        <th class="text-center">GST Rate</th>
                        <th class="text-end">CGST</th>
                        <th class="text-end">SGST</th>
                        <th class="text-end pe-3">Total Tax</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hsnSummary as $hsn)
                        @php
                            $rate = $hsn->avg_tax_rate ?: 18.0;
                            $tax = ($hsn->taxable_amount * $rate) / 100;
                            $half = $tax / 2;
                        @endphp
                        <tr>
                            <td class="ps-3 fw-bold">
                                <span class="badge bg-dark px-2 py-1 fs-6 font-monospace">{{ $hsn->hsn_code }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">Parts & Accessories of Motor Vehicles (Emblems / Badges)</span>
                            </td>
                            <td class="text-center fw-bold">{{ $hsn->total_units }}</td>
                            <td class="text-end fw-bold text-dark">&#x20B9;{{ number_format($hsn->taxable_amount, 2) }}</td>
                            <td class="text-center"><span class="badge bg-info text-dark">{{ number_format($rate, 1) }}%</span></td>
                            <td class="text-end">&#x20B9;{{ number_format($half, 2) }}</td>
                            <td class="text-end">&#x20B9;{{ number_format($half, 2) }}</td>
                            <td class="text-end pe-3 fw-bold text-success">&#x20B9;{{ number_format($tax, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="ps-3 fw-bold"><span class="badge bg-dark px-2 py-1 fs-6 font-monospace">8708</span></td>
                            <td>Parts & Accessories of Motor Vehicles (Car Emblems & Monograms)</td>
                            <td class="text-center">0</td>
                            <td class="text-end">&#x20B9;0.00</td>
                            <td class="text-center"><span class="badge bg-info text-dark">18.0%</span></td>
                            <td class="text-end">&#x20B9;0.00</td>
                            <td class="text-end">&#x20B9;0.00</td>
                            <td class="text-end pe-3 fw-bold text-success">&#x20B9;0.00</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Invoice-Level Tax Register Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-file-invoice text-danger me-2"></i> Tax Invoices Register ({{ $orders->total() }} Records)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Receipt / Invoice #</th>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th class="text-end">Taxable Amount</th>
                        <th class="text-end">CGST (9%)</th>
                        <th class="text-end">SGST (9%)</th>
                        <th class="text-end">Total Tax (18%)</th>
                        <th class="text-end pe-3">Invoice Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        @php
                            $tax = (float) $o->tax_amount;
                            $half = $tax / 2;
                        @endphp
                        <tr>
                            <td class="ps-3 fw-bold">
                                <span class="badge bg-light text-dark border font-monospace">
                                    {{ $o->receipt->receipt_number ?? ('REC-' . $o->id) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="text-decoration-none text-danger fw-semibold">
                                    #{{ $o->order_number }}
                                </a>
                            </td>
                            <td class="small text-muted">{{ $o->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $o->customer_name }}</div>
                                <small class="text-muted">{{ $o->shipping_state ?? 'Delhi NCR' }}</small>
                            </td>
                            <td class="text-end">&#x20B9;{{ number_format($o->subtotal, 2) }}</td>
                            <td class="text-end text-muted">&#x20B9;{{ number_format($half, 2) }}</td>
                            <td class="text-end text-muted">&#x20B9;{{ number_format($half, 2) }}</td>
                            <td class="text-end fw-bold text-success">&#x20B9;{{ number_format($tax, 2) }}</td>
                            <td class="text-end pe-3 fw-bold text-dark">&#x20B9;{{ number_format($o->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">No paid invoices recorded for this tax period.</td>
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
