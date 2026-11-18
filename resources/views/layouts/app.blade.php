<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SKM Saller - Genuine Car Logos, Monograms & Emblems')</title>
    <meta name="description" content="@yield('meta_description', 'India\'s leading online specialist for genuine car logos, chrome emblems, car monograms, and custom badges.')">
    
    @php
        $appLogo = \App\Models\Setting::get('site_logo');
        $appFavicon = \App\Models\Setting::get('site_favicon');
        $appName = \App\Models\Setting::get('store_name', 'SKM Saller');
        $appTagline = \App\Models\Setting::get('store_tagline', 'CAR LOGOS & EMBLEMS');
        $appPhone = \App\Models\Setting::get('store_phone', '+91 98765 43210');
        $appMobile = \App\Models\Setting::get('store_mobile', $appPhone);
        $appEmail = \App\Models\Setting::get('store_email', 'info@autologos.in');
        $appAddress = \App\Models\Setting::get('store_address', '123, Auto Street, New Delhi, India - 110001');
        $appAnnouncement = \App\Models\Setting::get('header_announcement_text', \App\Models\Setting::get('announcement_bar_text', 'Free Pan-India Delivery on orders above ₹999 | 100% Genuine Fitment Guarantee'));
        $appWhatsappRaw = \App\Models\Setting::get('whatsapp_support_number', \App\Models\Setting::get('whatsapp_phone', '919876543210'));
        $appWhatsappClean = preg_replace('/[^0-9]/', '', $appWhatsappRaw ?: '919876543210');
        $appFacebook = \App\Models\Setting::get('facebook_url', 'https://facebook.com');
        $appInstagram = \App\Models\Setting::get('instagram_url', 'https://instagram.com');
        $appYoutube = \App\Models\Setting::get('youtube_url', 'https://youtube.com');
        $appGstin = \App\Models\Setting::get('gstin', '07AAAAA0000A1Z5');
    @endphp

    @if($appFavicon && file_exists(public_path($appFavicon)))
        <link rel="icon" href="{{ asset($appFavicon) }}">
        <link rel="shortcut icon" href="{{ asset($appFavicon) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/dummy-product.svg') }}">
    @endif
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Montserrat:ital,wght@0,600;0,700;0,800;0,900;1,700;1,900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Frontend Custom Theme CSS (Browser Cached) -->
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}?v={{ file_exists(public_path('css/frontend.css')) ? filemtime(public_path('css/frontend.css')) : '1' }}">
    @stack('styles')
