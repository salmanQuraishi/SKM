@extends('layouts.admin')

@section('title', 'Coupons - AutoLogos')
@section('page_title', 'Discount Coupons & Promo Codes')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Coupon Codes</h5>
            <p class="text-muted small mb-0">Create percentage or flat discount vouchers for checkout.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-theme-red px-3">
            <i class="fa-solid fa-plus me-1"></i> Add Coupon
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Coupon Code</th>
                        <th>Discount</th>
                        <th>Min Order / Max Discount</th>
                        <th>Validity</th>
                        <th>Redemptions</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $cp)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 font-monospace">{{ $cp->code }}</span>
                                @if($cp->description)
                                    <div class="small text-muted mt-1">{{ $cp->description }}</div>
                                @endif
                            </td>
                            <td>
                                @if($cp->discount_type === 'percentage')
                                    <strong class="text-success">{{ $cp->discount_value }}% OFF</strong>
                                @else
                                    <strong class="text-success">&#x20B9;{{ number_format($cp->discount_value, 2) }} FLAT OFF</strong>
                                @endif
                            </td>
                            <td class="small">
                                <div>Min: &#x20B9;{{ number_format($cp->min_order_amount ?? 0, 2) }}</div>
                                @if($cp->max_discount_amount)
                                    <div class="text-muted">Max Cap: &#x20B9;{{ number_format($cp->max_discount_amount, 2) }}</div>
                                @endif
                            </td>
                            <td class="small text-muted">
                                @if($cp->start_date && $cp->end_date)
                                    {{ $cp->start_date->format('d M Y') }} to {{ $cp->end_date->format('d M Y') }}
                                @else
                                    Always Active
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $cp->usages_count }} used</span>
                                @if($cp->usage_limit)
                                    <small class="text-muted d-block mt-1">Limit: {{ $cp->usage_limit }}</small>
                                @endif
                            </td>
                            <td>
                                @if($cp->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">Disabled</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.coupons.edit', $cp->id) }}" class="btn btn-light border text-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $cp->id) }}" method="POST" class="d-inline confirm-action" data-confirm="Are you sure you want to delete coupon '{{ $cp->code }}'?" data-confirm-title="Delete Coupon?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No discount coupons created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $coupons->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
