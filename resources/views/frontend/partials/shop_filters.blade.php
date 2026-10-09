@php
    $prefix = isset($isMobile) && $isMobile ? 'mob_' : 'desk_';
@endphp

<div class="shop-filter-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: #1e293b !important;">
        <span class="fw-bold font-sm text-uppercase text-white">
            <i class="fa-solid fa-sliders text-danger me-2"></i> FILTER BY
        </span>
        @if(request()->hasAny(['brand', 'model', 'variant', 'category', 'placement', 'finish', 'min_price', 'max_price', 'q']))
            <a href="{{ route('shop') }}" class="text-danger small text-decoration-none fw-semibold">
                <i class="fa-solid fa-rotate-left me-1"></i> Reset
            </a>
        @endif
    </div>

    <!-- 1. VEHICLE FITMENT ACCORDION BOX -->
    <div class="filter-vehicle-box">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="fw-bold font-sm text-uppercase text-white d-flex align-items-center">
                <i class="fa-solid fa-car-side text-danger me-2 fs-6"></i> SELECT VEHICLE
            </div>
            @if(request('brand') || request('model') || request('variant'))
                <a href="{{ request()->fullUrlWithQuery(['brand' => null, 'model' => null, 'variant' => null]) }}" class="text-warning small text-decoration-none" style="font-size: 11px;">
                    Clear
                </a>
            @endif
        </div>

        @if($selectedBrand)
            <div class="active-vehicle-banner text-white">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset($selectedBrand->logo ?: 'images/brands/' . $selectedBrand->slug . '.svg') }}" alt="{{ $selectedBrand->name }}" style="width: 22px; height: 22px; object-fit: contain;">
                    <div class="flex-grow-1 text-truncate">
                        <div class="fw-bold text-white font-sm">{{ $selectedBrand->name }}</div>
                        <div class="text-white-50" style="font-size: 11px;">
                            {{ $selectedModel ? $selectedModel->name : 'All Models' }} 
                            {{ $selectedVariant ? '› ' . $selectedVariant->name : '' }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('shop') }}" method="GET" class="vehicle-filter-form">
            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('placement')) <input type="hidden" name="placement" value="{{ request('placement') }}"> @endif
            @if(request('finish')) <input type="hidden" name="finish" value="{{ request('finish') }}"> @endif
            @if(request('min_price')) <input type="hidden" name="min_price" value="{{ request('min_price') }}"> @endif
            @if(request('max_price')) <input type="hidden" name="max_price" value="{{ request('max_price') }}"> @endif

            <!-- 1. Brand -->
            <div class="mb-2">
                <select name="brand" id="{{ $prefix }}filter_brand_select" class="form-select form-select-sm" onchange="handleBrandChange(this, '{{ $prefix }}')">
                    <option value="">1. All Companies / Brands</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->slug }}" data-id="{{ $b->id }}" {{ (request('brand') == $b->slug || ($selectedBrand && $selectedBrand->slug == $b->slug)) ? 'selected' : '' }}>
                            {{ $b->name }} ({{ $b->products_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Car Model -->
            <div class="mb-2">
                <select name="model" id="{{ $prefix }}filter_model_select" class="form-select form-select-sm" onchange="handleModelChange(this, '{{ $prefix }}')" {{ $models->isEmpty() ? 'disabled' : '' }}>
                    <option value="">2. Select Car Model</option>
                    @foreach($models as $m)
                        <option value="{{ $m->slug }}" data-id="{{ $m->id }}" {{ (request('model') == $m->slug || ($selectedModel && $selectedModel->slug == $m->slug)) ? 'selected' : '' }}>
                            {{ $m->name }} ({{ $m->products_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Variant -->
            <div class="mb-2">
                <select name="variant" id="{{ $prefix }}filter_variant_select" class="form-select form-select-sm" {{ $variants->isEmpty() ? 'disabled' : '' }}>
                    <option value="">3. Select Variant / Trim</option>
                    @foreach($variants as $v)
                        <option value="{{ $v->slug }}" data-id="{{ $v->id }}" {{ (request('variant') == $v->slug || ($selectedVariant && $selectedVariant->slug == $v->slug)) ? 'selected' : '' }}>
                            {{ $v->name }} ({{ $v->products_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-theme-red btn-sm w-100 py-1 font-sm fw-bold">
                <i class="fa-solid fa-filter me-1"></i> Apply Vehicle Filter
            </button>
        </form>
    </div>

    <!-- 2. CATEGORIES FILTER -->
    <div class="mb-4">
        <div class="filter-section-title">
            <span><i class="fa-solid fa-shapes text-danger me-1"></i> Categories</span>
            @if(request('category'))
                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
            @endif
        </div>
        <div class="d-flex flex-column">
            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="filter-cat-item {{ !request('category') ? 'active' : '' }}">
                <span><i class="fa-solid fa-border-all me-2 text-muted" style="width: 14px;"></i> All Categories</span>
                <span class="badge bg-dark text-secondary border border-secondary border-opacity-25 rounded-pill">{{ $categories->sum('products_count') }}</span>
            </a>
            @foreach($categories as $cat)
                @php
                    $catIcon = 'fa-certificate';
                    if (str_contains($cat->slug, 'logo')) $catIcon = 'fa-shield-halved';
                    elseif (str_contains($cat->slug, 'monogram')) $catIcon = 'fa-bolt';
                    elseif (str_contains($cat->slug, 'letter') || str_contains($cat->slug, 'number')) $catIcon = 'fa-font';
                    elseif (str_contains($cat->slug, 'access')) $catIcon = 'fa-wrench';
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['category' => $cat->slug]) }}" class="filter-cat-item {{ request('category') == $cat->slug ? 'active' : '' }}">
                    <span><i class="fa-solid {{ $catIcon }} me-2 text-muted" style="width: 14px;"></i> {{ $cat->name }}</span>
                    <span class="badge bg-dark text-secondary border border-secondary border-opacity-25 rounded-pill">{{ $cat->products_count }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- 3. PRICE RANGE FILTER -->
    <div class="mb-4">
        <div class="filter-section-title">
            <span><i class="fa-solid fa-indian-rupee-sign text-danger me-1"></i> Price Range</span>
            @if(request('min_price') || request('max_price'))
                <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
            @endif
        </div>

        <!-- Quick Price Presets -->
        <div class="filter-price-preset-grid">
            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="filter-price-preset {{ !request('min_price') && !request('max_price') ? 'active' : '' }}">
                All
            </a>
            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => 500]) }}" class="filter-price-preset {{ request('max_price') == 500 && !request('min_price') ? 'active' : '' }}">
                Under &#x20B9;500
            </a>
            <a href="{{ request()->fullUrlWithQuery(['min_price' => 500, 'max_price' => 1000]) }}" class="filter-price-preset {{ request('min_price') == 500 && request('max_price') == 1000 ? 'active' : '' }}">
                &#x20B9;500 - 1K
            </a>
            <a href="{{ request()->fullUrlWithQuery(['min_price' => 1000, 'max_price' => 2000]) }}" class="filter-price-preset {{ request('min_price') == 1000 && request('max_price') == 2000 ? 'active' : '' }}">
                &#x20B9;1K - 2K
            </a>
            <a href="{{ request()->fullUrlWithQuery(['min_price' => 2000, 'max_price' => null]) }}" class="filter-price-preset {{ request('min_price') == 2000 && !request('max_price') ? 'active' : '' }}">
                &#x20B9;2,000+
            </a>
        </div>

        <!-- Custom Price Range Inputs -->
        <form action="{{ route('shop') }}" method="GET" class="mt-2">
            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
            @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
            @if(request('model')) <input type="hidden" name="model" value="{{ request('model') }}"> @endif
            @if(request('variant')) <input type="hidden" name="variant" value="{{ request('variant') }}"> @endif
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            @if(request('placement')) <input type="hidden" name="placement" value="{{ request('placement') }}"> @endif
            @if(request('finish')) <input type="hidden" name="finish" value="{{ request('finish') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

            <div class="d-flex gap-2 align-items-center mb-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-secondary">&#x20B9;</span>
                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}" min="0">
                </div>
                <span class="text-secondary font-sm">-</span>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-secondary">&#x20B9;</span>
                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}" min="0">
                </div>
            </div>
            <button type="submit" class="btn btn-outline-danger btn-sm w-100 font-sm py-1 fw-bold">
                Apply Price
            </button>
        </form>
    </div>

    <!-- 4. FINISH & COLOR SWATCH FILTER -->
    <div class="mb-4">
        <div class="filter-section-title">
            <span><i class="fa-solid fa-palette text-danger me-1"></i> Finish / Color</span>
            @if(request('finish'))
                <a href="{{ request()->fullUrlWithQuery(['finish' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
            @endif
        </div>
        <div class="filter-swatch-grid">
            @php
                $finishes = [
                    ['id' => 'Chrome', 'name' => 'Chrome', 'dot' => 'background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 50%, #94a3b8 100%);'],
                    ['id' => 'Gloss Black', 'name' => 'Piano Black', 'dot' => 'background: #0b0f19; box-shadow: inset 0 1px 1px rgba(255,255,255,0.4);'],
                    ['id' => 'Matte Black', 'name' => 'Matte Black', 'dot' => 'background: #334155;'],
                    ['id' => 'Red', 'name' => 'Racing Red', 'dot' => 'background: #d90429;'],
                    ['id' => 'Gold', 'name' => 'Royal Gold', 'dot' => 'background: linear-gradient(135deg, #fef08a 0%, #eab308 100%);'],
                ];
            @endphp
            @foreach($finishes as $f)
                <a href="{{ request('finish') == $f['id'] ? request()->fullUrlWithQuery(['finish' => null]) : request()->fullUrlWithQuery(['finish' => $f['id']]) }}" class="filter-swatch-pill {{ request('finish') == $f['id'] ? 'active' : '' }}">
                    <span class="swatch-dot" style="{{ $f['dot'] }}"></span>
                    <span>{{ $f['name'] }}</span>
                    @if(request('finish') == $f['id'])
                        <i class="fa-solid fa-check ms-1 font-sm"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- 5. PLACEMENT FILTER -->
    <div class="mb-2">
        <div class="filter-section-title">
            <span><i class="fa-solid fa-arrows-to-dot text-danger me-1"></i> Placement</span>
            @if(request('placement'))
                <a href="{{ request()->fullUrlWithQuery(['placement' => null]) }}" class="text-danger small text-decoration-none fw-normal" style="font-size: 11px;">Clear</a>
            @endif
        </div>
        <div class="filter-placement-grid">
            @php
                $placements = [
                    ['id' => '', 'name' => 'All', 'icon' => 'fa-border-all'],
                    ['id' => 'Front', 'name' => 'Front Grille', 'icon' => 'fa-shield-halved'],
                    ['id' => 'Rear', 'name' => 'Rear Boot / Trunk', 'icon' => 'fa-car-rear'],
                    ['id' => 'Side', 'name' => 'Side Fender', 'icon' => 'fa-car-side'],
                ];
            @endphp
            @foreach($placements as $p)
                @if($p['id'] == '')
                    <a href="{{ request()->fullUrlWithQuery(['placement' => null]) }}" class="filter-placement-pill {{ !request('placement') ? 'active' : '' }}">
                        <i class="fa-solid {{ $p['icon'] }} me-1 text-muted"></i> {{ $p['name'] }}
                    </a>
                @else
                    <a href="{{ request('placement') == $p['id'] ? request()->fullUrlWithQuery(['placement' => null]) : request()->fullUrlWithQuery(['placement' => $p['id']]) }}" class="filter-placement-pill {{ request('placement') == $p['id'] ? 'active' : '' }}">
                        <i class="fa-solid {{ $p['icon'] }} me-1 text-muted"></i> {{ $p['name'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    function handleBrandChange(selectElem, prefix) {
        const brandSlug = selectElem.value;
        const brandOpt = selectElem.options[selectElem.selectedIndex];
        const brandId = brandOpt ? (brandOpt.dataset.id || brandOpt.value) : brandSlug;
        const modelSelect = document.getElementById(prefix + 'filter_model_select');
        const variantSelect = document.getElementById(prefix + 'filter_variant_select');

        if (!modelSelect) return;

        modelSelect.innerHTML = '<option value="">2. Select Car Model</option>';
        modelSelect.disabled = true;

        if (variantSelect) {
            variantSelect.innerHTML = '<option value="">3. Select Variant / Trim</option>';
            variantSelect.disabled = true;
        }

        if (window.jQuery) {
            $(modelSelect).prop('disabled', true).val('').trigger('change.select2');
            if (variantSelect) $(variantSelect).prop('disabled', true).val('').trigger('change.select2');
        }

        if (!brandSlug && !brandId) return;

        modelSelect.innerHTML = '<option value="">Loading models...</option>';
        if (window.jQuery) $(modelSelect).trigger('change.select2');

        const lookup = brandId || brandSlug;
        fetch(`/ajax/models-by-brand/${lookup}`)
            .then(res => res.json())
            .then(resData => {
                const models = Array.isArray(resData) ? resData : (resData && resData.data ? resData.data : []);
                modelSelect.innerHTML = '<option value="">2. Select Car Model</option>';
                if (models && models.length > 0) {
                    models.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.slug;
                        opt.dataset.id = m.id;
                        const countText = (m.products_count !== undefined && m.products_count !== null) ? ` (${m.products_count})` : '';
                        opt.textContent = m.name + countText;
                        modelSelect.appendChild(opt);
                    });
                    modelSelect.disabled = false;
                } else {
                    modelSelect.innerHTML = '<option value="">No models available</option>';
                }
                if (window.jQuery) {
                    $(modelSelect).prop('disabled', modelSelect.disabled).val('').trigger('change.select2');
                }
            })
            .catch(err => {
                console.error('Error fetching models:', err);
                modelSelect.innerHTML = '<option value="">Failed to load models</option>';
                if (window.jQuery) $(modelSelect).trigger('change.select2');
            });
    }

    function handleModelChange(selectElem, prefix) {
        const modelSlug = selectElem.value;
        const modelOpt = selectElem.options[selectElem.selectedIndex];
        const modelId = modelOpt ? (modelOpt.dataset.id || modelOpt.value) : modelSlug;
        const variantSelect = document.getElementById(prefix + 'filter_variant_select');

        if (!variantSelect) return;

        variantSelect.innerHTML = '<option value="">3. Select Variant / Trim</option>';
        variantSelect.disabled = true;

        if (window.jQuery) {
            $(variantSelect).prop('disabled', true).val('').trigger('change.select2');
        }

        if (!modelSlug && !modelId) return;

        variantSelect.innerHTML = '<option value="">Loading variants...</option>';
        if (window.jQuery) $(variantSelect).trigger('change.select2');

        const lookup = modelId || modelSlug;
        fetch(`/ajax/variants-by-model/${lookup}`)
            .then(res => res.json())
            .then(resData => {
                const variants = Array.isArray(resData) ? resData : (resData && resData.data ? resData.data : []);
                variantSelect.innerHTML = '<option value="">3. Select Variant / Trim</option>';
                if (variants && variants.length > 0) {
                    variants.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.slug;
                        opt.dataset.id = v.id;
                        const countText = (v.products_count !== undefined && v.products_count !== null) ? ` (${v.products_count})` : '';
                        opt.textContent = v.name + countText;
                        variantSelect.appendChild(opt);
                    });
                    variantSelect.disabled = false;
                } else {
                    variantSelect.innerHTML = '<option value="">All Variants (No trims)</option>';
                }
                if (window.jQuery) {
                    $(variantSelect).prop('disabled', variantSelect.disabled).val('').trigger('change.select2');
                }
            })
            .catch(err => {
                console.error('Error fetching variants:', err);
                variantSelect.innerHTML = '<option value="">Failed to load variants</option>';
                if (window.jQuery) $(variantSelect).trigger('change.select2');
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.vehicle-filter-form').forEach(function(form) {
            form.addEventListener('submit', function() {
                form.querySelectorAll('select').forEach(function(sel) {
                    if (!sel.value) {
                        sel.disabled = true;
                    }
                });
            });
        });
    });
</script>
@endpush
