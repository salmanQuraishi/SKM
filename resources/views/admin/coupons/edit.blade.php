@extends('layouts.admin')

@section('title', 'Edit Coupon - ' . $coupon->code)
@section('page_title', 'Edit Coupon: ' . $coupon->code)

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Edit Coupon</h5>
            <p class="text-muted small mb-0">Update coupon discount rules and limitations.</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase font-monospace fw-bold" value="{{ old('code', $coupon->code) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Discount Type <span class="text-danger">*</span></label>
                        <select name="discount_type" class="form-select" required>
                            <option value="percentage" {{ old('discount_type', $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Flat Fixed Amount (&#x20B9;)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Discount Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $coupon->discount_value) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Min Cart Total (&#x20B9;)</label>
                        <input type="number" step="0.01" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $coupon->min_order_amount) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Max Discount Cap (&#x20B9;)</label>
                        <input type="number" step="0.01" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Expiry Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $coupon->end_date ? $coupon->end_date->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Total Usage Limit</label>
                        <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Per Customer Limit</label>
                        <input type="number" name="per_customer_limit" class="form-control" value="{{ old('per_customer_limit', $coupon->per_customer_limit) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $coupon->description) }}</textarea>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" {{ old('status', $coupon->status) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="statusSwitch">Coupon Active</label>
                </div>

                <button type="submit" class="btn btn-theme-red px-4 py-2 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
