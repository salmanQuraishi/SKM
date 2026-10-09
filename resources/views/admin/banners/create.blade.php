@extends('layouts.admin')

@section('title', 'Add Banner - AutoLogos')
@section('page_title', 'Create Banner Slide')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Create Banner Slide</h5>
            <p class="text-muted small mb-0">Set up hero section titles, bullet points, CTA button URLs and automotive imagery.</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 bg-white" style="max-width: 900px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Badge / Pill Text</label>
                        <input type="text" name="badge_text" class="form-control" value="{{ old('badge_text', 'PREMIUM AUTOMOTIVE EMBLEMS') }}" placeholder="e.g., PREMIUM ACCESSORIES">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold small text-dark">Main Heading Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g., UPGRADE YOUR RIDE'S IDENTITY" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Subtitle / Description</label>
                    <textarea name="subtitle" class="form-control" rows="2" placeholder="e.g., Factory-grade chrome emblems, glossy black lettering & 3D replacement badges for all leading car brands.">{{ old('subtitle') }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Primary CTA Button Text <span class="text-danger">*</span></label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', 'SHOP CATALOG') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Primary CTA URL <span class="text-danger">*</span></label>
                        <input type="text" name="button_url" class="form-control" value="{{ old('button_url', '/shop') }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Secondary Button Text</label>
                        <input type="text" name="secondary_button_text" class="form-control" value="{{ old('secondary_button_text', 'EXPLORE BRANDS') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Secondary Button URL</label>
                        <input type="text" name="secondary_button_url" class="form-control" value="{{ old('secondary_button_url', '/#brands') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Car Graphic / Render Image</label>
                        <input type="file" name="car_image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Background Banner Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Features Bullet Points (One per line)</label>
                    <textarea name="features_text" class="form-control" rows="3" placeholder="100% Genuine Fitment&#10;Chrome & Gloss Finish&#10;Waterproof 3M Adhesive">{{ old('features_text', "100% Exact OEM Fit\nPremium Chrome & Matte Finish\nWaterproof 3M Automotive Backing") }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Position</label>
                        <select name="position" class="form-select">
                            <option value="hero" {{ old('position', 'hero') == 'hero' ? 'selected' : '' }}>Hero Carousel (Main Top Slider)</option>
                            <option value="promo" {{ old('position') == 'promo' ? 'selected' : '' }}>Promotional Strip (Middle)</option>
                            <option value="footer" {{ old('position') == 'footer' ? 'selected' : '' }}>Footer CTA</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" checked>
                            <label class="form-check-label fw-semibold" for="statusSwitch">Banner Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-theme-red px-4 py-2 fw-bold">
                    <i class="fa-solid fa-check me-2"></i> Save Banner Slide
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
