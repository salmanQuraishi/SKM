@extends('layouts.admin')

@section('title', 'Orders & Shipments - AutoLogos')
@section('page_title', 'Orders Management')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Customer Orders</h5>
            <p class="text-muted small mb-0">Track order statuses, payments, shipping AWB numbers, and generate tax invoices.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="d-flex gap-2 flex-wrap mb-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-danger' : 'btn-white border text-dark' }}">
            All <span class="badge bg-secondary ms-1">{{ $statusCounts['all'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') == 'pending' ? 'btn-danger' : 'btn-white border text-dark' }}">
            Pending <span class="badge bg-warning text-dark ms-1">{{ $statusCounts['pending'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="btn btn-sm {{ request('status') == 'confirmed' ? 'btn-danger' : 'btn-white border text-dark' }}">
            Confirmed <span class="badge bg-info text-dark ms-1">{{ $statusCounts['confirmed'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="btn btn-sm {{ request('status') == 'processing' ? 'btn-danger' : 'btn-white border text-dark' }}">
            Processing <span class="badge bg-primary ms-1">{{ $statusCounts['processing'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'packed']) }}" class="btn btn-sm {{ request('status') == 'packed' ? 'btn-danger' : 'btn-white border text-dark' }}">
            Packed <span class="badge bg-secondary ms-1">{{ $statusCounts['packed'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="btn btn-sm {{ request('status') == 'shipped' ? 'btn-danger' : 'btn-white border text-dark' }}">
            Shipped <span class="badge bg-primary ms-1">{{ $statusCounts['shipped'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="btn btn-sm {{ request('status') == 'delivered' ? 'btn-danger' : 'btn-white border text-dark' }}">
            Delivered <span class="badge bg-success ms-1">{{ $statusCounts['delivered'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="btn btn-sm {{ request('status') == 'cancelled' ? 'btn-danger' : 'btn-white border text-dark' }}">
            Cancelled <span class="badge bg-danger ms-1">{{ $statusCounts['cancelled'] }}</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="col-md-3">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search Order #, customer, phone, AWB...">
                </div>
                <div class="col-md-2">
                    <select name="payment_status" class="form-select">
                        <option value="">All Payment Status</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                        <option value="razorpay" {{ request('payment_method') == 'razorpay' ? 'selected' : '' }}>Razorpay / UPI</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" title="Date From">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" title="Date To">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-dark w-100"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['q', 'payment_status', 'payment_method', 'date_from', 'date_to']))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Order Number</th>
                        <th>Date & Time</th>
                        <th>Customer Details</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Order Status</th>
                        <th>AWB / Courier</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="fw-bold text-danger text-decoration-none">
                                    #{{ $o->order_number }}
                                </a>
                            </td>
                            <td class="text-muted small">
                                {{ $o->created_at->format('d M Y') }}<br>
                                <span class="text-secondary" style="font-size: 11px;">{{ $o->created_at->format('h:i A') }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $o->customer_name }}</div>
                                <div class="text-muted small">{{ $o->customer_phone }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $o->items->sum('quantity') }} items</span>
                            </td>
                            <td class="fw-bold text-dark">
                                &#x20B9;{{ number_format($o->total_amount, 2) }}
                            </td>
                            <td>
                                @if($o->payment_status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-circle-check me-1"></i> Paid ({{ strtoupper($o->payment_method) }})</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="fa-solid fa-clock me-1"></i> {{ strtoupper($o->payment_status) }} ({{ strtoupper($o->payment_method) }})</span>
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
                                    $badgeClass = $statusColors[$o->order_status] ?? 'bg-light text-dark border';
                                @endphp
                                <span class="badge {{ $badgeClass }} text-uppercase" style="font-size: 11px;">
                                    {{ str_replace('_', ' ', $o->order_status) }}
                                </span>
                            </td>
                            <td>
                                @if($o->tracking_number)
                                    <div class="small fw-semibold text-dark">{{ $o->courier_name }}</div>
                                    <code class="small">{{ $o->tracking_number }}</code>
                                @else
                                    <span class="text-muted small">— Not Assigned</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="btn btn-light border text-primary" title="View & Process Order">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.invoice.download', $o->id) }}" class="btn btn-light border text-danger" title="Download Tax Invoice PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.shipping.download', $o->id) }}" class="btn btn-light border text-secondary" title="Download Shipping Label">
                                        <i class="fa-solid fa-barcode"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-cart-shopping fs-2 mb-2 d-block text-secondary"></i>
                                No customer orders found for the specified criteria.
                            </td>
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