</head>
<body>

    <!-- 1. TOP INFORMATION BAR -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-5 d-none d-md-block">
                    <span class="me-3"><i class="fa-solid fa-phone text-danger me-1"></i> {{ $appPhone }}</span>
                    <span><i class="fa-solid fa-envelope text-danger me-1"></i> {{ $appEmail }}</span>
                </div>
                <div class="col-lg-4 col-md-3 text-center">
                    <span class="fw-semibold"><i class="fa-solid fa-truck-fast text-danger me-1"></i> {{ $appAnnouncement }}</span>
                </div>
                <div class="col-lg-4 col-md-4 text-end">
                    <a href="{{ route('order.track.view') }}" class="me-3"><i class="fa-solid fa-location-crosshairs text-danger me-1"></i> Track Order</a>
                    @auth
                        <div class="dropdown d-inline-block">
                            <a href="#" class="dropdown-toggle text-white fw-bold" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                @if(Auth::user()->isAdmin())
                                    <li><a class="dropdown-item fw-bold text-danger" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge-high me-2"></i> Admin Panel</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('account.dashboard') }}"><i class="fa-solid fa-user me-2"></i> My Account</a></li>
                                <li><a class="dropdown-item" href="{{ route('account.orders') }}"><i class="fa-solid fa-box-open me-2"></i> My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('account.addresses') }}"><i class="fa-solid fa-address-book me-2"></i> Saved Addresses</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}"><i class="fa-solid fa-user text-danger me-1"></i> Login / Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- SITE HEADER WRAPPER (Smart Scroll-Up Sticky Header) -->
    <div id="stickyHeaderContainer" class="sticky-header-container">
        <div id="stickyHeaderInner" class="sticky-header-inner">
            <!-- 2. MAIN HEADER -->
            <header class="main-header">
                <div class="container">
                    <div class="row align-items-center">
                        <!-- Mobile Hamburger & Logo -->
                        <div class="col-lg-3 col-md-4 col-7 d-flex align-items-center">
                            <button class="btn btn-outline-dark d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                            <a href="{{ route('home') }}" class="text-decoration-none">
                                @if($appLogo && file_exists(public_path($appLogo)))
                                    <img src="{{ asset($appLogo) }}" alt="{{ $appName }}" class="site-header-logo" style="max-height: 80px; max-width: 220px; object-fit: contain;">
                                @else
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <i class="fa-solid fa-shield-halved text-danger fs-2"></i>
                                        </div>
                                        <div>
                                            <div class="logo-text brand-font">
                                                @php
                                                    $words = explode(' ', $appName, 2);
                                                @endphp
                                                @if(count($words) == 2)
                                                    {{ $words[0] }}<span>{{ $words[1] }}</span>
                                                @else
                                                    {{ $appName }}
                                                @endif
                                            </div>
                                            <div class="logo-subtext">{{ $appTagline }}</div>
                                        </div>
                                    </div>
                                @endif
                            </a>
                        </div>

                        <!-- Search Bar with Category Dropdown -->
                        <div class="col-lg-6 col-md-5 d-none d-md-block position-relative">
                            <form action="{{ route('shop') }}" method="GET" class="header-search-form" id="headerSearchForm">
                                <select name="category" class="search-category-select no-select2">
                                    <option value="">All Categories</option>
                                    @foreach($globalCategories as $cat)
                                        <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="q" id="headerSearchInput" class="header-search-input" placeholder="Search for car logos, monograms, emblems..." value="{{ request('q') }}" autocomplete="off">
                                <button type="submit" class="header-search-btn">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </form>
                            <div id="searchSuggestionsBox"></div>
                        </div>

                        <!-- Header Actions (Wishlist & Cart) -->
                        <div class="col-lg-3 col-md-3 col-5 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <a href="{{ route('wishlist.index') }}" class="text-decoration-none text-dark position-relative d-flex align-items-center">
                                    <i class="fa-regular fa-heart fs-4 me-1"></i>
                                    <span class="d-none d-lg-inline fw-semibold font-sm">Wishlist</span>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="globalWishlistCounter">
                                        {{ $globalWishlistCount }}
                                    </span>
                                </a>

                                <a href="{{ route('cart.index') }}" class="text-decoration-none text-dark position-relative d-flex align-items-center ps-2">
                                    <i class="fa-solid fa-cart-shopping fs-4 text-danger me-1"></i>
                                    <span class="d-none d-lg-inline fw-semibold font-sm">Cart</span>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="globalCartCounter">
                                        {{ $globalCartCount }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Search Bar -->
                    <div class="row d-md-none mt-2">
                        <div class="col-12">
                            <form action="{{ route('shop') }}" method="GET" class="header-search-form">
                                <input type="text" name="q" class="header-search-input" placeholder="Search logos, emblems..." value="{{ request('q') }}">
                                <button type="submit" class="header-search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- 3. NAVIGATION BAR -->
            <nav class="nav-header d-none d-lg-block">
                <div class="container">
                    <ul class="nav main-nav mb-0">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fa-solid fa-house me-1"></i> HOME</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('vehicle*') || request()->routeIs('vehicles') ? 'active' : '' }}" href="{{ route('vehicles') }}"><i class="fa-solid fa-car-side text-danger me-1"></i> VEHICLES</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop') && !request('category') && !request('sort') && !request('placement') && !request('type') && !request('new_arrivals') && !request('brand') && !request('model') ? 'active' : '' }}" href="{{ route('shop') }}">ALL PRODUCTS</a></li>

                        {{-- DYNAMIC HEADER CATEGORIES --}}
                        @if(isset($headerCategories) && $headerCategories->isNotEmpty())
                            @foreach($headerCategories as $navCat)
                                @php
                                    $isCatActive = request('category') == $navCat->slug
                                        || ($navCat->slug == 'new-arrivals' && (request('category') == 'new-arrivals' || request('new_arrivals') == '1'))
                                        || ($navCat->slug == 'front-logo' && (request('category') == 'front-logo' || request('placement') == 'Front' || request('placement') == 'Front Grille'))
                                        || ($navCat->slug == 'rear-logo' && (request('category') == 'rear-logo' || request('placement') == 'Rear' || request('placement') == 'Rear Trunk'))
                                        || ($navCat->slug == 'bonnet-tailgate-letters' && (request('category') == 'bonnet-tailgate-letters' || request('placement') == 'Bonnet' || request('placement') == 'Bonnet / Boot'))
                                        || ($navCat->slug == 'kit-full-monogram' && (request('category') == 'kit-full-monogram' || request('placement') == 'Kit' || request('placement') == 'Full Exterior Kit'))
                                        || ($navCat->slug == 'single-name-badge' && (request('category') == 'single-name-badge' || request('type') == 'badge'));
                                    $isNewArrival = ($navCat->slug == 'new-arrivals' || str_contains(strtolower($navCat->name), 'new arrival'));
                                @endphp
                                <li class="nav-item">
                                    <a class="nav-link {{ $isNewArrival ? 'text-warning' : '' }} {{ $isCatActive ? 'active' : '' }}" href="{{ route('shop', ['category' => $navCat->slug]) }}">
                                        @if($isNewArrival)<i class="fa-solid fa-sparkles me-1"></i>@endif
                                        {{ strtoupper($navCat->name) }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}"><i class="fa-solid fa-headset me-1 text-danger"></i> CONTACT US</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- WhatsApp Floating Button -->
    <a href="https://api.whatsapp.com/send?phone={{ $appWhatsappClean }}&text=Hello%20{{ urlencode($appName) }},%20I%20have%20an%20inquiry%20regarding%20car%20emblems." target="_blank" class="whatsapp-float shadow" title="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Mobile Offcanvas Menu (Lightweight with On-Demand AJAX Vehicle Accordion) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header bg-dark text-white">
            @if($appLogo && file_exists(public_path($appLogo)))
                <img src="{{ asset($appLogo) }}" alt="{{ $appName }}" style="max-height: 38px; max-width: 150px; object-fit: contain;">
            @else
                <h5 class="offcanvas-title brand-font" id="mobileMenuLabel">{{ $appName }}</h5>
            @endif
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ route('home') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-house me-2 text-danger"></i> Home</a></li>
                <li class="list-group-item"><a href="{{ route('vehicles') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-car-side me-2 text-danger"></i> Vehicles / Brands</a></li>
                <li class="list-group-item"><a href="{{ route('shop') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-border-all me-2 text-danger"></i> All Products</a></li>
                @if(isset($headerCategories) && $headerCategories->isNotEmpty())
                    @foreach($headerCategories as $mCat)
                        @php
                            $isMCatActive = request('category') == $mCat->slug;
                            $isMNewArrival = ($mCat->slug == 'new-arrivals' || str_contains(strtolower($mCat->name), 'new arrival'));
                            $mIcon = $mCat->icon ?: ($isMNewArrival ? 'fa-solid fa-sparkles text-warning' : 'fa-solid fa-certificate text-danger');
                        @endphp
                        <li class="list-group-item">
                            <a href="{{ route('shop', ['category' => $mCat->slug]) }}" class="text-decoration-none text-dark {{ $isMCatActive ? ($isMNewArrival ? 'fw-bold text-warning' : 'fw-bold text-danger') : '' }}">
                                <i class="{{ $mIcon }} me-2 {{ $isMNewArrival ? 'text-warning' : 'text-danger' }}"></i> {{ $mCat->name }}
                            </a>
                        </li>
                    @endforeach
                @endif
                <li class="list-group-item"><a href="{{ route('order.track.view') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-location-crosshairs me-2 text-danger"></i> Track Order</a></li>
                <li class="list-group-item"><a href="{{ route('contact') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-envelope me-2 text-danger"></i> Contact Us</a></li>
            </ul>

            <div class="px-3 py-2 border-top border-bottom" style="background: #090d16; border-color: #1e293b !important;">
                <span class="text-uppercase fw-bold text-secondary" style="font-size: 11px; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-car text-danger me-1"></i> Shop By Vehicle Brand
                </span>
            </div>
            <div class="accordion accordion-flush" id="mobileVehicleAccordion" data-brands-loaded="false">
                <div class="text-center py-3 text-secondary small">
                    <i class="fa-solid fa-spinner fa-spin text-danger me-1"></i> Loading brands...
                </div>
            </div>
        </div>
    </div>

    <!-- 4. SITE FOOTER -->
    <footer class="site-footer mt-5">
        <div class="container pb-5">
            <div class="row g-4">
                <!-- Col 1: About -->
                <div class="col-lg-3 col-md-6">
                    @if($appLogo && file_exists(public_path($appLogo)))
                        <div class="mb-3">
                            <img src="{{ asset($appLogo) }}" alt="{{ $appName }}" class="site-footer-logo bg-white rounded p-2" style="max-height: 48px; max-width: 190px; object-fit: contain;">
                        </div>
                    @else
                        <div class="logo-text brand-font text-white mb-2">
                            @php
                                $words = explode(' ', $appName, 2);
                            @endphp
                            @if(count($words) == 2)
                                {{ $words[0] }}<span class="text-danger">{{ $words[1] }}</span>
                            @else
                                {{ $appName }}
                            @endif
                        </div>
                        <div class="logo-subtext text-secondary mb-3">{{ $appTagline }}</div>
                    @endif
                    <p class="text-secondary leading-relaxed">
                        Your one-stop destination for all car logos, monograms, emblems, letters & numbers. 100% genuine and premium quality products for all cars.
                    </p>
                    <div class="d-flex gap-2 mt-3">
                        @if($appFacebook)<a href="{{ $appFacebook }}" target="_blank" class="btn btn-sm btn-outline-secondary text-white rounded-circle"><i class="fa-brands fa-facebook-f"></i></a>@endif
                        @if($appInstagram)<a href="{{ $appInstagram }}" target="_blank" class="btn btn-sm btn-outline-secondary text-white rounded-circle"><i class="fa-brands fa-instagram"></i></a>@endif
                        @if($appYoutube)<a href="{{ $appYoutube }}" target="_blank" class="btn btn-sm btn-outline-secondary text-white rounded-circle"><i class="fa-brands fa-youtube"></i></a>@endif
                        <a href="https://api.whatsapp.com/send?phone={{ $appWhatsappClean }}&text=Hi%20{{ urlencode($appName) }}" target="_blank" class="btn btn-sm btn-outline-secondary text-white rounded-circle"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h5>Quick Links</h5>
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('vehicles') }}">All 18 Brands</a>
                    <a href="{{ route('shop') }}">All Products</a>
                    @if(isset($headerCategories) && $headerCategories->isNotEmpty())
                        @foreach($headerCategories as $fCat)
                            <a href="{{ route('shop', ['category' => $fCat->slug]) }}">{{ $fCat->name }}</a>
                        @endforeach
                    @endif
                    <a href="{{ route('contact') }}">Contact Us</a>
                </div>

                <!-- Col 3: Customer Service -->
                <div class="col-lg-2 col-md-6">
                    <h5>Customer Service</h5>
                    <a href="{{ route('order.track.view') }}">Track Order</a>
                    <a href="{{ route('page.show', 'shipping-policy') }}">Shipping Policy</a>
                    <a href="{{ route('page.show', 'return-refund-policy') }}">Return & Refund</a>
                    <a href="{{ route('page.show', 'terms-conditions') }}">Terms & Conditions</a>
                    <a href="{{ route('page.show', 'privacy-policy') }}">Privacy Policy</a>
                    <a href="{{ route('page.show', 'faq') }}">FAQ</a>
                </div>

                <!-- Col 4: My Account -->
                <div class="col-lg-2 col-md-6">
                    <h5>My Account</h5>
                    <a href="{{ route('login') }}">Login / Register</a>
                    <a href="{{ route('account.orders') }}">My Orders</a>
                    <a href="{{ route('wishlist.index') }}">Wishlist</a>
                    <a href="{{ route('account.dashboard') }}">Account Details</a>
                    <a href="{{ route('cart.index') }}">View Cart</a>
                </div>

                <!-- Col 5: Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <h5>Contact Us</h5>
                    <p class="mb-2"><i class="fa-solid fa-phone text-danger me-2"></i> {{ $appPhone }}</p>
                    <p class="mb-2"><i class="fa-solid fa-envelope text-danger me-2"></i> {{ $appEmail }}</p>
                    <p class="mb-2"><i class="fa-solid fa-location-dot text-danger me-2"></i> {!! nl2br(e($appAddress)) !!}</p>
                    <p class="small text-secondary mt-3">GSTIN: <span class="text-white">{{ $appGstin }}</span></p>
                </div>
            </div>
        </div>

        <!-- Copyright Bar -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start text-secondary">
                        © {{ date('Y') }} {{ $appName }}. All Rights Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end text-secondary mt-2 mt-md-0">
                        <span class="me-2">We Accept</span>
                        <span class="badge bg-light text-dark me-1"><i class="fa-brands fa-cc-visa text-primary"></i> VISA</span>
                        <span class="badge bg-light text-dark me-1"><i class="fa-brands fa-cc-mastercard text-danger"></i> Mastercard</span>
                        <span class="badge bg-light text-dark me-1"><i class="fa-solid fa-qrcode text-success"></i> UPI</span>
                        <span class="badge bg-light text-dark"><i class="fa-solid fa-wallet text-info"></i> Paytm</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global AJAX Handlers -->
    <script>
        // Setup CSRF token for Fetch / AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Instant Live Search Suggestions
        const searchInput = document.getElementById('headerSearchInput');
        const suggestionsBox = document.getElementById('searchSuggestionsBox');

        if (searchInput && suggestionsBox) {
            let timeout = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const val = this.value.trim();
                if (val.length < 2) {
                    suggestionsBox.style.display = 'none';
                    return;
                }

                timeout = setTimeout(() => {
                    const categorySelect = document.querySelector('.search-category-select');
                    const categoryVal = categorySelect ? categorySelect.value : '';
                    fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(val)}&category=${encodeURIComponent(categoryVal)}`)
                        .then(res => res.json())
                        .then(data => {
                            let html = '';
                            if (data.products && data.products.length > 0) {
                                html += '<div class="p-2 bg-dark text-uppercase fw-bold text-secondary border-bottom border-secondary border-opacity-25" style="font-size:11px;">Products</div>';
                                data.products.forEach(p => {
                                    html += `
                                        <a href="${p.url}" class="search-item-row">
                                            <img src="${p.image}" alt="${p.name}" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold font-sm text-light">${p.name}</div>
                                                <div class="text-danger fw-bold font-sm">${p.price}</div>
                                            </div>
                                        </a>
                                    `;
                                });
                            }
                            if (data.models && data.models.length > 0) {
                                html += '<div class="p-2 bg-dark text-uppercase fw-bold text-secondary border-bottom border-secondary border-opacity-25" style="font-size:11px;">Car Models</div>';
                                data.models.forEach(m => {
                                    html += `
                                        <a href="${m.url}" class="search-item-row">
                                            <i class="fa-solid fa-car-side text-danger me-2"></i>
                                            <div class="fw-bold font-sm text-light">${m.name}</div>
                                        </a>
                                    `;
                                });
                            }
                            if (data.brands && data.brands.length > 0) {
                                html += '<div class="p-2 bg-dark text-uppercase fw-bold text-secondary border-bottom border-secondary border-opacity-25" style="font-size:11px;">Brands</div>';
                                data.brands.forEach(b => {
                                    html += `
                                        <a href="${b.url}" class="search-item-row">
                                            <i class="fa-solid fa-car text-danger me-2"></i>
                                            <div class="fw-bold font-sm text-light">${b.name}</div>
                                        </a>
                                    `;
                                });
                            }
                            if (html) {
                                html += `
                                    <a href="{{ route('shop') }}?q=${encodeURIComponent(val)}${categoryVal ? '&category=' + encodeURIComponent(categoryVal) : ''}" class="p-2 bg-dark d-block text-center text-danger fw-bold font-sm text-decoration-none border-top border-secondary border-opacity-25">
                                        <i class="fa-solid fa-magnifying-glass me-1"></i> View all results for "${val}"
                                    </a>
                                `;
                                suggestionsBox.innerHTML = html;
                                suggestionsBox.style.display = 'block';
                            } else {
                                suggestionsBox.style.display = 'none';
                            }
                        });
                }, 250);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.style.display = 'none';
                }
            });
        }

        // Global AJAX Wishlist Toggle
        function toggleWishlist(productId, btnElem) {
            fetch(`/wishlist/toggle/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const counter = document.getElementById('globalWishlistCounter');
                    if (counter) counter.innerText = data.wishlist_count;

                    if (btnElem) {
                        const heartIcon = btnElem.querySelector('i');
                        if (heartIcon) {
                            if (data.added) {
                                heartIcon.classList.remove('fa-regular');
                                heartIcon.classList.add('fa-solid', 'text-danger');
                            } else {
                                heartIcon.classList.remove('fa-solid', 'text-danger');
                                heartIcon.classList.add('fa-regular');
                            }
                        }
                    }

                    showToast(data.message, 'success');
                }
            })
            .catch(err => console.error(err));
        }

        // Global AJAX Add to Cart
        function addToCartAjax(productId, variantId = null, qty = 1, callback = null) {
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    quantity: qty
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const counter = document.getElementById('globalCartCounter');
                    if (counter) counter.innerText = data.cart_count;
                    showToast(data.message, 'success');
                    if (callback) callback(data);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => console.error(err));
        }

        // SweetAlert2 Toast Instance
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 3800,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // Global window.alert replacement
        window.alert = function (message) {
            Swal.fire({
                text: message,
                icon: 'info',
                confirmButtonColor: '#d90429',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn-swal-confirm' }
            });
        };

        // Universal Form Confirmation Interceptor
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.dataset.confirmed === 'true') {
                return;
            }

            const confirmMsg = form.getAttribute('data-confirm') || form.getAttribute('data-confirm-message');
            const hasConfirmClass = form.classList.contains('confirm-action') || form.classList.contains('delete-form');
            const onsubmitAttr = form.getAttribute('onsubmit') || '';
            const hasInlineConfirm = onsubmitAttr.includes('confirm(');

            if (confirmMsg || hasConfirmClass || hasInlineConfirm) {
                e.preventDefault();
                e.stopPropagation();

                let msg = confirmMsg;
                if (!msg && hasInlineConfirm) {
                    const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                    if (match && match[1]) {
                        msg = match[1];
                    }
                }
                msg = msg || 'Are you sure you want to proceed?';
                const title = form.getAttribute('data-confirm-title') || 'Are you sure?';
                const btnText = form.getAttribute('data-confirm-btn') || 'Yes, proceed';

                Swal.fire({
                    title: title,
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d90429',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: btnText,
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn-swal-confirm',
                        cancelButton: 'btn-swal-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            }
        }, true);

        // Universal Link Confirmation Interceptor
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[data-confirm], a[onclick*="confirm("], .confirm-link');
            if (!link) return;

            if (link.dataset.confirmed === 'true') {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            let msg = link.getAttribute('data-confirm');
            const onclickAttr = link.getAttribute('onclick') || '';
            if (!msg && onclickAttr.includes('confirm(')) {
                const match = onclickAttr.match(/confirm\(['"](.*?)['"]\)/);
                if (match && match[1]) {
                    msg = match[1];
                }
            }
            msg = msg || 'Are you sure you want to proceed?';
            const title = link.getAttribute('data-confirm-title') || 'Are you sure?';
            const btnText = link.getAttribute('data-confirm-btn') || 'Yes, proceed';

            Swal.fire({
                title: title,
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d90429',
                cancelButtonColor: '#64748b',
                confirmButtonText: btnText,
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn-swal-confirm',
                    cancelButton: 'btn-swal-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    link.dataset.confirmed = 'true';
                    window.location.href = link.href;
                }
            });
        }, true);

        // Upgraded showToast to SweetAlert2
        window.showToast = function (msg, type = 'info') {
            const iconType = ['success', 'error', 'warning', 'info', 'question'].includes(type) ? type : 'info';
            Toast.fire({
                icon: iconType,
                title: msg
            });
        };

        // Automatic flash message handling via SweetAlert2
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ addslashes(session('success')) }}"
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Action Failed',
                    text: "{{ addslashes(session('error')) }}",
                    confirmButtonColor: '#d90429',
                    customClass: { confirmButton: 'btn-swal-confirm' }
                });
            @endif

            @if(session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: "{{ addslashes(session('warning')) }}"
                });
            @endif

            @if(session('info'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ addslashes(session('info')) }}"
                });
            @endif

            @if(session('status'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ addslashes(session('status')) }}"
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: '<ul class="text-start mb-0 ps-3">@foreach($errors->all() as $err)<li>{{ addslashes($err) }}</li>@endforeach</ul>',
                    confirmButtonColor: '#d90429',
                    customClass: { confirmButton: 'btn-swal-confirm' }
                });
            @endif
        });
    </script>

    <!-- jQuery & Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function applySelect2(context) {
            if (!window.jQuery || !$.fn.select2) return;
            const $root = context ? $(context) : $(document);

            // 1. Initialize Quick Vehicle Selector on homepage (Dark Theme)
            $root.find('#vehicleQuickSearchForm select').each(function() {
                const $s = $(this);
                if ($s.hasClass('no-select2') || $s.hasClass('select2-hidden-accessible')) {
                    return;
                }

                const firstOptionText = $s.find('option:first-child').text() || 'Search';
                const searchPrompt = firstOptionText.replace(/^[0-9]+\.\s*/, 'Search ') + '...';

                $s.select2({
                    width: '100%',
                    dropdownAutoWidth: false,
                    dropdownCssClass: 'quick-search-dropdown',
                    minimumResultsForSearch: 4,
                    templateResult: function(data) {
                        // Hide empty placeholder from the dropdown list
                        if (!data.id) {
                            return null;
                        }
                        return data.text;
                    }
                });

                // Dynamically set search input placeholder on open
                $s.on('select2:open', function() {
                    setTimeout(function() {
                        const searchField = document.querySelector('.select2-container--open .select2-search__field');
                        if (searchField) {
                            searchField.placeholder = searchPrompt;
                        }
                    }, 15);
                });

                $s.on('select2:select select2:unselect', function() {
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                });

                $s.on('change.select2 change', function() {
                    const $container = $s.next('.select2-container');
                    if ($s.prop('disabled')) {
                        $container.addClass('select2-container--disabled');
                    } else {
                        $container.removeClass('select2-container--disabled');
                    }
                });
            });

            // 2. Initialize all other standard frontend selects (Light Theme)
            $root.find('select.form-select, select.select2')
                .not('#vehicleQuickSearchForm select, .search-category-select, .no-select2')
                .each(function() {
                    const $s = $(this);
                    if ($s.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    const isSm = $s.hasClass('form-select-sm');
                    const optCount = $s.find('option').length;
                    const isFilter = $s.attr('name') === 'brand' || $s.attr('name') === 'model' || $s.attr('name') === 'variant';
                    const hasExplicitWidth = $s[0].style.width && $s[0].style.width !== '';
                    const widthVal = hasExplicitWidth ? $s[0].style.width : '100%';

                    $s.select2({
                        width: widthVal,
                        dropdownAutoWidth: false,
                        minimumResultsForSearch: (optCount <= 5 && !isFilter) ? Infinity : 4,
                        dropdownCssClass: 'select2-dropdown-light',
                        containerCssClass: isSm ? 'select2-container-sm' : ''
                    });

                    $s.on('select2:select select2:unselect', function() {
                        this.dispatchEvent(new Event('change', { bubbles: true }));
                    });

                    $s.on('change.select2 change', function() {
                        const $container = $s.next('.select2-container');
                        if ($s.prop('disabled')) {
                            $container.addClass('select2-container--disabled');
                        } else {
                            $container.removeClass('select2-container--disabled');
                        }
                    });
                });
        }

        $(document).ready(function() {
            applySelect2();

            // Smart Scroll-Up Sticky Header with smooth slide-down animation
            (function() {
                const container = document.getElementById('stickyHeaderContainer');
                const inner = document.getElementById('stickyHeaderInner');
                if (!container || !inner) return;

                let lastScrollY = window.pageYOffset || document.documentElement.scrollTop;
                let ticking = false;
                const threshold = 6;

                function getHeaderHeight() {
                    return inner.offsetHeight || 115;
                }

                function updateScroll() {
                    const currentScrollY = window.pageYOffset || document.documentElement.scrollTop;
                    const headerH = getHeaderHeight();
                    const delta = currentScrollY - lastScrollY;

                    // Near the top of page: revert to normal static flow
                    if (currentScrollY <= headerH + 35) {
                        container.classList.remove('is-sticky', 'header-visible', 'header-hidden');
                        container.style.minHeight = '';
                        lastScrollY = Math.max(0, currentScrollY);
                        ticking = false;
                        return;
                    }

                    // Ignore tiny scroll jitter
                    if (Math.abs(delta) < threshold) {
                        ticking = false;
                        return;
                    }

                    if (delta > 0) {
                        // Scrolling DOWN -> slide header up out of view
                        if (!container.classList.contains('is-sticky')) {
                            container.style.minHeight = headerH + 'px';
                            container.classList.add('is-sticky');
                        }
                        container.classList.remove('header-visible');
                        container.classList.add('header-hidden');
                    } else {
                        // Scrolling UP -> slide header DOWN smoothly into view
                        if (!container.classList.contains('is-sticky')) {
                            container.style.minHeight = headerH + 'px';
                            container.classList.add('is-sticky');
                        }
                        container.classList.remove('header-hidden');
                        container.classList.add('header-visible');
                    }

                    lastScrollY = Math.max(0, currentScrollY);
                    ticking = false;
                }

                window.addEventListener('scroll', function() {
                    if (!ticking) {
                        window.requestAnimationFrame(updateScroll);
                        ticking = true;
                    }
                }, { passive: true });

                window.addEventListener('resize', function() {
                    if (container.classList.contains('is-sticky')) {
                        container.style.minHeight = getHeaderHeight() + 'px';
                    }
                });
            })();

            // Mobile Offcanvas Vehicle Accordion AJAX Loader (Zero HTML bloat on desktop/bots)
            (function() {
                const mobileOffcanvas = document.getElementById('mobileMenu');
                const accordion = document.getElementById('mobileVehicleAccordion');
                if (!mobileOffcanvas || !accordion) return;

                window.__brandHierarchyCache = window.__brandHierarchyCache || {};

                function escMobileHtml(str) {
                    const div = document.createElement('div');
                    div.textContent = str == null ? '' : String(str);
                    return div.innerHTML;
                }

                function renderMobileBrands(brands) {
                    if (!brands || brands.length === 0) {
                        return '<div class="text-secondary small text-center py-3">No brands available.</div>';
                    }
                    let html = '';
                    brands.forEach(function(brand) {
                        html += '<div class="accordion-item">';
                        html += '<h2 class="accordion-header" id="headingBrand' + brand.id + '">';
                        html += '<button class="accordion-button collapsed py-2 font-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBrand' + brand.id + '" aria-expanded="false">';
                        html += '<span class="d-inline-flex align-items-center justify-content-center rounded me-2 flex-shrink-0" style="width: 26px; height: 26px; background: #161f30; border: 1px solid #1e293b;">';
                        html += '<img src="' + escMobileHtml(brand.logo_url) + '" alt="' + escMobileHtml(brand.name) + '" loading="lazy" decoding="async" style="max-width: 18px; max-height: 18px; object-fit: contain;">';
                        html += '</span>';
                        html += '<span>' + escMobileHtml(brand.name) + '</span>';
                        html += '<span class="badge bg-light text-muted border ms-auto me-2 font-sm" style="font-size: 10px;">' + (brand.models_count || 0) + '</span>';
                        html += '</button></h2>';
                        html += '<div id="collapseBrand' + brand.id + '" class="accordion-collapse collapse" data-bs-parent="#mobileVehicleAccordion" data-mobile-brand-slug="' + escMobileHtml(brand.slug) + '" data-loaded="false">';
                        html += '<div class="accordion-body p-2">';
                        html += '<a href="' + escMobileHtml(brand.cars_url) + '" class="btn btn-outline-danger btn-sm w-100 mb-2 py-1 font-sm fw-bold">All ' + escMobileHtml(brand.name) + ' Models &rarr;</a>';
                        html += '<div class="mobile-brand-models-container"><div class="text-center py-2 text-secondary small"><i class="fa-solid fa-spinner fa-spin text-danger me-1"></i> Loading models...</div></div>';
                        html += '</div></div></div>';
                    });
                    return html;
                }

                function renderMobileModels(models) {
                    if (!models || models.length === 0) {
                        return '<div class="text-secondary small text-center py-2">No active models available.</div>';
                    }
                    let html = '<div class="list-group list-group-flush">';
                    models.forEach(function(car) {
                        html += '<div class="list-group-item rounded border mb-1 p-2">';
                        html += '<div class="d-flex align-items-center justify-content-between mb-1">';
                        html += '<a href="' + escMobileHtml(car.url) + '" class="fw-bold text-decoration-none font-sm">';
                        html += '<i class="fa-solid fa-car text-danger me-1"></i> ' + escMobileHtml(car.name);
                        html += '</a>';
                        html += '<a href="' + escMobileHtml(car.url) + '" class="text-secondary font-sm text-decoration-none" style="font-size: 11px;">All &rarr;</a>';
                        html += '</div>';
                        if (car.variants && car.variants.length > 0) {
                            html += '<div class="d-flex flex-wrap gap-1 mt-1">';
                            car.variants.forEach(function(variant) {
                                html += '<a href="' + escMobileHtml(variant.url) + '" class="flyout-variant-pill">' + escMobileHtml(variant.name) + '</a>';
                            });
                            html += '</div>';
                        }
                        html += '</div>';
                    });
                    html += '</div>';
                    return html;
                }

                mobileOffcanvas.addEventListener('show.bs.offcanvas', function() {
                    if (accordion.dataset.brandsLoaded === 'true') return;
                    fetch('{{ route("ajax.active_brands") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res && res.success) {
                            accordion.innerHTML = renderMobileBrands(res.data);
                            accordion.dataset.brandsLoaded = 'true';
                        } else {
                            accordion.innerHTML = '<div class="text-secondary small text-center py-3">Unable to load brands.</div>';
                        }
                    })
                    .catch(function() {
                        accordion.innerHTML = '<div class="text-secondary small text-center py-3">Unable to load brands.</div>';
                    });
                });

                accordion.addEventListener('show.bs.collapse', function(e) {
                    const collapseEl = e.target;
                    const slug = collapseEl.getAttribute('data-mobile-brand-slug');
                    if (!slug || collapseEl.dataset.loaded === 'true') return;

                    const container = collapseEl.querySelector('.mobile-brand-models-container');
                    if (!container) return;

                    if (window.__brandHierarchyCache[slug]) {
                        container.innerHTML = renderMobileModels(window.__brandHierarchyCache[slug].models);
                        collapseEl.dataset.loaded = 'true';
                        return;
                    }

                    fetch('/ajax/brand-hierarchy/' + encodeURIComponent(slug), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data && data.success) {
                            window.__brandHierarchyCache[slug] = data;
                            container.innerHTML = renderMobileModels(data.models);
                            collapseEl.dataset.loaded = 'true';
                        } else {
                            container.innerHTML = '<div class="text-secondary small text-center py-2">Unable to load models.</div>';
                        }
                    })
                    .catch(function() {
                        container.innerHTML = '<div class="text-secondary small text-center py-2">Unable to load models.</div>';
                    });
                });
            })();
        });
    </script>
    @stack('scripts')
</body>
</html>
