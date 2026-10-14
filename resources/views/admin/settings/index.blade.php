@extends('layouts.admin')

@section('title', 'Company Details & Store Settings - AutoLogos')
@section('page_title', 'Company Details & Store Settings')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa-solid fa-building-circle-check text-danger me-2"></i> Company Profile & Store Settings
            </h4>
            <p class="text-muted small mb-0">Manage your company branding, contact numbers, email, physical address, official logo, and site favicon.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-danger px-3 py-2 fw-semibold shadow-sm btn-clear-cache" title="Clear application cache & temporary cookies">
                <i class="fa-solid fa-broom me-2"></i> Clear Cache & Cookies
            </button>
            <button type="submit" form="settingsForm" class="btn btn-theme-red px-4 py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save All Changes
            </button>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
        @csrf
        @method('PUT')

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm border" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold font-sm" id="company-tab" data-bs-toggle="pill" data-bs-target="#company-pane" type="button" role="tab">
                    <i class="fa-solid fa-id-card me-2 text-danger"></i> Company Identity & Logo
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold font-sm" id="contact-tab" data-bs-toggle="pill" data-bs-target="#contact-pane" type="button" role="tab">
                    <i class="fa-solid fa-address-book me-2 text-primary"></i> Contact & Address
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold font-sm" id="social-tab" data-bs-toggle="pill" data-bs-target="#social-pane" type="button" role="tab">
                    <i class="fa-brands fa-whatsapp me-2 text-success"></i> Social & WhatsApp
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold font-sm" id="shipping-tab" data-bs-toggle="pill" data-bs-target="#shipping-pane" type="button" role="tab">
                    <i class="fa-solid fa-truck-fast me-2 text-warning"></i> Shipping & COD
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold font-sm" id="payment-tab" data-bs-toggle="pill" data-bs-target="#payment-pane" type="button" role="tab">
                    <i class="fa-solid fa-credit-card me-2 text-danger"></i> Payment Methods (Online & COD)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold font-sm" id="system-tab" data-bs-toggle="pill" data-bs-target="#system-pane" type="button" role="tab">
                    <i class="fa-solid fa-broom me-2 text-danger"></i> Cache & Optimization
                </button>
            </li>
        </ul>

        <div class="tab-content" id="settingsTabContent">
            <!-- ================= TAB 1: COMPANY IDENTITY & LOGO ================= -->
            <div class="tab-pane fade show active" id="company-pane" role="tabpanel">
                <div class="row g-4">
                    <!-- Brand Names & Tax Info -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="fa-solid fa-building text-danger me-2"></i> Company Name & Details
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">
                                        Company / Store Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="store_name" class="form-control" value="{{ \App\Models\Setting::get('store_name', 'AutoLogos') }}" required placeholder="e.g. AutoLogos Automotive Hub">
                                    <small class="text-muted">Displayed in header, invoices, packing slips, and footer.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">Tagline / Subtitle</label>
                                    <input type="text" name="store_tagline" class="form-control" value="{{ \App\Models\Setting::get('store_tagline', 'CAR LOGOS & EMBLEMS') }}" placeholder="e.g. Genuine Car Logos, Monograms & Emblems">
                                    <small class="text-muted">Appears below the logo text in the header and footer.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">GSTIN / Business Registration Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-solid fa-receipt text-secondary"></i></span>
                                        <input type="text" name="gstin" class="form-control text-uppercase font-monospace" value="{{ \App\Models\Setting::get('gstin', '07AAAAA0000A1Z5') }}" placeholder="e.g. 07AAAAA0000A1Z5">
                                    </div>
                                    <small class="text-muted">Printed on GST tax invoices and contact page.</small>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold small text-dark">Header Announcement Bar Text</label>
                                    <textarea name="header_announcement_text" class="form-control" rows="3" placeholder="Top banner announcement text...">{{ \App\Models\Setting::get('header_announcement_text', \App\Models\Setting::get('announcement_bar_text', 'Free Pan-India Delivery on orders above ₹999 | 100% Genuine Fitment Guarantee')) }}</textarea>
                                    <small class="text-muted">Shown in top notification bar on all frontend pages.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo & Favicon Upload -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="fa-solid fa-image text-danger me-2"></i> Store Logo & Favicon
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <!-- Logo Box -->
                                <div class="p-3 bg-light rounded-3 border mb-4">
                                    <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center">
                                        <span><i class="fa-solid fa-shapes text-danger me-1"></i> Company Brand Logo</span>
                                        <span class="badge bg-secondary font-monospace">PNG, SVG, WEBP</span>
                                    </label>

                                    @php
                                        $currentLogo = \App\Models\Setting::get('site_logo');
                                        $hasLogo = $currentLogo && file_exists(public_path($currentLogo));
                                    @endphp

                                    <div class="row align-items-center g-3">
                                        <div class="col-sm-5 text-center">
                                            <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-center" style="height: 90px;">
                                                <img id="logoPreviewImg" src="{{ $hasLogo ? asset($currentLogo) : asset('images/dummy-product.svg') }}" alt="Logo Preview" style="max-height: 75px; max-width: 100%; object-fit: contain;">
                                            </div>
                                            <div class="small text-muted mt-1" id="logoStatusText">
                                                {{ $hasLogo ? 'Active Custom Logo' : 'Default Text Logo' }}
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="file" name="site_logo" id="siteLogoInput" class="form-control form-control-sm mb-2" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                                            <div class="form-text small mb-2">Recommended: Transparent background, Height ~50px to 80px (Max: 4MB).</div>
                                            @if($hasLogo)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_site_logo" value="1" id="removeLogoCheck">
                                                    <label class="form-check-label text-danger small fw-semibold" for="removeLogoCheck">
                                                        <i class="fa-solid fa-trash-can me-1"></i> Remove custom logo (Revert to text)
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Favicon Box -->
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center">
                                        <span><i class="fa-solid fa-globe text-primary me-1"></i> Browser Tab Favicon</span>
                                        <span class="badge bg-secondary font-monospace">ICO, PNG, SVG</span>
                                    </label>

                                    @php
                                        $currentFavicon = \App\Models\Setting::get('site_favicon');
                                        $hasFavicon = $currentFavicon && file_exists(public_path($currentFavicon));
                                    @endphp

                                    <div class="row align-items-center g-3">
                                        <div class="col-sm-5">
                                            <!-- Browser Mockup -->
                                            <div class="border rounded-2 bg-white shadow-sm p-2" style="font-size: 11px;">
                                                <div class="d-flex align-items-center bg-light px-2 py-1 rounded border">
                                                    <img id="faviconPreviewImg" src="{{ $hasFavicon ? asset($currentFavicon) : asset('images/dummy-product.svg') }}" alt="Favicon" style="width: 16px; height: 16px; object-fit: contain;" class="me-2">
                                                    <span class="text-truncate text-secondary fw-semibold">AutoLogos - Genuine...</span>
                                                </div>
                                            </div>
                                            <div class="text-center small text-muted mt-1">
                                                {{ $hasFavicon ? 'Active Favicon' : 'Default Favicon' }}
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="file" name="site_favicon" id="siteFaviconInput" class="form-control form-control-sm mb-2" accept="image/x-icon,image/png,image/svg+xml,image/webp,image/jpeg">
                                            <div class="form-text small mb-2">Square format recommended (32x32px or 64x64px, Max: 1MB).</div>
                                            @if($hasFavicon)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_site_favicon" value="1" id="removeFaviconCheck">
                                                    <label class="form-check-label text-danger small fw-semibold" for="removeFaviconCheck">
                                                        <i class="fa-solid fa-trash-can me-1"></i> Remove custom favicon
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 2: CONTACT & ADDRESS ================= -->
            <div class="tab-pane fade" id="contact-pane" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="fa-solid fa-phone-volume text-primary me-2"></i> Phone Numbers & Communication
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">
                                        Primary Mobile / WhatsApp Number <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-solid fa-mobile-screen text-danger"></i></span>
                                        <input type="text" name="store_mobile" class="form-control" value="{{ \App\Models\Setting::get('store_mobile', \App\Models\Setting::get('store_phone', '+91 98765 43210')) }}" placeholder="+91 98765 43210" required>
                                    </div>
                                    <small class="text-muted">Primary contact displayed on header, contact page & order confirmations.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">Alternate Phone / Landline Hotline</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-solid fa-phone text-secondary"></i></span>
                                        <input type="text" name="store_phone" class="form-control" value="{{ \App\Models\Setting::get('store_phone', '+91 98765 43210') }}" placeholder="+91 11 2345 6789">
                                    </div>
                                    <small class="text-muted">Secondary customer support line.</small>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold small text-dark">
                                        Official Support Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-primary"></i></span>
                                        <input type="email" name="store_email" class="form-control" value="{{ \App\Models\Setting::get('store_email', 'info@autologos.in') }}" placeholder="support@autologos.in" required>
                                    </div>
                                    <small class="text-muted">Used for customer inquiries, invoice communications & footer info.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="fa-solid fa-location-dot text-danger me-2"></i> Physical Address & Warehouse
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">
                                        Registered Warehouse / Store Address <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="store_address" class="form-control" rows="5" required placeholder="Shop/Building No., Street, Area, City, State, PIN Code">{{ \App\Models\Setting::get('store_address', "123, Auto Street, New Delhi, India - 110001") }}</textarea>
                                    <small class="text-muted">Printed on customer GST invoices, dispatch labels and shipping slips.</small>
                                </div>

                                <div class="alert alert-light border p-3 small mb-0">
                                    <i class="fa-solid fa-shield-halved text-success me-2"></i>
                                    <strong>Automatic Invoices:</strong> Whenever an invoice or receipt PDF is generated, this address and contact information will appear under the company header.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 3: SOCIAL & WHATSAPP ================= -->
            <div class="tab-pane fade" id="social-pane" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="fa-brands fa-whatsapp text-success me-2"></i> WhatsApp Customer Support
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">WhatsApp Number for Customer Chat</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa-brands fa-whatsapp text-success fs-5"></i></span>
                                        <input type="text" name="whatsapp_support_number" class="form-control" value="{{ \App\Models\Setting::get('whatsapp_support_number', \App\Models\Setting::get('whatsapp_phone', '919876543210')) }}" placeholder="e.g. 919876543210 (Country code without +)">
                                    </div>
                                    <small class="text-muted">The floating WhatsApp button on frontend connects directly to this number.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">Meta Cloud API Phone ID (Optional)</label>
                                    <input type="text" name="whatsapp_phone_number_id" class="form-control font-monospace" value="{{ \App\Models\Setting::get('whatsapp_phone_number_id', '') }}" placeholder="Meta Business Cloud API Phone ID">
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold small text-dark">Meta Cloud API Permanent Token (Optional)</label>
                                    <input type="password" name="whatsapp_token" class="form-control font-monospace" value="{{ \App\Models\Setting::get('whatsapp_token', \App\Models\Setting::get('whatsapp_access_token', '')) }}" placeholder="Meta Cloud API Access Token">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="fa-solid fa-share-nodes text-danger me-2"></i> Social Media Profiles
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">Instagram Profile URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-danger"><i class="fa-brands fa-instagram"></i></span>
                                        <input type="url" name="instagram_url" class="form-control" value="{{ \App\Models\Setting::get('instagram_url', 'https://instagram.com') }}" placeholder="https://instagram.com/autologos">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">Facebook Page URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-primary"><i class="fa-brands fa-facebook-f"></i></span>
                                        <input type="url" name="facebook_url" class="form-control" value="{{ \App\Models\Setting::get('facebook_url', 'https://facebook.com') }}" placeholder="https://facebook.com/autologos">
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold small text-dark">YouTube Channel URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-danger"><i class="fa-brands fa-youtube"></i></span>
                                        <input type="url" name="youtube_url" class="form-control" value="{{ \App\Models\Setting::get('youtube_url', 'https://youtube.com') }}" placeholder="https://youtube.com/@autologos">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 4: SHIPPING & LOGISTICS ================= -->
            <div class="tab-pane fade" id="shipping-pane" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-truck text-danger me-2"></i> Shipping & Cash on Delivery (COD) Rules
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Standard Flat Shipping Fee (₹)</label>
                                <input type="number" step="0.01" name="shipping_flat_rate" class="form-control" value="{{ \App\Models\Setting::get('shipping_flat_rate', \App\Models\Setting::get('shipping_charge', 99)) }}">
                                <small class="text-muted">Standard rate applied to cart below free shipping threshold.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Free Shipping Threshold (₹)</label>
                                <input type="number" step="0.01" name="free_shipping_threshold" class="form-control" value="{{ \App\Models\Setting::get('free_shipping_threshold', 999) }}">
                                <small class="text-muted">Cart subtotal above which shipping becomes completely free.</small>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-bold small text-dark"><i class="fa-solid fa-truck-fast text-danger me-1"></i> Cash on Delivery (COD) & Online Payment Controls</div>
                                <div class="text-muted small">COD status and fee are centrally managed under the <a href="javascript:void(0)" onclick="bootstrap.Tab.getOrCreateInstance(document.getElementById('payment-tab')).show()" class="text-danger fw-bold text-decoration-underline">Payment Methods (Online & COD)</a> tab.</div>
                            </div>
                            <span class="badge {{ \App\Models\Setting::get('cod_enabled', \App\Models\Setting::get('enable_cod', '1')) == '1' ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }}">
                                COD: {{ \App\Models\Setting::get('cod_enabled', \App\Models\Setting::get('enable_cod', '1')) == '1' ? 'ACTIVE / OPEN' : 'CLOSED' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 5: PAYMENT GATEWAY & METHODS ================= -->
            <div class="tab-pane fade" id="payment-pane" role="tabpanel">
                @php
                    $rzpKey = \App\Models\Setting::get('razorpay_key_id', config('services.razorpay.key', env('RAZORPAY_KEY_ID', '')));
                    $rzpSecret = \App\Models\Setting::get('razorpay_key_secret', config('services.razorpay.secret', env('RAZORPAY_KEY_SECRET', '')));
                    $rzpMode = \App\Models\Setting::get('razorpay_mode', 'test');
                    $rzpActive = \App\Models\Setting::get('razorpay_enabled', \App\Models\Setting::get('enable_razorpay', '1')) == '1';
                    $codActive = \App\Models\Setting::get('cod_enabled', \App\Models\Setting::get('enable_cod', '1')) == '1';
                    $codCharge = (float)\App\Models\Setting::get('cod_charge', 50);
                    $isConfigured = !empty($rzpKey) && !empty($rzpSecret) && !str_contains($rzpKey, 'sample') && !str_contains($rzpKey, 'placeholder');
                @endphp

                <!-- 1. PAYMENT METHODS AVAILABILITY (ONLINE & COD SWITCHES) -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fa-solid fa-wallet text-danger me-2"></i> Payment Methods Availability (Checkout Controls)
                            </h6>
                            <small class="text-muted">Choose whether Online Payment and Cash on Delivery (COD) are Open or Closed for customers.</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span id="badge-online-header" class="badge {{ $rzpActive ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }}">
                                <i class="fa-solid {{ $rzpActive ? 'fa-circle-check' : 'fa-ban' }} me-1"></i> Online Payment: {{ $rzpActive ? 'OPEN' : 'CLOSED' }}
                            </span>
                            <span id="badge-cod-header" class="badge {{ $codActive ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }}">
                                <i class="fa-solid {{ $codActive ? 'fa-circle-check' : 'fa-ban' }} me-1"></i> COD: {{ $codActive ? 'OPEN' : 'CLOSED' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Online Payment Switch Card -->
                            <div class="col-lg-6">
                                <div class="p-4 rounded-3 border h-100 {{ $rzpActive ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light' }}" id="card-online-box">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-bolt text-danger fs-5"></i>
                                            <div>
                                                <span>Online Payment (UPI, Cards, NetBanking)</span>
                                                <div class="text-muted small fw-normal">Razorpay Secure Online Gateway</div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm {{ $rzpActive ? 'btn-outline-danger' : 'btn-success' }} px-3 py-1 fw-bold" onclick="togglePaymentViaAjax('online', this)">
                                            <i class="fa-solid {{ $rzpActive ? 'fa-power-off' : 'fa-check' }} me-1"></i> {{ $rzpActive ? 'Turn OFF' : 'Turn ON' }}
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-dark">Online Payment Status (Checkout)</label>
                                        <select name="razorpay_enabled" id="select_razorpay_enabled" class="form-select fw-semibold" onchange="syncPaymentCards()">
                                            <option value="1" {{ $rzpActive ? 'selected' : '' }}>🟢 OPEN / Active (Accept UPI, Cards, NetBanking at Checkout)</option>
                                            <option value="0" {{ !$rzpActive ? 'selected' : '' }}>🔴 CLOSED / Disabled (Hide Online Payment at Checkout)</option>
                                        </select>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-circle-info text-primary me-1"></i> When <strong>OPEN</strong>, customers can pay instantly via Google Pay, PhonePe, Paytm, BHIM, Cards, and NetBanking.
                                    </div>
                                </div>
                            </div>

                            <!-- Cash on Delivery Switch Card -->
                            <div class="col-lg-6">
                                <div class="p-4 rounded-3 border h-100 {{ $codActive ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light' }}" id="card-cod-box">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-truck-fast text-primary fs-5"></i>
                                            <div>
                                                <span>Cash On Delivery (COD)</span>
                                                <div class="text-muted small fw-normal">Pay Cash / UPI upon delivery</div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm {{ $codActive ? 'btn-outline-danger' : 'btn-success' }} px-3 py-1 fw-bold" onclick="togglePaymentViaAjax('cod', this)">
                                            <i class="fa-solid {{ $codActive ? 'fa-power-off' : 'fa-check' }} me-1"></i> {{ $codActive ? 'Turn OFF' : 'Turn ON' }}
                                        </button>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-sm-7">
                                            <label class="form-label fw-bold small text-dark">COD Status (Checkout)</label>
                                            <select name="cod_enabled" id="select_cod_enabled" class="form-select fw-semibold" onchange="syncPaymentCards()">
                                                <option value="1" {{ $codActive ? 'selected' : '' }}>🟢 OPEN / Active (Accept COD at Checkout)</option>
                                                <option value="0" {{ !$codActive ? 'selected' : '' }}>🔴 CLOSED / Disabled (Prepaid Orders Only / Hide COD)</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-5">
                                            <label class="form-label fw-bold small text-dark">COD Fee (₹)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">₹</span>
                                                <input type="number" step="0.01" name="cod_charge" class="form-control" value="{{ $codCharge }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-circle-info text-primary me-1"></i> When <strong>CLOSED</strong>, customers can only place prepaid orders via Online Payment.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. RAZORPAY API INTEGRATION & CONFIGURATION -->
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-credit-card text-danger me-2"></i> Razorpay API Gateway Credentials
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            @if($isConfigured)
                                @if($rzpMode === 'live' && str_starts_with($rzpKey, 'rzp_live_'))
                                    <span class="badge bg-primary text-white"><i class="fa-solid fa-bolt me-1"></i> Live Production Mode</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-flask me-1"></i> Test Sandbox Mode</span>
                                @endif
                            @else
                                <span class="badge bg-secondary text-white"><i class="fa-solid fa-info-circle me-1"></i> Demo / Sample Keys</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-light border border-secondary-subtle p-3 mb-4 rounded-3 d-flex align-items-start gap-3">
                            <i class="fa-solid fa-shield-halved text-danger fs-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Razorpay Live API Integration Setup</h6>
                                <p class="text-muted small mb-0">
                                    Your Razorpay API credentials connect the store checkout with Razorpay. View API Keys and Webhooks inside <a href="https://dashboard.razorpay.com" target="_blank" class="fw-bold text-danger text-decoration-underline">Razorpay Dashboard <i class="fa-solid fa-arrow-up-right-from-square small"></i></a>.
                                </p>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-dark">
                                    Environment / Operating Mode <span class="text-danger">*</span>
                                </label>
                                <select name="razorpay_mode" class="form-select">
                                    <option value="live" {{ $rzpMode === 'live' ? 'selected' : '' }}>🟢 Live Production (Use with Live API Keys: rzp_live_...)</option>
                                    <option value="test" {{ $rzpMode === 'test' ? 'selected' : '' }}>🟡 Test Sandbox (Use with Test API Keys: rzp_test_...)</option>
                                </select>
                                <small class="text-muted">Select 'Live Production' for real customer payments, or 'Test Sandbox' for simulations.</small>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">
                                    Razorpay Key ID <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted font-monospace"><i class="fa-solid fa-key"></i></span>
                                    <input type="text" name="razorpay_key_id" id="razorpayKeyId" class="form-control font-monospace" value="{{ \App\Models\Setting::get('razorpay_key_id', config('services.razorpay.key', env('RAZORPAY_KEY_ID', ''))) }}" placeholder="rzp_test_XXXXXXXXXXXXXX or rzp_live_XXXXXXXXXXXXXX">
                                </div>
                                <small class="text-muted">Public Key ID found under Razorpay Dashboard &rarr; Settings &rarr; API Keys.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">
                                    Razorpay Key Secret <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted font-monospace"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="razorpay_key_secret" id="razorpayKeySecret" class="form-control font-monospace" value="{{ \App\Models\Setting::get('razorpay_key_secret', config('services.razorpay.secret', env('RAZORPAY_KEY_SECRET', ''))) }}" placeholder="Enter Key Secret">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('razorpayKeySecret', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Private Key Secret generated along with the Key ID. Keep confidential.</small>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">
                                    Razorpay Webhook Secret <span class="badge bg-secondary-subtle text-secondary small">Recommended</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted font-monospace"><i class="fa-solid fa-fingerprint"></i></span>
                                    <input type="password" name="razorpay_webhook_secret" id="razorpayWebhookSecret" class="form-control font-monospace" value="{{ \App\Models\Setting::get('razorpay_webhook_secret', config('services.razorpay.webhook_secret', env('RAZORPAY_WEBHOOK_SECRET', ''))) }}" placeholder="Enter Webhook Secret">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('razorpayWebhookSecret', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Set in Razorpay Dashboard &rarr; Settings &rarr; Webhooks to verify notifications.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">
                                    Instant Webhook URL <span class="text-muted">(Auto-Generated)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-link"></i></span>
                                    <input type="text" id="webhookUrlInput" class="form-control font-monospace bg-light" value="{{ url('/razorpay/webhook') }}" readonly>
                                    <button class="btn btn-outline-danger" type="button" onclick="copyWebhookUrl()">
                                        <i class="fa-solid fa-copy me-1"></i> <span id="copyBtnText">Copy</span>
                                    </button>
                                </div>
                                <small class="text-muted">Events to subscribe: <code>payment.captured</code>, <code>order.paid</code>, <code>payment.failed</code>.</small>
                            </div>
                        </div>

                        <!-- Step-by-Step Instructions Card -->
                        <div class="border rounded-3 p-3 bg-light mt-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-circle-question text-primary me-2"></i> How to get Razorpay API Keys (Step-by-Step Guide)</h6>
                            <ol class="small text-muted mb-0 ps-3">
                                <li class="mb-1">Log in to <a href="https://dashboard.razorpay.com" target="_blank" class="fw-bold text-danger">Razorpay Dashboard</a>. Toggle between <strong>Test Mode</strong> and <strong>Live Mode</strong> in top-right as required.</li>
                                <li class="mb-1">Go to <strong>Settings</strong> &rarr; <strong>API Keys</strong> &rarr; click on <strong>Generate Key</strong> (or Regenerate Key).</li>
                                <li class="mb-1">Copy the <strong>Key ID</strong> and <strong>Key Secret</strong> and paste them in the fields above.</li>
                                <li class="mb-1">Go to <strong>Settings</strong> &rarr; <strong>Webhooks</strong> &rarr; click <strong>Add New Webhook</strong>. Paste the <em>Instant Webhook URL</em> above, choose a secret password, and select active events: <code>payment.captured</code> and <code>order.paid</code>.</li>
                                <li>Click <strong>Save All Settings</strong> at the bottom of this page.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 6: CACHE & OPTIMIZATION ================= -->
            <div class="tab-pane fade" id="system-pane" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-broom text-danger me-2"></i> Application Cache & Temporary Cookies
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted small mb-4">
                            If you have updated products, categories, vehicle models, or site branding and changes are not appearing immediately due to browser or server cache, clear the system cache and temporary cookies below.
                        </p>

                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-3 border text-center">
                                    <div class="text-muted small mb-1"><i class="fa-solid fa-layer-group text-primary me-1"></i> Blade Views</div>
                                    <div class="fw-bold text-dark">Compiled Views</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-3 border text-center">
                                    <div class="text-muted small mb-1"><i class="fa-solid fa-route text-success me-1"></i> Routing</div>
                                    <div class="fw-bold text-dark">Route Cache</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-3 border text-center">
                                    <div class="text-muted small mb-1"><i class="fa-solid fa-gears text-warning me-1"></i> Configuration</div>
                                    <div class="fw-bold text-dark">Config Cache</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-3 border text-center">
                                    <div class="text-muted small mb-1"><i class="fa-solid fa-cookie text-info me-1"></i> Client Storage</div>
                                    <div class="fw-bold text-dark">Temporary Cookies</div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 border d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">Purge All System Cache & Client Storage</h6>
                                <p class="text-muted small mb-0">Runs <code>optimize:clear</code>, flushes the cache store, and purges non-essential client cookies.</p>
                            </div>
                            <button type="button" class="btn btn-danger px-4 py-2 fw-bold shadow-sm btn-clear-cache">
                                <i class="fa-solid fa-broom me-2"></i> Clear Cache & Cookies Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky / Bottom Action Bar -->
        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> Changes will immediately take effect across the entire website.
            </span>
            <button type="submit" class="btn btn-theme-red px-5 py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save All Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Live preview for Logo
    const logoInput = document.getElementById('siteLogoInput');
    const logoPreviewImg = document.getElementById('logoPreviewImg');
    const logoStatusText = document.getElementById('logoStatusText');
    const removeLogoCheck = document.getElementById('removeLogoCheck');

    if (logoInput && logoPreviewImg) {
        logoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    logoPreviewImg.src = event.target.result;
                    if (logoStatusText) {
                        logoStatusText.innerHTML = '<span class="text-success fw-bold"><i class="fa-solid fa-check me-1"></i> New Logo Selected</span>';
                    }
                    if (removeLogoCheck) {
                        removeLogoCheck.checked = false;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeLogoCheck && logoPreviewImg) {
        removeLogoCheck.addEventListener('change', function () {
            if (this.checked) {
                logoPreviewImg.style.opacity = '0.3';
                if (logoStatusText) {
                    logoStatusText.innerHTML = '<span class="text-danger fw-bold"><i class="fa-solid fa-trash me-1"></i> Logo will be removed</span>';
                }
            } else {
                logoPreviewImg.style.opacity = '1';
                if (logoStatusText) {
                    logoStatusText.innerHTML = 'Current Logo';
                }
            }
        });
    }

    // Live preview for Favicon
    const faviconInput = document.getElementById('siteFaviconInput');
    const faviconPreviewImg = document.getElementById('faviconPreviewImg');
    const removeFaviconCheck = document.getElementById('removeFaviconCheck');

    if (faviconInput && faviconPreviewImg) {
        faviconInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    faviconPreviewImg.src = event.target.result;
                    if (removeFaviconCheck) {
                        removeFaviconCheck.checked = false;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeFaviconCheck && faviconPreviewImg) {
        removeFaviconCheck.addEventListener('change', function () {
            if (this.checked) {
                faviconPreviewImg.style.opacity = '0.3';
            } else {
                faviconPreviewImg.style.opacity = '1';
            }
        });
    }
});

function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

function copyWebhookUrl() {
    const input = document.getElementById('webhookUrlInput');
    if (!input) return;
    navigator.clipboard.writeText(input.value).then(() => {
        const btnText = document.getElementById('copyBtnText');
        if (btnText) {
            btnText.innerText = 'Copied!';
            setTimeout(() => {
                btnText.innerText = 'Copy';
            }, 2000);
        }
    }).catch(err => {
        input.select();
        document.execCommand('copy');
    });
}

function togglePaymentViaAjax(method, btn) {
    btn.disabled = true;
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating...';

    fetch('{{ route("admin.settings.toggle-payment") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ method: method })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            if (typeof showToast === 'function') {
                showToast(data.message, 'success');
            }

            if (method === 'online' || method === 'razorpay') {
                const sel = document.getElementById('select_razorpay_enabled');
                if (sel) sel.value = data.is_active ? '1' : '0';
                btn.className = 'btn btn-sm ' + (data.is_active ? 'btn-outline-danger' : 'btn-success') + ' px-3 py-1 fw-bold';
                btn.innerHTML = '<i class="fa-solid ' + (data.is_active ? 'fa-power-off' : 'fa-check') + ' me-1"></i> ' + (data.is_active ? 'Turn OFF' : 'Turn ON');
                const badge = document.getElementById('badge-online-header');
                if (badge) {
                    badge.className = 'badge ' + (data.is_active ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger');
                    badge.innerHTML = '<i class="fa-solid ' + (data.is_active ? 'fa-circle-check' : 'fa-ban') + ' me-1"></i> Online Payment: ' + (data.is_active ? 'OPEN' : 'CLOSED');
                }
                const card = document.getElementById('card-online-box');
                if (card) {
                    card.className = 'p-4 rounded-3 border h-100 ' + (data.is_active ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light');
                }
            } else if (method === 'cod') {
                const sel = document.getElementById('select_cod_enabled');
                if (sel) sel.value = data.is_active ? '1' : '0';
                btn.className = 'btn btn-sm ' + (data.is_active ? 'btn-outline-danger' : 'btn-success') + ' px-3 py-1 fw-bold';
                btn.innerHTML = '<i class="fa-solid ' + (data.is_active ? 'fa-power-off' : 'fa-check') + ' me-1"></i> ' + (data.is_active ? 'Turn OFF' : 'Turn ON');
                const badge = document.getElementById('badge-cod-header');
                if (badge) {
                    badge.className = 'badge ' + (data.is_active ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger');
                    badge.innerHTML = '<i class="fa-solid ' + (data.is_active ? 'fa-circle-check' : 'fa-ban') + ' me-1"></i> COD: ' + (data.is_active ? 'OPEN' : 'CLOSED');
                }
                const card = document.getElementById('card-cod-box');
                if (card) {
                    card.className = 'p-4 rounded-3 border h-100 ' + (data.is_active ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light');
                }
            }
        } else {
            btn.innerHTML = origHtml;
            alert(data.message || 'Failed to update payment status.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = origHtml;
        console.error(err);
        alert('Network error while toggling payment status.');
    });
}

function syncPaymentCards() {
    const rzpSel = document.getElementById('select_razorpay_enabled');
    if (rzpSel) {
        const isActive = rzpSel.value === '1';
        const card = document.getElementById('card-online-box');
        if (card) {
            card.className = 'p-4 rounded-3 border h-100 ' + (isActive ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light');
        }
        const badge = document.getElementById('badge-online-header');
        if (badge) {
            badge.className = 'badge ' + (isActive ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger');
            badge.innerHTML = '<i class="fa-solid ' + (isActive ? 'fa-circle-check' : 'fa-ban') + ' me-1"></i> Online Payment: ' + (isActive ? 'OPEN' : 'CLOSED');
        }
    }

    const codSel = document.getElementById('select_cod_enabled');
    if (codSel) {
        const isActive = codSel.value === '1';
        const card = document.getElementById('card-cod-box');
        if (card) {
            card.className = 'p-4 rounded-3 border h-100 ' + (isActive ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light');
        }
        const badge = document.getElementById('badge-cod-header');
        if (badge) {
            badge.className = 'badge ' + (isActive ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger');
            badge.innerHTML = '<i class="fa-solid ' + (isActive ? 'fa-circle-check' : 'fa-ban') + ' me-1"></i> COD: ' + (isActive ? 'OPEN' : 'CLOSED');
        }
    }
}
</script>
@endpush
@endsection
