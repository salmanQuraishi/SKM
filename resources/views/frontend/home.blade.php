@extends('layouts.app')

@section('title', 'AutoLogos - Genuine Car Logos, Monograms & Emblems')

@section('content')

<!-- 1. HERO SECTION & CATEGORY SIDEBAR -->
<section class="hero-section py-3" style="background: #090d16;">
    <div class="container">
        <div class="row g-3 align-items-start">
            <!-- Left Category & Vehicle Mega Sidebar -->
            <div class="col-lg-3 d-none d-lg-block position-relative" style="z-index: 1050;">
                @include('frontend.partials.vehicle_mega_menu')
            </div>

            <!-- Hero Banner Content -->
            <div class="col-lg-9">
                @php $banner = $heroBanners->first(); @endphp
                <div class="position-relative overflow-hidden rounded-3 p-4 p-md-5 d-flex align-items-center" style="background: radial-gradient(circle at 80% 50%, #172033 0%, #090d16 100%); border: 1px solid #1e293b; min-height: 380px;">
                    <div class="row align-items-center w-100 position-relative" style="z-index: 2;">
                        <div class="col-md-7 text-white">
                            <h1 class="fw-black brand-font text-uppercase mb-2" style="font-size: calc(1.5rem + 1.2vw); line-height: 1.15; letter-spacing: -0.5px;">
                                Genuine Car Logos, <span class="text-danger">Monograms</span> & Emblems
                            </h1>
                            <p class="text-secondary fw-semibold mb-4" style="font-size: 14px; letter-spacing: 1px;">FOR ALL BRANDS & MODELS</p>

                            <ul class="list-unstyled mb-4">
                                <li class="mb-2 d-flex align-items-center"><i class="fa-solid fa-check text-white me-2"></i> 100% Genuine Products</li>
                                <li class="mb-2 d-flex align-items-center"><i class="fa-solid fa-check text-white me-2"></i> Perfect Fit For Your Car</li>
                                <li class="mb-2 d-flex align-items-center"><i class="fa-solid fa-check text-white me-2"></i> Premium Chrome Finish</li>
                                <li class="mb-2 d-flex align-items-center"><i class="fa-solid fa-check text-white me-2"></i> Easy Returns & Refunds</li>
                            </ul>

                            <a href="{{ route('shop') }}" class="btn btn-theme-red px-4 py-3 fs-6 d-inline-flex align-items-center gap-2 shadow">
                                <span>SHOP NOW</span>
                                <i class="fa-solid fa-chevron-right fs-6"></i>
                            </a>
                        </div>

                        <div class="col-md-5 text-center mt-4 mt-md-0">
                            <!-- Hero Car Graphic -->
                            <img src="{{ asset('images/hero_car.svg') }}" alt="AutoLogos Automotive Emblems" class="img-fluid drop-shadow" style="max-height: 280px; filter: drop-shadow(0 15px 25px rgba(0,0,0,0.8));">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- QUICK VEHICLE SELECTOR WIDGET -->
