@extends('layouts.admin')

@section('title', 'Add New Product - AutoLogos')
@section('page_title', 'Add New Product')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Create Product</h5>
            <p class="text-muted small mb-0">Add a new car logo, chrome emblem, or monogram letter product.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Catalog
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the following errors:</h6>
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Left 8 Columns: Main Information -->
            <div class="col-lg-8">
                <!-- Basic Card -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-circle-info text-danger me-2"></i> Basic Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g., Hyundai i20 Rear Chrome Monogram Emblem" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">SKU Code <span class="text-danger">*</span></label>
                                <input type="text" name="sku" class="form-control text-uppercase" value="{{ old('sku') }}" placeholder="e.g., HYU-I20-EMB-01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Product Type <span class="text-danger">*</span></label>
                                <select name="product_type" class="form-select" required>
                                    <option value="logo" {{ old('product_type') == 'logo' ? 'selected' : '' }}>Car Logo / Monogram</option>
                                    <option value="emblem" {{ old('product_type') == 'emblem' ? 'selected' : '' }}>Badge / Emblem</option>
                                    <option value="monogram" {{ old('product_type') == 'monogram' ? 'selected' : '' }}>Lettering / Monogram</option>
                                    <option value="accessory" {{ old('product_type') == 'accessory' ? 'selected' : '' }}>Automotive Accessory</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Short Description</label>
                            <textarea name="short_description" class="form-control" rows="2" placeholder="Brief summary of the emblem/logo...">{{ old('short_description') }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-dark">Full Description & Specifications</label>
                            <textarea name="full_description" class="form-control" rows="6" placeholder="Detailed product specifications, installation instructions, fitment info...">{{ old('full_description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Automotive Compatibility & Placement Card -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-car text-danger me-2"></i> Vehicle Fitment & Specifications</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Placement Area</label>
                                <select name="placement" class="form-select">
                                    <option value="Front Grille" {{ old('placement') == 'Front Grille' ? 'selected' : '' }}>Front Grille</option>
                                    <option value="Rear Trunk" {{ old('placement') == 'Rear Trunk' ? 'selected' : '' }}>Rear Trunk</option>
                                    <option value="Fender / Side" {{ old('placement') == 'Fender / Side' ? 'selected' : '' }}>Fender / Side</option>
                                    <option value="Steering Wheel" {{ old('placement') == 'Steering Wheel' ? 'selected' : '' }}>Steering Wheel</option>
                                    <option value="Universal" {{ old('placement') == 'Universal' ? 'selected' : '' }}>Universal Fit</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Finish Type</label>
                                <select name="finish" class="form-select">
                                    <option value="Gloss Chrome" {{ old('finish') == 'Gloss Chrome' ? 'selected' : '' }}>Gloss Chrome</option>
                                    <option value="Matte Black" {{ old('finish') == 'Matte Black' ? 'selected' : '' }}>Matte Black</option>
                                    <option value="Gloss Black" {{ old('finish') == 'Gloss Black' ? 'selected' : '' }}>Gloss Black</option>
                                    <option value="Silver Metallic" {{ old('finish') == 'Silver Metallic' ? 'selected' : '' }}>Silver Metallic</option>
                                    <option value="Gold Plated" {{ old('finish') == 'Gold Plated' ? 'selected' : '' }}>Gold Plated</option>
                                    <option value="Carbon Fiber" {{ old('finish') == 'Carbon Fiber' ? 'selected' : '' }}>Carbon Fiber Look</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Compatible Text</label>
                                <input type="text" name="compatible_models_text" class="form-control" value="{{ old('compatible_models_text') }}" placeholder="e.g., Hyundai i20 (2015-2023)">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">Weight (g)</label>
                                <input type="number" step="0.01" name="weight" class="form-control" value="{{ old('weight', 45) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">Length (cm)</label>
                                <input type="number" step="0.01" name="length" class="form-control" value="{{ old('length', 12) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">Width (cm)</label>
                                <input type="number" step="0.01" name="width" class="form-control" value="{{ old('width', 6) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">Height (cm)</label>
                                <input type="number" step="0.01" name="height" class="form-control" value="{{ old('height', 1) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory Card -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-indian-rupee-sign text-danger me-2"></i> Pricing & Stock</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Regular MRP (&#x20B9;) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="regular_price" class="form-control" value="{{ old('regular_price') }}" placeholder="799.00" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Sale Price (&#x20B9;)</label>
                                <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price') }}" placeholder="499.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Cost Price (&#x20B9;)</label>
                                <input type="number" step="0.01" name="cost_price" class="form-control" value="{{ old('cost_price') }}" placeholder="200.00">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Initial Stock Qty <span class="text-danger">*</span></label>
                                <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 50) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Low Stock Alert Level</label>
                                <input type="number" name="min_stock_alert" class="form-control" value="{{ old('min_stock_alert', 5) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">GST / Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ old('tax_rate', 18.00) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Images Upload -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-images text-danger me-2"></i> Product Media Gallery</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Upload Emblem Images (SVG / PNG / JPG / WEBP)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <div class="form-text small">First selected image will automatically become the primary store image.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 4 Columns: Relations, Flags & Publishing -->
            <div class="col-lg-4">
                <!-- Publish / Status Card -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-sliders text-danger me-2"></i> Status & Flags</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="statusSwitch">Published / Active</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="featSwitch" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="featSwitch">Featured Product</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_new_arrival" id="newSwitch" value="1" {{ old('is_new_arrival', '1') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="newSwitch">New Arrival</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_best_seller" id="bestSwitch" value="1" {{ old('is_best_seller') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="bestSwitch">Best Seller</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_monogram" id="monoSwitch" value="1" {{ old('is_monogram') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="monoSwitch">Monogram / Lettering</label>
                        </div>

                        <hr class="my-4">
                        <button type="submit" class="btn btn-theme-red w-100 py-2 fw-bold">
                            <i class="fa-solid fa-check me-2"></i> Save & Publish Product
                        </button>
                    </div>
                </div>

                <!-- Vehicle Hierarchy & Organization Card -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-car-side text-danger me-2"></i> Vehicle Hierarchy & Categories</h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Step 1: Company / Brand -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Company / Brand</label>
                            <select name="brand_id" id="prodBrandSelect" class="form-select">
                                <option value="">-- Universal / No Company --</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">e.g. Tata, Mahindra, Maruti Suzuki, Hyundai</div>
                        </div>

                        <!-- Step 2: Car Model -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center">
                                <span>Car Model</span>
                                <span id="prodModelSpinner" class="spinner-border spinner-border-sm text-danger" style="display: none;" role="status"></span>
                            </label>
                            <select name="car_model_id" id="prodModelSelect" class="form-select">
                                <option value="">-- Universal / All Models --</option>
                                @foreach($models as $m)
                                    <option value="{{ $m->id }}" {{ old('car_model_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} ({{ $m->brand->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">e.g. Tata Punch, Nexon, Thar</div>
                        </div>

                        <!-- Step 3: Car Variant -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center">
                                <span>Car Variant</span>
                                <span id="prodVariantSpinner" class="spinner-border spinner-border-sm text-danger" style="display: none;" role="status"></span>
                            </label>
                            <select name="model_variant_id" id="prodVariantSelect" class="form-select">
                                <option value="">-- All Variants / No Specific Variant --</option>
                                @foreach($variants as $v)
                                    <option value="{{ $v->id }}" {{ old('model_variant_id') == $v->id ? 'selected' : '' }}>
                                        {{ $v->name }} ({{ $v->carModel->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">e.g. Punch Pure, Punch Adventure, Thar AX</div>
                        </div>

                        <hr class="my-3">

                        <!-- Categories -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Primary Category</label>
                            <select name="primary_category_id" class="form-select">
                                <option value="">Select Primary Category</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('primary_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-dark">Additional Categories</label>
                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                @foreach($categories as $c)
                                    <div class="form-check small mb-1">
                                        <input class="form-check-input" type="checkbox" name="category_ids[]" value="{{ $c->id }}" id="cat_{{ $c->id }}" {{ is_array(old('category_ids')) && in_array($c->id, old('category_ids')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat_{{ $c->id }}">{{ $c->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Card -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-magnifying-glass text-danger me-2"></i> SEO Meta</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-dark">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandSelect = document.getElementById('prodBrandSelect');
    const modelSelect = document.getElementById('prodModelSelect');
    const variantSelect = document.getElementById('prodVariantSelect');
    const modelSpinner = document.getElementById('prodModelSpinner');
    const variantSpinner = document.getElementById('prodVariantSpinner');

    if (brandSelect && modelSelect && variantSelect) {
        brandSelect.addEventListener('change', function() {
            const brandId = this.value;
            modelSelect.innerHTML = '<option value="">-- Universal / All Models --</option>';
            variantSelect.innerHTML = '<option value="">-- All Variants / No Specific Variant --</option>';

            if (!brandId) return;

            if (modelSpinner) modelSpinner.style.display = 'inline-block';

            fetch('/ajax/models-by-brand/' + brandId)
                .then(res => res.json())
                .then(data => {
                    if (modelSpinner) modelSpinner.style.display = 'none';
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            opt.textContent = m.name;
                            modelSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    if (modelSpinner) modelSpinner.style.display = 'none';
                    console.error('Error loading car models:', err);
                });
        });

        modelSelect.addEventListener('change', function() {
            const modelId = this.value;
            variantSelect.innerHTML = '<option value="">-- All Variants / No Specific Variant --</option>';

            if (!modelId) return;

            if (variantSpinner) variantSpinner.style.display = 'inline-block';

            fetch('/ajax/variants-by-model/' + modelId)
                .then(res => res.json())
                .then(data => {
                    if (variantSpinner) variantSpinner.style.display = 'none';
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.id;
                            opt.textContent = v.name;
                            variantSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    if (variantSpinner) variantSpinner.style.display = 'none';
                    console.error('Error loading variants:', err);
                });
        });
    }
});
</script>
@endpush
@endsection
