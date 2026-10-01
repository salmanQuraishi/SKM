@extends('layouts.admin')

@section('title', 'Customer - ' . $customer->name)
@section('page_title', 'Customer Profile: ' . $customer->name)

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
            <p class="text-muted small mb-0">Customer ID: #{{ $customer->id }} &bull; Member since {{ $customer->created_at->format('M Y') }}</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4">
        <!-- Customer Info Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-danger-subtle text-danger fw-bold d-inline-flex align-items-center justify-content-center fs-2 mb-3" style="width: 80px; height: 80px;">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $customer->name }}</h5>
                    <p class="text-muted small mb-3">{{ $customer->email }}</p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @if($customer->is_active)
                            <span class="badge bg-success-subtle text-success">Active Account</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">Suspended</span>
                        @endif
                    </div>

                    <div class="border-top pt-3 text-start small">
                        <div class="mb-2"><strong>Phone:</strong> {{ $customer->phone ?: 'Not provided' }}</div>
                        <div class="mb-2"><strong>Total Orders:</strong> {{ $customer->orders->count() }}</div>
                        <div class="mb-0"><strong>Lifetime Paid:</strong> &#x20B9;{{ number_format($totalSpent, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Saved Addresses -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-location-dot text-danger me-2"></i> Saved Addresses ({{ $customer->addresses->count() }})</h6>
                </div>
                <div class="card-body p-3">
                    @forelse($customer->addresses as $addr)
                        <div class="border rounded p-3 mb-2 small bg-light">
                            <div class="fw-bold text-dark mb-1">{{ $addr->full_name }} ({{ $addr->phone }})</div>
                            <div>{{ $addr->address_line1 }}</div>
                            @if($addr->address_line2) <div>{{ $addr->address_line2 }}</div> @endif
                            <div>{{ $addr->city }}, {{ $addr->state }} - <strong>{{ $addr->pincode }}</strong></div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No saved addresses on file.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Orders History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock-rotate-left text-danger me-2"></i> Order History</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders as $ord)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('admin.orders.show', $ord->id) }}" class="fw-bold text-danger text-decoration-none">
                                            #{{ $ord->order_number }}
                                        </a>
                                    </td>
                                    <td class="small text-muted">{{ $ord->created_at->format('d M Y') }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $ord->items->count() }} items</span></td>
                                    <td class="fw-bold text-dark">&#x20B9;{{ number_format($ord->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ord->payment_status === 'paid' ? 'success' : 'warning' }}-subtle text-{{ $ord->payment_status === 'paid' ? 'success' : 'warning' }}">
                                            {{ strtoupper($ord->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-uppercase" style="font-size: 11px;">
                                            {{ str_replace('_', ' ', $ord->order_status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-sm btn-light border text-primary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No orders placed by this customer yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
