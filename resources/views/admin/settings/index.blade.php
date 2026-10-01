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
        <div>
            <button type="submit" form="settingsForm" class="btn btn-theme-red px-4 py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save All Changes
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <strong><i class="fa-solid fa-triangle-exclamation me-2"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                    <i class="fa-solid fa-credit-card me-2 text-info"></i> Payment Gateway
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

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Enable Cash on Delivery (COD)</label>
                                <select name="cod_enabled" class="form-select">
                                    <option value="1" {{ \App\Models\Setting::get('cod_enabled', \App\Models\Setting::get('enable_cod', '1')) == '1' ? 'selected' : '' }}>Enabled (Available at Checkout)</option>
                                    <option value="0" {{ \App\Models\Setting::get('cod_enabled', \App\Models\Setting::get('enable_cod', '1')) == '0' ? 'selected' : '' }}>Disabled (Prepaid Orders Only)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Cash on Delivery (COD) Handling Charge (₹)</label>
                                <input type="number" step="0.01" name="cod_charge" class="form-control" value="{{ \App\Models\Setting::get('cod_charge', 49) }}">
                                <small class="text-muted">Additional fee applied when customer selects COD payment.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB 5: PAYMENT GATEWAY ================= -->
            <div class="tab-pane fade" id="payment-pane" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-credit-card text-danger me-2"></i> Razorpay Payment Gateway Integration
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Razorpay Key ID</label>
                                <input type="text" name="razorpay_key_id" class="form-control font-monospace" value="{{ \App\Models\Setting::get('razorpay_key_id', 'rzp_test_sampleKey123456') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Razorpay Key Secret</label>
                                <input type="password" name="razorpay_key_secret" class="form-control font-monospace" value="{{ \App\Models\Setting::get('razorpay_key_secret', 'sampleSecretValueRazorpay123') }}">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-dark">Gateway Status</label>
                            <select name="razorpay_enabled" class="form-select">
                                <option value="1" {{ \App\Models\Setting::get('razorpay_enabled', \App\Models\Setting::get('enable_razorpay', '1')) == '1' ? 'selected' : '' }}>Active (Accept UPI, Credit/Debit Cards, NetBanking)</option>
                                <option value="0" {{ \App\Models\Setting::get('razorpay_enabled', \App\Models\Setting::get('enable_razorpay', '1')) == '0' ? 'selected' : '' }}>Disabled / Maintenance</option>
                            </select>
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
</script>
@endpush
@endsection