<section class="py-3" style="background: #0d1322; border-bottom: 1px solid #1e293b;">
    <div class="container">
        <div class="p-3 p-md-4 rounded-3 shadow-lg" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border: 1px solid #374151;">
            <div class="row align-items-center g-3">
                <div class="col-lg-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger text-white flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-car-side fs-4"></i>
                        </div>
                        <div>
                            <div class="text-white fw-bold text-uppercase brand-font" style="letter-spacing: 0.5px;">Find Your Vehicle</div>
                            <div class="text-secondary" style="font-size: 12px;">Company &rarr; Model &rarr; Variant</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <form id="vehicleQuickSearchForm" class="row g-2">
                        <div class="col-md-4">
                            <select id="home_company_select" class="form-select bg-dark text-white border-secondary" style="font-size: 13px;">
                                <option value="">1. Select Company</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" data-slug="{{ $b->slug }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="home_model_select" class="form-select bg-dark text-white border-secondary" style="font-size: 13px;" disabled>
                                <option value="">2. Select Car Model</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="home_variant_select" class="form-select bg-dark text-white border-secondary" style="font-size: 13px;" disabled>
                                <option value="">3. Select Variant</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="col-lg-2 d-grid">
                    <button type="button" id="btnFindVehicleProducts" class="btn btn-theme-red fw-bold d-flex align-items-center justify-content-center gap-2 py-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>FIND PRODUCTS</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. TRUST / USP BAR -->
<section class="usp-bar py-3" style="background: #090d16; border-top: 1px solid #1e293b; border-bottom: 1px solid #1e293b;">
    <div class="container">
        <div class="row g-3 text-white text-center text-md-start">
            <div class="col-6 col-md-4 col-lg-2 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                <i class="fa-solid fa-shield-halved text-danger fs-3"></i>
                <div>
                    <div class="fw-bold" style="font-size: 12px; text-transform: uppercase;">100% Genuine</div>
                    <div class="text-secondary" style="font-size: 11px;">Original Products</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                <i class="fa-solid fa-award text-danger fs-3"></i>
                <div>
                    <div class="fw-bold" style="font-size: 12px; text-transform: uppercase;">Perfect Fit</div>
                    <div class="text-secondary" style="font-size: 11px;">For Your Car</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                <i class="fa-solid fa-gem text-danger fs-3"></i>
                <div>
                    <div class="fw-bold" style="font-size: 12px; text-transform: uppercase;">Premium Quality</div>
                    <div class="text-secondary" style="font-size: 11px;">Chrome Finish</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                <i class="fa-solid fa-rotate-left text-danger fs-3"></i>
                <div>
                    <div class="fw-bold" style="font-size: 12px; text-transform: uppercase;">Easy Returns</div>
                    <div class="text-secondary" style="font-size: 11px;">7 Days Easy Returns</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                <i class="fa-solid fa-credit-card text-danger fs-3"></i>
                <div>
                    <div class="fw-bold" style="font-size: 12px; text-transform: uppercase;">Secure Payment</div>
                    <div class="text-secondary" style="font-size: 11px;">100% Safe & Secure</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                <i class="fa-solid fa-truck-fast text-danger fs-3"></i>
                <div>
                    <div class="fw-bold" style="font-size: 12px; text-transform: uppercase;">Fast Delivery</div>
                    <div class="text-secondary" style="font-size: 11px;">Across India</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. SHOP BY BRAND -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold brand-font text-uppercase mb-0" style="font-size: 20px;">
                    SHOP BY <span class="text-danger">BRAND / COMPANY</span>
                </h3>
                <small class="text-muted">Choose your vehicle company to view compatible models & variants</small>
            </div>
            <a href="{{ route('vehicles') }}" class="text-danger fw-bold text-decoration-none font-sm d-flex align-items-center gap-1">
                VIEW ALL VEHICLES <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-6 g-3">
            @foreach($brands as $b)
                <div class="col">
                    <a href="{{ route('vehicle.cars', $b->slug) }}" class="text-decoration-none">
                        <div class="card h-100 text-center border p-2 shadow-sm rounded-3 brand-card" style="transition: all 0.2s;">
                            <div class="d-flex align-items-center justify-content-center p-2" style="height: 60px;">
                                <img src="{{ asset($b->logo ?: 'images/brands/' . $b->slug . '.svg') }}" alt="{{ $b->name }}" style="max-height: 45px; max-width: 80px; object-fit: contain;">
                            </div>
                            <div class="fw-bold text-dark mt-1" style="font-size: 12px;">{{ $b->name }}</div>
                            <div class="text-danger fw-semibold" style="font-size: 10px;">Select Models &rarr;</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 4. POPULAR CAR LOGOS & EMBLEMS -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold brand-font text-uppercase mb-0" style="font-size: 20px;">
                POPULAR <span class="text-danger">CAR LOGOS & EMBLEMS</span>
            </h3>
            <a href="{{ route('shop', ['category' => 'car-logos']) }}" class="text-danger fw-bold text-decoration-none font-sm d-flex align-items-center gap-1">
                VIEW ALL PRODUCTS <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-3">
            @foreach($popularProducts as $prod)
                <div class="col">
                    <div class="product-card">
                        <div class="img-wrapper">
                            <a href="{{ route('product.detail', $prod->slug) }}">
                                <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                            </a>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('product.detail', $prod->slug) }}" class="product-title">
                                {{ $prod->name }}
                            </a>
                            <div class="product-subtitle">{{ $prod->compatible_models_text ?: 'All Models' }}</div>

                            <div class="mt-auto pt-2 border-top">
                                <div class="d-flex align-items-baseline mb-2">
                                    <span class="price-tag">&#x20B9;{{ number_format($prod->active_price, 0) }}</span>
                                    @if($prod->has_discount)
                                        <span class="old-price">&#x20B9;{{ number_format($prod->regular_price, 0) }}</span>
                                    @endif
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" onclick="addToCartAjax({{ $prod->id }})" class="btn btn-theme-red w-100 py-2 d-flex align-items-center justify-content-center gap-1 font-sm">
                                        <span>ADD TO CART</span>
                                    </button>
                                    <button type="button" onclick="toggleWishlist({{ $prod->id }}, this)" class="btn btn-outline-secondary px-2 py-2" title="Add to Wishlist">
                                        <i class="{{ in_array($prod->id, $globalWishlistProductIds ?? []) ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart' }}"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Carousel Dots Indicator Mockup -->
        <div class="d-flex justify-content-center gap-2 mt-4">
            <span class="badge rounded-pill bg-danger" style="width: 25px; height: 6px;"></span>
            <span class="badge rounded-pill bg-secondary" style="width: 12px; height: 6px;"></span>
            <span class="badge rounded-pill bg-secondary" style="width: 12px; height: 6px;"></span>
        </div>
    </div>
