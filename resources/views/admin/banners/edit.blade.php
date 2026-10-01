@extends('layouts.admin')

@section('title', 'Edit Banner - ' . $banner->title)
@section('page_title', 'Edit Banner Slide')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Edit Banner Slide</h5>
            <p class="text-muted small mb-0">Update hero visual elements, text and CTA settings.</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white" style="max-width: 900px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Badge / Pill Text</label>
                        <input type="text" name="badge_text" class="form-control" value="{{ old('badge_text', $banner->badge_text) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold small text-dark">Main Heading Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Subtitle / Description</label>
                    <textarea name="subtitle" class="form-control" rows="2">{{ old('subtitle', $banner->subtitle) }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Primary CTA Button Text <span class="text-danger">*</span></label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $banner->button_text) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Primary CTA URL <span class="text-danger">*</span></label>
                        <input type="text" name="button_url" class="form-control" value="{{ old('button_url', $banner->button_url) }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Secondary Button Text</label>
                        <input type="text" name="secondary_button_text" class="form-control" value="{{ old('secondary_button_text', $banner->secondary_button_text) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Secondary Button URL</label>
                        <input type="text" name="secondary_button_url" class="form-control" value="{{ old('secondary_button_url', $banner->secondary_button_url) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Car Graphic / Render Image</label>
                        @if($banner->car_image_path)
                            <div class="mb-2 bg-dark p-2 rounded text-center" style="max-width: 160px;">
                                <img src="{{ asset($banner->car_image_path) }}" alt="Car" class="img-fluid" style="max-height: 50px;">
                            </div>
                        @endif
                        <input type="file" name="car_image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Background Banner Image</label>
                        @if($banner->image_path)
                            <div class="mb-2 bg-dark p-2 rounded text-center" style="max-width: 160px;">
                                <img src="{{ asset($banner->image_path) }}" alt="BG" class="img-fluid" style="max-height: 50px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Features Bullet Points (One per line)</label>
                    <textarea name="features_text" class="form-control" rows="3">{{ old('features_text', $featuresText) }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Position</label>
                        <select name="position" class="form-select">
                            <option value="hero" {{ old('position', $banner->position) == 'hero' ? 'selected' : '' }}>Hero Carousel</option>
                            <option value="promotional" {{ old('position', $banner->position) == 'promotional' ? 'selected' : '' }}>Promotional Strip</option>
                            <option value="footer" {{ old('position', $banner->position) == 'footer' ? 'selected' : '' }}>Footer CTA</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $banner->sort_order) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" {{ old('status', $banner->status) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="statusSwitch">Banner Active</label>
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
