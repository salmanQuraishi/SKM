@extends('layouts.admin')

@section('title', 'Edit Brand - ' . $brand->name)
@section('page_title', 'Edit Brand: ' . $brand->name)

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Edit Car Brand</h5>
            <p class="text-muted small mb-0">Update brand logo, banner and details.</p>
        </div>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $brand->name) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Brand Logo</label>
                        @if($brand->logo)
                            <div class="mb-2">
                                <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" class="img-thumbnail" style="max-height: 60px;">
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Brand Banner Image</label>
                        @if($brand->banner)
                            <div class="mb-2">
                                <img src="{{ asset($brand->banner) }}" alt="{{ $brand->name }}" class="img-thumbnail" style="max-height: 60px;">
                            </div>
                        @endif
                        <input type="file" name="banner" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $brand->description) }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $brand->sort_order) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" {{ old('status', $brand->status) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="statusSwitch">Active Status</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="featSwitch" value="1" {{ old('is_featured', $brand->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="featSwitch">Featured Brand</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-theme-red px-4 py-2 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
