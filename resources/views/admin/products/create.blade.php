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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" novalidate id="createProductForm">
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
                            <input type="text" name="name" id="prodNameInput" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Hyundai i20 Rear Chrome Monogram Emblem" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold small text-dark mb-0">SKU Code</label>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-danger text-decoration-none fw-semibold" style="font-size: 11px;" onclick="generateSku(true)">
                                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Generate SKU
                                    </button>
                                </div>
                                <input type="text" name="sku" id="prodSkuInput" class="form-control text-uppercase @error('sku') is-invalid @enderror" value="{{ old('sku') }}" placeholder="Auto-generated if left blank">
                                <div class="form-text text-muted" style="font-size: 11px;">Leave blank to auto-generate unique SKU automatically</div>
                                @error('sku')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center">
                                    <span>Product Type & Placement <span class="text-danger">*</span></span>
                                    <span class="badge bg-light text-muted border fw-normal" style="font-size: 10px;">Header & Catalog Synced</span>
                                </label>
                                <select name="product_type" id="prodTypeSelect" class="form-select @error('product_type') is-invalid @enderror" onchange="handleProductTypeChange(this)" required>
                                    <option value="">-- Select Product Type / Category --</option>
                                    <optgroup label="🌟 Featured Types (Direct Header Navigation)">
                                        <option value="front_logo" {{ old('product_type') == 'front_logo' ? 'selected' : '' }}>Front Logo (Header: FRONT LOGO)</option>
                                        <option value="rear_logo" {{ old('product_type') == 'rear_logo' ? 'selected' : '' }}>Rear Logo (Header: REAR LOGO)</option>
                                        <option value="bonnet_letters" {{ old('product_type') == 'bonnet_letters' ? 'selected' : '' }}>Bonnet & Letters (Header: BONNET & LETTERS)</option>
                                        <option value="full_monogram_kit" {{ old('product_type') == 'full_monogram_kit' ? 'selected' : '' }}>Full Monogram Kits (Header: FULL MONOGRAM KITS)</option>
                                        <option value="single_badge" {{ old('product_type') == 'single_badge' ? 'selected' : '' }}>Single Badges (Header: SINGLE BADGES)</option>
                                        <option value="new_arrival" {{ old('product_type') == 'new_arrival' ? 'selected' : '' }}>New Arrivals (Header: NEW ARRIVALS)</option>
                                    </optgroup>
                                    <optgroup label="🏷️ Standard Types (Auto-Mapped to Catalog & Header)">
                                        <option value="logo" {{ old('product_type') == 'logo' ? 'selected' : '' }}>Car Logo / Monogram (Catalog: Car Logos • Links to Header by Placement)</option>
                                        <option value="emblem" {{ old('product_type') == 'emblem' ? 'selected' : '' }}>Badge / Emblem (Header: Single Badges • Catalog: Model Badges)</option>
                                        <option value="monogram" {{ old('product_type') == 'monogram' ? 'selected' : '' }}>Lettering / Monogram (Header: Bonnet & Letters • Catalog: Monograms)</option>
                                        <option value="accessory" {{ old('product_type') == 'accessory' ? 'selected' : '' }}>Automotive Accessory (Catalog: Accessories)</option>
                                    </optgroup>
                                </select>
                                @error('product_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                <!-- Real-time Destination & Visibility Preview Badge -->
                                <div id="prodTypeHintCard" class="mt-2 p-2 rounded-2 bg-light border border-secondary-subtle" style="display: none; font-size: 11.5px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fa-solid fa-compass text-danger me-1"></i>
                                        <strong class="text-dark">Website Display Destination:</strong>
                                    </div>
                                    <div id="prodTypeHintDetails" class="text-secondary ps-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Short Description</label>
                            <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="2" placeholder="Brief summary of the emblem/logo...">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-dark">Full Description & Specifications</label>
                            <textarea name="full_description" class="form-control @error('full_description') is-invalid @enderror" rows="6" placeholder="Detailed product specifications, installation instructions, fitment info...">{{ old('full_description') }}</textarea>
                            @error('full_description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
                                    <option value="Bonnet / Boot" {{ old('placement') == 'Bonnet / Boot' ? 'selected' : '' }}>Bonnet / Boot</option>
                                    <option value="Full Exterior Kit" {{ old('placement') == 'Full Exterior Kit' ? 'selected' : '' }}>Full Exterior Kit</option>
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
                                <input type="number" step="0.01" name="regular_price" id="prodRegularPrice" class="form-control @error('regular_price') is-invalid @enderror" value="{{ old('regular_price') }}" placeholder="e.g. 799" required>
                                <div class="form-text text-muted" style="font-size: 11px;">Enter actual MRP price (e.g. 799)</div>
                                @error('regular_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Sale Price (&#x20B9;)</label>
                                <input type="number" step="0.01" name="sale_price" id="prodSalePrice" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price') }}" placeholder="e.g. 499">
                                <div class="form-text text-muted" style="font-size: 11px;">Discounted price (must be &le; MRP)</div>
                                @error('sale_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Cost Price (&#x20B9;)</label>
                                <input type="number" step="0.01" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price') }}" placeholder="e.g. 200">
                                <div class="form-text text-muted" style="font-size: 11px;">Your internal cost (optional)</div>
                                @error('cost_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">Initial Stock Qty <span class="text-danger">*</span></label>
                                <input type="number" name="stock_quantity" id="prodStockQty" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', 50) }}" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">Low Stock Alert Level</label>
                                <input type="number" name="min_stock_alert" class="form-control @error('min_stock_alert') is-invalid @enderror" value="{{ old('min_stock_alert', 5) }}">
                                @error('min_stock_alert')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">GST / Tax Rate (%)</label>
                                <input type="number" step="0.01" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate', 18.00) }}">
                                @error('tax_rate')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">HSN / SAC Code</label>
                                <input type="text" name="hsn_code" class="form-control @error('hsn_code') is-invalid @enderror" value="{{ old('hsn_code', old('hsn_sac', '8708')) }}" placeholder="e.g., 8708">
                                <div class="form-text small" style="font-size: 10px;">Automotive parts standard: 8708</div>
                                @error('hsn_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                            <input type="file" name="images[]" id="prodImagesInput" class="form-control @error('images.*') is-invalid @enderror" multiple accept="image/*">
                            <div class="form-text small">First selected image will automatically become the primary store image.</div>
                            @error('images.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Similar Product / Marketplace Links (Amazon & Flipkart) -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-cart-shopping text-danger me-2"></i> Similar Product Links (Amazon / Flipkart)
                        </h6>
                        <span class="badge bg-secondary-subtle text-secondary small">Optional</span>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-3">Agar yeh product Amazon ya Flipkart par bhi available hai, toh aap unka link yahan add kar sakte hain.</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark d-flex align-items-center gap-2">
                                    <i class="fa-brands fa-amazon text-warning fs-5"></i>
                                    <span>Amazon Product Link</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-link"></i></span>
                                    <input type="text" name="amazon_url" id="prodAmazonUrl" class="form-control font-sm" placeholder="https://www.amazon.in/dp/..." value="{{ old('amazon_url') }}">
                                </div>
                                <div class="form-text small" style="font-size: 11px;">Amazon product listing URL paste karein</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-bag-shopping text-primary fs-5"></i>
                                    <span>Flipkart Product Link</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-link"></i></span>
                                    <input type="text" name="flipkart_url" id="prodFlipkartUrl" class="form-control font-sm" placeholder="https://www.flipkart.com/..." value="{{ old('flipkart_url') }}">
                                </div>
                                <div class="form-text small" style="font-size: 11px;">Flipkart product listing URL paste karein</div>
                            </div>
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
                        <button type="submit" id="saveProductBtn" class="btn btn-theme-red w-100 py-2 fw-bold">
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
                    </div>
                </div>

                <!-- Product Category & Navigation Mapping Card -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-shapes text-danger me-2"></i> Category & Header Menu</h6>
                        <span class="badge bg-danger text-white small px-2 py-1">Required</span>
                    </div>
                    <div class="card-body p-4">
                        <!-- Primary Category -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark d-flex align-items-center justify-content-between">
                                <span>Primary Category <span class="text-danger">*</span></span>
                                <span class="text-muted" style="font-size: 11px;">Main section on website</span>
                            </label>
                            <select name="primary_category_id" id="primaryCategorySelect" class="form-select form-select-lg fs-6 @error('primary_category_id') is-invalid @enderror" onchange="handlePrimaryCatChange(this)">
                                <option value="">-- Choose Category (e.g. Front Logo, Rear Logo, Monogram) --</option>
                                <optgroup label="🌟 Featured Header Navigation Categories">
                                    @foreach($categories->where('is_featured', true) as $c)
                                        <option value="{{ $c->id }}" data-slug="{{ $c->slug }}" {{ old('primary_category_id') == $c->id ? 'selected' : '' }}>
                                            ★ {{ $c->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                @if($categories->where('is_featured', false)->isNotEmpty())
                                    <optgroup label="📂 Other Categories">
                                        @foreach($categories->where('is_featured', false) as $c)
                                            <option value="{{ $c->id }}" data-slug="{{ $c->slug }}" {{ old('primary_category_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            <div class="form-text small text-muted">Select the primary category to link this product to header navigation and shop catalog.</div>
                            @error('primary_category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Additional Categories -->
                        <div class="mb-0">
                            <label class="form-label fw-bold small text-dark d-flex align-items-center justify-content-between">
                                <span>Also Show In (Multiple Categories)</span>
                                <span class="text-muted" style="font-size: 11px;">Tick all matching</span>
                            </label>
                            <div class="p-2 bg-light rounded-3 border" style="max-height: 200px; overflow-y: auto;">
                                <div class="row g-2">
                                    @foreach($categories as $c)
                                        <div class="col-12 col-sm-6">
                                            <div class="form-check p-2 bg-white rounded border border-1 h-100 d-flex align-items-center">
                                                <input class="form-check-input ms-0 me-2" type="checkbox" name="category_ids[]" value="{{ $c->id }}" id="cat_{{ $c->id }}" data-slug="{{ $c->slug }}" {{ (is_array(old('category_ids')) && in_array($c->id, old('category_ids'))) ? 'checked' : '' }}>
                                                <label class="form-check-label small fw-semibold text-truncate mb-0 cursor-pointer flex-grow-1" for="cat_{{ $c->id }}">
                                                    @if($c->is_featured)
                                                        <i class="fa-solid fa-star text-warning me-1 small"></i>
                                                    @endif
                                                    {{ $c->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-text small text-muted mt-1">Product will appear in all selected categories in the shop filters.</div>
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
function handleProductTypeChange(selectElem) {
    const type = selectElem.value;
    const catSelect = document.getElementById('primaryCategorySelect');
    const placementSelect = document.querySelector('select[name="placement"]');
    const newArrivalInput = document.querySelector('input[name="is_new_arrival"]');
    const hintCard = document.getElementById('prodTypeHintCard');
    const hintDetails = document.getElementById('prodTypeHintDetails');

    const typeConfig = {
        'front_logo': {
            slug: 'front-logo',
            placement: 'Front Grille',
            header: 'FRONT LOGO',
            category: 'Front Logo',
            info: 'Product website ke header menu [FRONT LOGO] me dikhega. Placement "Front Grille" auto-select ho gaya hai.'
        },
        'rear_logo': {
            slug: 'rear-logo',
            placement: 'Rear Trunk',
            header: 'REAR LOGO',
            category: 'Rear Logo',
            info: 'Product website ke header menu [REAR LOGO] me dikhega. Placement "Rear Trunk" auto-select ho gaya hai.'
        },
        'bonnet_letters': {
            slug: 'bonnet-tailgate-letters',
            placement: 'Bonnet / Boot',
            header: 'BONNET & LETTERS',
            category: 'Bonnet & Letters',
            info: 'Product website ke header menu [BONNET & LETTERS] me dikhega. Placement "Bonnet / Boot" auto-select ho gaya hai.'
        },
        'full_monogram_kit': {
            slug: 'kit-full-monogram',
            placement: 'Full Exterior Kit',
            header: 'FULL MONOGRAM KITS',
            category: 'Full Monogram Kits',
            info: 'Product website ke header menu [FULL MONOGRAM KITS] me dikhega. Placement "Full Exterior Kit" auto-select ho gaya hai.'
        },
        'single_badge': {
            slug: 'single-name-badge',
            placement: null,
            header: 'SINGLE BADGES',
            category: 'Single Badges',
            info: 'Product website ke header menu [SINGLE BADGES] me dikhega.'
        },
        'new_arrival': {
            slug: 'new-arrivals',
            placement: null,
            header: 'NEW ARRIVALS',
            category: 'New Arrivals',
            info: 'Product website ke header menu [NEW ARRIVALS] me dikhega. "Mark as New Arrival" auto-checked ho gaya hai.'
        },
        'logo': {
            slug: 'car-logos',
            placement: null,
            header: 'Placement Based (Front Grille ➔ FRONT LOGO, Rear Trunk ➔ REAR LOGO)',
            category: 'Car Logos',
            info: 'Category "Car Logos" me aayega. Agar Placement "Front Grille" rakhte hain toh Header FRONT LOGO me bhi dikhega; agar "Rear Trunk" rakhte hain toh REAR LOGO me bhi dikhega.'
        },
        'emblem': {
            slug: 'single-name-badge',
            secondarySlug: 'model-logos-badges',
            placement: null,
            header: 'SINGLE BADGES',
            category: 'Single Badges & Model Logos / Badges',
            info: 'Header Menu [SINGLE BADGES] aur Category [Model Logos / Badges] dono me automatic include hoga.'
        },
        'monogram': {
            slug: 'bonnet-tailgate-letters',
            secondarySlug: 'car-monograms',
            placement: 'Bonnet / Boot',
            header: 'BONNET & LETTERS',
            category: 'Bonnet & Letters & Car Monograms',
            info: 'Header Menu [BONNET & LETTERS] aur Category [Car Monograms] dono me automatic include hoga.'
        },
        'accessory': {
            slug: 'accessories',
            placement: 'Universal',
            header: 'Catalog Only (Accessories)',
            category: 'Accessories',
            info: 'Shop category "Accessories", ALL PRODUCTS (/shop), Search aur Brand/Model pages me dikhega.'
        }
    };

    if (type && typeConfig[type]) {
        const conf = typeConfig[type];

        // Update Destination Hint UI
        if (hintCard && hintDetails) {
            hintCard.style.display = 'block';
            hintDetails.innerHTML = `
                <div class="mb-1"><span class="badge bg-danger me-1">Header Menu:</span> <strong class="text-dark">${conf.header}</strong></div>
                <div class="mb-1"><span class="badge bg-secondary me-1">Primary Category:</span> <strong>${conf.category}</strong></div>
                <div class="text-dark"><i class="fa-solid fa-circle-check text-success me-1"></i> ${conf.info}</div>
                <div class="text-muted mt-1" style="font-size: 11px;"><i class="fa-solid fa-car-side me-1"></i> Also visible in: ALL PRODUCTS, Search, Brand and Model vehicle pages.</div>
            `;
        }

        // Auto select primary category in dropdown
        if (catSelect) {
            for (let i = 0; i < catSelect.options.length; i++) {
                if (catSelect.options[i].dataset.slug === conf.slug) {
                    catSelect.selectedIndex = i;
                    handlePrimaryCatChange(catSelect);
                    break;
                }
            }
        }

        // Auto check secondary category checkbox if present
        if (conf.secondarySlug) {
            const secChk = document.querySelector(`input[name="category_ids[]"][data-slug="${conf.secondarySlug}"]`);
            if (secChk) secChk.checked = true;
        }

        // Auto select placement if empty or default
        if (conf.placement && placementSelect && (!placementSelect.value || placementSelect.value === 'Universal')) {
            placementSelect.value = conf.placement;
        }

        // Auto check new arrival
        if (type === 'new_arrival' && newArrivalInput) {
            newArrivalInput.checked = true;
        }
    } else {
        if (hintCard) hintCard.style.display = 'none';
    }
}

function handlePrimaryCatChange(selectElem) {
    const opt = selectElem.options[selectElem.selectedIndex];
    const catId = selectElem.value;
    const catSlug = opt ? opt.dataset.slug : '';

    if (catId) {
        const chk = document.getElementById('cat_' + catId);
        if (chk) chk.checked = true;
    }

    const placementSelect = document.querySelector('select[name="placement"]');
    if (placementSelect && catSlug) {
        if (catSlug === 'front-logo') {
            placementSelect.value = 'Front Grille';
        } else if (catSlug === 'rear-logo') {
            placementSelect.value = 'Rear Trunk';
        } else if (catSlug === 'bonnet-tailgate-letters') {
            placementSelect.value = 'Bonnet / Boot';
        } else if (catSlug === 'kit-full-monogram') {
            placementSelect.value = 'Full Exterior Kit';
        }
    }

    if (catSlug === 'new-arrivals') {
        const newArrivalInput = document.querySelector('input[name="is_new_arrival"]');
        if (newArrivalInput) newArrivalInput.checked = true;
    }
}

function generateSku(force = false) {
    const nameInput = document.getElementById('prodNameInput');
    const skuInput = document.getElementById('prodSkuInput');
    const name = nameInput ? nameInput.value.trim() : '';

    if (!name) {
        if (force) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Product Name First',
                    text: 'Please enter Product Name first to auto-generate SKU code.',
                    confirmButtonColor: '#d90429'
                });
            } else {
                alert('Please enter Product Name first.');
            }
            if (nameInput) nameInput.focus();
        }
        return;
    }

    const words = name.replace(/[^a-zA-Z0-9\s]/g, '').toUpperCase().split(/\s+/).filter(Boolean);
    let prefix = '';
    if (words.length >= 3) {
        prefix = words[0].substring(0, 3) + '-' + words[1].substring(0, 3) + '-' + words[2].substring(0, 3);
    } else if (words.length === 2) {
        prefix = words[0].substring(0, 4) + '-' + words[1].substring(0, 4);
    } else if (words.length === 1) {
        prefix = words[0].substring(0, 6);
    } else {
        prefix = 'PRD-GEN';
    }

    const randomSuffix = Math.floor(100 + Math.random() * 900);
    if (skuInput) {
        skuInput.value = prefix + '-' + randomSuffix;
        skuInput.classList.remove('is-invalid');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const prodTypeSelect = document.getElementById('prodTypeSelect');
    if (prodTypeSelect && prodTypeSelect.value) {
        handleProductTypeChange(prodTypeSelect);
    }

    const form = document.getElementById('createProductForm') || document.querySelector('form[action="{{ route('admin.products.store') }}"]');
    const saveBtn = document.getElementById('saveProductBtn');
    const nameInput = document.getElementById('prodNameInput');
    const skuInput = document.getElementById('prodSkuInput');

    // Auto-generate SKU as user types product name if SKU is empty
    if (nameInput && skuInput) {
        nameInput.addEventListener('input', function() {
            if (!skuInput.value || skuInput.dataset.autoGenerated === 'true') {
                generateSku(false);
                skuInput.dataset.autoGenerated = 'true';
            }
        });
        skuInput.addEventListener('input', function() {
            skuInput.dataset.autoGenerated = 'false';
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            const regPriceInput = document.getElementById('prodRegularPrice');
            const salePriceInput = document.getElementById('prodSalePrice');
            const stockInput = document.getElementById('prodStockQty');

            // If SKU is empty, auto-generate it right now
            if (skuInput && !skuInput.value.trim() && nameInput && nameInput.value.trim()) {
                generateSku(false);
            }

            let errors = [];
            let firstInvalid = null;

            if (!nameInput || !nameInput.value.trim()) {
                errors.push('<strong>Product Name:</strong> कृपया प्रोडक्ट का नाम दर्ज करें');
                if (nameInput) {
                    nameInput.classList.add('is-invalid');
                    if (!firstInvalid) firstInvalid = nameInput;
                }
            } else if (nameInput) {
                nameInput.classList.remove('is-invalid');
            }

            if (!regPriceInput || !regPriceInput.value || parseFloat(regPriceInput.value) <= 0) {
                errors.push('<strong>Regular MRP (₹):</strong> कृपया मान्य MRP कीमत दर्ज करें (जैसे 799)');
                if (regPriceInput) {
                    regPriceInput.classList.add('is-invalid');
                    if (!firstInvalid) firstInvalid = regPriceInput;
                }
            } else if (regPriceInput) {
                regPriceInput.classList.remove('is-invalid');
            }

            if (salePriceInput && salePriceInput.value && regPriceInput && regPriceInput.value) {
                if (parseFloat(salePriceInput.value) > parseFloat(regPriceInput.value)) {
                    errors.push('<strong>Sale Price:</strong> सेल प्राइस Regular MRP से ज्यादा नहीं हो सकती');
                    salePriceInput.classList.add('is-invalid');
                    if (!firstInvalid) firstInvalid = salePriceInput;
                } else {
                    salePriceInput.classList.remove('is-invalid');
                }
            }

            if (!stockInput || stockInput.value === '' || parseInt(stockInput.value) < 0) {
                errors.push('<strong>Initial Stock Qty:</strong> कृपया स्टॉक संख्या दर्ज करें (जैसे 50)');
                if (stockInput) {
                    stockInput.classList.add('is-invalid');
                    if (!firstInvalid) firstInvalid = stockInput;
                }
            } else if (stockInput) {
                stockInput.classList.remove('is-invalid');
            }

            if (errors.length > 0) {
                e.preventDefault();
                e.stopPropagation();

                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => firstInvalid.focus(), 300);
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'कृपया यह जरूरी जानकारी भरें',
                        html: '<ul class="text-start mb-0 ps-3">' + errors.map(e => '<li>' + e + '</li>').join('') + '</ul>',
                        confirmButtonColor: '#d90429',
                        confirmButtonText: 'समझ गया'
                    });
                } else {
                    alert('Please fill required fields:\n' + errors.join('\n'));
                }
                return false;
            }

            // CRITICAL FIX: DO NOT disable the button synchronously before the submit dispatches!
            // Applying pointer-events:none prevents double-clicks without aborting the browser POST pipeline.
            if (saveBtn) {
                saveBtn.style.pointerEvents = 'none';
                saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Saving & Publishing...';
                setTimeout(() => {
                    saveBtn.disabled = true;
                }, 50);
            }
        });
    }

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