</section>

<!-- 5. CAR MONOGRAMS (NEW ARRIVALS) -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold brand-font text-uppercase mb-0" style="font-size: 20px;">
                    CAR MONOGRAMS
                </h3>
                <span class="badge bg-danger text-uppercase px-2 py-1" style="font-size: 10px;">NEW ARRIVALS</span>
            </div>
            <a href="{{ route('shop', ['type' => 'monogram']) }}" class="text-danger fw-bold text-decoration-none font-sm d-flex align-items-center gap-1">
                VIEW ALL MONOGRAMS <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-8 g-3">
            @foreach($monograms as $mono)
                <div class="col">
                    <a href="{{ route('product.detail', $mono->slug) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm rounded-3 text-center overflow-hidden" style="background: #111827; transition: transform 0.2s;">
                            <div class="p-3 d-flex align-items-center justify-content-center" style="height: 100px;">
                                <img src="{{ $mono->primary_image_url }}" alt="{{ $mono->name }}" style="max-height: 70px; max-width: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                            </div>
                            <div class="card-footer bg-dark border-0 p-2">
                                <div class="text-white fw-bold text-truncate" style="font-size: 12px;">{{ $mono->name }}</div>
                                <div class="text-danger fw-bold" style="font-size: 12px;">&#x20B9;{{ number_format($mono->active_price, 0) }}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 6. SHOP BY MODEL -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold brand-font text-uppercase mb-0" style="font-size: 20px;">
                SHOP BY <span class="text-danger">MODEL</span>
            </h3>
            <a href="{{ route('shop') }}" class="text-danger fw-bold text-decoration-none font-sm d-flex align-items-center gap-1">
                VIEW ALL MODELS <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-5 row-cols-lg-10 g-2">
            @foreach($carModels as $m)
                <div class="col">
                    <a href="{{ route('vehicle.variants', [$m->brand->slug ?? 'all', $m->slug]) }}" class="text-decoration-none">
                        <div class="p-2 text-center rounded-2 shadow-sm model-badge-card" style="background: #0f172a; border: 1px solid #1e293b; transition: all 0.2s;">
                            <div class="brand-font fw-black text-white fst-italic" style="font-size: 14px; letter-spacing: 2px;">
                                {{ strtoupper($m->name) }}
                            </div>
                            <div class="text-secondary" style="font-size: 9px;">SELECT VARIANT &rarr;</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 7. PROMOTIONAL BANNER ("CAN'T FIND YOUR LOGO?") -->
<section class="py-5" style="background: #ffffff; border-top: 1px solid #e2e8f0;">
    <div class="container">
        <div class="p-4 p-md-5 rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #090d16 0%, #1e293b 100%);">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white mb-4 mb-lg-0">
                    <h2 class="fw-black brand-font mb-2 text-uppercase" style="font-size: 28px;">
                        CAN'T FIND <span class="text-danger">YOUR LOGO</span>?
                    </h2>
                    <p class="text-secondary mb-4" style="font-size: 14px;">
                        We have 1000+ logos, monograms and emblems for all cars. If you can't find yours, just contact us and our specialists will source it for you!
                    </p>
                    <a href="{{ route('contact') }}" class="btn btn-theme-red px-4 py-3 fw-bold">
                        REQUEST YOUR LOGO
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="row g-3 text-center text-white">
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.05);">
                                <i class="fa-solid fa-shield-halved text-danger fs-3 mb-2"></i>
                                <div class="fw-bold" style="font-size: 12px;">Secure Payment</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.05);">
                                <i class="fa-solid fa-box-archive text-danger fs-3 mb-2"></i>
                                <div class="fw-bold" style="font-size: 12px;">7 Days Easy Returns</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.05);">
                                <i class="fa-solid fa-certificate text-danger fs-3 mb-2"></i>
                                <div class="fw-bold" style="font-size: 12px;">100% Genuine Products</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.05);">
                                <i class="fa-solid fa-headset text-danger fs-3 mb-2"></i>
                                <div class="fw-bold" style="font-size: 12px;">Dedicated Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
    .brand-card:hover {
        transform: translateY(-4px);
        border-color: var(--primary-red) !important;
        box-shadow: 0 10px 15px -3px rgba(217, 4, 41, 0.15) !important;
    }
    .model-badge-card:hover {
        background: var(--primary-red) !important;
        border-color: var(--primary-red) !important;
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('home_company_select');
    const modelSelect = document.getElementById('home_model_select');
    const variantSelect = document.getElementById('home_variant_select');
    const btnFind = document.getElementById('btnFindVehicleProducts');

    if (companySelect && modelSelect && variantSelect && btnFind) {
        companySelect.addEventListener('change', function() {
            const brandId = this.value;
            modelSelect.innerHTML = '<option value="">2. Select Car Model</option>';
            modelSelect.disabled = true;
            variantSelect.innerHTML = '<option value="">3. Select Variant</option>';
            variantSelect.disabled = true;

            if (window.jQuery) {
                $(modelSelect).prop('disabled', true).val('').trigger('change.select2');
                $(variantSelect).prop('disabled', true).val('').trigger('change.select2');
            }

            if (!brandId) return;

            modelSelect.innerHTML = '<option value="">Loading models...</option>';
            if (window.jQuery) $(modelSelect).trigger('change.select2');

            fetch(`/ajax/models-by-brand/${brandId}`)
                .then(res => res.json())
                .then(resData => {
                    const models = Array.isArray(resData) ? resData : (resData && resData.data ? resData.data : []);
                    modelSelect.innerHTML = '<option value="">2. Select Car Model</option>';
                    if (models && models.length > 0) {
                        models.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            opt.dataset.slug = m.slug;
                            opt.textContent = m.name;
                            modelSelect.appendChild(opt);
                        });
                        modelSelect.disabled = false;
                    } else {
                        modelSelect.innerHTML = '<option value="">No models found</option>';
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
        });

        modelSelect.addEventListener('change', function() {
            const modelId = this.value;
            variantSelect.innerHTML = '<option value="">3. Select Variant</option>';
            variantSelect.disabled = true;

            if (window.jQuery) $(variantSelect).prop('disabled', true).val('').trigger('change.select2');

            if (!modelId) return;

            variantSelect.innerHTML = '<option value="">Loading variants...</option>';
            if (window.jQuery) $(variantSelect).trigger('change.select2');

            fetch(`/ajax/variants-by-model/${modelId}`)
                .then(res => res.json())
                .then(resData => {
                    const variants = Array.isArray(resData) ? resData : (resData && resData.data ? resData.data : []);
                    variantSelect.innerHTML = '<option value="">3. Select Variant</option>';
                    if (variants && variants.length > 0) {
                        variants.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.id;
                            opt.dataset.slug = v.slug;
                            opt.textContent = v.name;
                            variantSelect.appendChild(opt);
                        });
                        variantSelect.disabled = false;
                    } else {
                        variantSelect.innerHTML = '<option value="">No variants found</option>';
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
        });

        btnFind.addEventListener('click', function() {
            const companyOpt = companySelect.options[companySelect.selectedIndex];
            const modelOpt = modelSelect.options[modelSelect.selectedIndex];
            const variantOpt = variantSelect.options[variantSelect.selectedIndex];

            const companySlug = companyOpt ? companyOpt.dataset.slug : null;
            const carSlug = modelOpt ? modelOpt.dataset.slug : null;
            const variantSlug = variantOpt ? variantOpt.dataset.slug : null;

            if (companySlug && carSlug && variantSlug) {
                window.location.href = `/vehicle/${companySlug}/${carSlug}/${variantSlug}`;
            } else if (companySlug && carSlug) {
                window.location.href = `/vehicle/${companySlug}/${carSlug}`;
            } else if (companySlug) {
                window.location.href = `/vehicle/${companySlug}`;
            } else {
                window.location.href = `{{ route('vehicles') }}`;
            }
        });
    }
});
</script>
@endpush
