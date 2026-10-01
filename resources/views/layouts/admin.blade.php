<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - AutoLogos')</title>

    @php
        $adminFavicon = \App\Models\Setting::get('site_favicon');
        $adminLogo = \App\Models\Setting::get('site_logo');
        $adminStoreName = \App\Models\Setting::get('store_name', 'AutoLogos');
    @endphp
    @if($adminFavicon && file_exists(public_path($adminFavicon)))
        <link rel="icon" href="{{ asset($adminFavicon) }}">
        <link rel="shortcut icon" href="{{ asset($adminFavicon) }}">
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --sidebar-bg: #0b0f19;
            --sidebar-hover: #1e293b;
            --sidebar-active: #d90429;
            --admin-red: #d90429;
            --body-bg: #f1f5f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: #1e293b;
            overflow-x: hidden;
        }

        .brand-font {
            font-family: 'Montserrat', sans-serif;
        }

        /* ========================================================
           SweetAlert2 Professional SaaS Theme (Stripe/Linear Style)
           ======================================================== */
        /* Modal Dialog Customization */
        .swal2-popup.swal2-modal {
            font-family: 'Inter', sans-serif !important;
            border-radius: 16px !important;
            padding: 2rem !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
        }
        .swal2-modal .swal2-title {
            font-weight: 700 !important;
            font-size: 1.25rem !important;
            color: #0f172a !important;
            margin-bottom: 0.5rem !important;
            padding: 0 !important;
        }
        .swal2-modal .swal2-html-container {
            font-size: 0.925rem !important;
            color: #64748b !important;
            line-height: 1.55 !important;
            margin: 0 !important;
        }
        .swal2-modal .swal2-icon {
            margin: 0.5rem auto 1.25rem auto !important;
            transform: scale(0.95);
        }
        .swal2-modal .swal2-actions {
            margin-top: 1.5rem !important;
            gap: 10px !important;
        }
        .btn-swal-confirm {
            background-color: var(--admin-red, #d90429) !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 9px 22px !important;
            box-shadow: 0 4px 12px rgba(217, 4, 41, 0.25) !important;
            border: none !important;
            transition: all 0.2s ease !important;
        }
        .btn-swal-confirm:hover {
            background-color: #b90323 !important;
            transform: translateY(-1px) !important;
        }
        .btn-swal-cancel {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 9px 22px !important;
            border: 1px solid #cbd5e1 !important;
            transition: all 0.2s ease !important;
        }
        .btn-swal-cancel:hover {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
        }

        /* ========================================================
           Floating Toast Notification Customization (Stripe/Linear)
           ======================================================== */
        .swal2-container.swal2-top-end,
        .swal2-container.swal2-top-right {
            top: 20px !important;
            right: 20px !important;
            padding: 0 !important;
            pointer-events: none !important;
            background: transparent !important;
            z-index: 99999 !important;
        }
        .swal2-popup.swal2-toast {
            position: relative !important;
            box-sizing: border-box !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            width: auto !important;
            min-width: 320px !important;
            max-width: 480px !important;
            padding: 13px 16px !important;
            background: #ffffff !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.05) !important;
            overflow: hidden !important;
            pointer-events: all !important;
        }

        /* Responsive adjustments for mobile */
        @media (max-width: 576px) {
            .swal2-container.swal2-top-end,
            .swal2-container.swal2-top-right {
                top: 12px !important;
                right: 12px !important;
                left: 12px !important;
            }
            .swal2-popup.swal2-toast {
                min-width: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* Subtle Brand Border Tinting */
        .swal2-popup.swal2-toast.swal2-icon-success {
            border-color: #fecdd3 !important;
            box-shadow: 0 10px 25px -5px rgba(217, 4, 41, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.05) !important;
        }
        .swal2-popup.swal2-toast.swal2-icon-error {
            border-color: #fee2e2 !important;
        }
        .swal2-popup.swal2-toast.swal2-icon-warning {
            border-color: #fef3c7 !important;
        }
        .swal2-popup.swal2-toast.swal2-icon-info {
            border-color: #cbd5e1 !important;
        }

        /* Order elements properly in Flexbox */
        .swal2-popup.swal2-toast .swal2-icon {
            order: 1 !important;
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            max-width: 32px !important;
            margin: 0 12px 0 0 !important;
            padding: 0 !important;
            border-radius: 50% !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            flex-shrink: 0 !important;
        }

        /* Hide SweetAlert2's messy internal animated SVG lines and rings */
        .swal2-popup.swal2-toast .swal2-icon * {
            display: none !important;
        }

        /* Pixel-perfect Crisp FontAwesome Icon via ::after */
        .swal2-popup.swal2-toast .swal2-icon::after {
            font-family: 'Font Awesome 6 Free' !important;
            font-weight: 900 !important;
            font-size: 13.5px !important;
            line-height: 1 !important;
            display: inline-block !important;
        }

        /* Success Theme (AutoLogos Signature Red) */
        .swal2-popup.swal2-toast .swal2-icon.swal2-success {
            background-color: #fff1f2 !important;
            border: 1px solid #fecdd3 !important;
        }
        .swal2-popup.swal2-toast .swal2-icon.swal2-success::after {
            content: "\f00c" !important; /* fa-check */
            color: #d90429 !important;
        }

        /* Error Theme */
        .swal2-popup.swal2-toast .swal2-icon.swal2-error {
            background-color: #fef2f2 !important;
            border: 1px solid #fecaca !important;
        }
        .swal2-popup.swal2-toast .swal2-icon.swal2-error::after {
            content: "\f00d" !important; /* fa-xmark */
            color: #dc2626 !important;
        }

        /* Warning Theme */
        .swal2-popup.swal2-toast .swal2-icon.swal2-warning {
            background-color: #fffbeb !important;
            border: 1px solid #fde68a !important;
        }
        .swal2-popup.swal2-toast .swal2-icon.swal2-warning::after {
            content: "\f12a" !important; /* fa-exclamation */
            color: #d97706 !important;
        }

        /* Info Theme (Dark Charcoal) */
        .swal2-popup.swal2-toast .swal2-icon.swal2-info {
            background-color: #0b0f19 !important;
        }
        .swal2-popup.swal2-toast .swal2-icon.swal2-info::after {
            content: "\f129" !important; /* fa-info */
            color: #ffffff !important;
        }

        /* Question Theme */
        .swal2-popup.swal2-toast .swal2-icon.swal2-question {
            background-color: #f8fafc !important;
        }
        .swal2-popup.swal2-toast .swal2-icon.swal2-question::after {
            content: "\3f" !important;
            color: #64748b !important;
        }

        /* Toast Title & Text - 100% Dead Center Vertically */
        .swal2-popup.swal2-toast .swal2-title {
            order: 2 !important;
            flex: 1 1 auto !important;
            margin: 0 !important;
            padding: 0 10px 0 0 !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            line-height: 1.35 !important;
            text-align: left !important;
            display: flex !important;
            align-items: center !important;
            align-self: center !important;
            word-break: break-word !important;
        }
        .swal2-popup.swal2-toast .swal2-html-container {
            display: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Toast Close Button - Pinned to the Right Edge and Centered Vertically */
        .swal2-popup.swal2-toast .swal2-close {
            order: 3 !important;
            margin: 0 0 0 auto !important;
            padding: 0 !important;
            width: 24px !important;
            height: 24px !important;
            min-width: 24px !important;
            border-radius: 6px !important;
            color: #94a3b8 !important;
            font-size: 18px !important;
            font-family: Arial, sans-serif !important;
            line-height: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            border: none !important;
            background: transparent !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
            outline: none !important;
            box-shadow: none !important;
            flex-shrink: 0 !important;
        }
        .swal2-popup.swal2-toast .swal2-close:hover {
            color: #d90429 !important;
            background-color: #fff1f2 !important;
        }
        .swal2-popup.swal2-toast .swal2-close:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        /* Sleek Bottom Progress Bar (AutoLogos Signature Red) */
        .swal2-popup.swal2-toast .swal2-timer-progress-bar-container {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 3px !important;
            background: rgba(241, 245, 249, 0.7) !important;
            border-bottom-left-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
            overflow: hidden !important;
            z-index: 5 !important;
        }
        .swal2-popup.swal2-toast .swal2-timer-progress-bar {
            height: 3px !important;
            border-bottom-left-radius: 12px !important;
        }
        .swal2-toast.swal2-icon-success .swal2-timer-progress-bar {
            background: #d90429 !important;
        }
        .swal2-toast.swal2-icon-error .swal2-timer-progress-bar {
            background: #ef4444 !important;
        }
        .swal2-toast.swal2-icon-warning .swal2-timer-progress-bar {
            background: #f59e0b !important;
        }
        .swal2-toast.swal2-icon-info .swal2-timer-progress-bar {
            background: #0b0f19 !important;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: #94a3b8;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }

        .admin-sidebar .brand-header {
            padding: 20px;
            background: #060911;
            border-bottom: 1px solid #1e293b;
        }

        .admin-sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .admin-sidebar .nav-link:hover {
            color: #ffffff;
            background: var(--sidebar-hover);
            border-left-color: var(--admin-red);
        }

        .admin-sidebar .nav-link.active {
            color: #ffffff;
            background: rgba(217, 4, 41, 0.15);
            border-left-color: var(--admin-red);
        }

        .admin-sidebar .nav-header-text {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 16px 20px 6px;
            color: #475569;
            font-weight: 700;
        }

        /* Main Content Wrapper */
        .admin-main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .admin-topbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 30px;
            position: sticky;
            top: 0;
            z-index: 990;
        }

        .font-sm {
            font-size: 13px;
        }

        .btn-theme-red {
            background: var(--admin-red);
            color: #ffffff;
            font-weight: 700;
            border: none;
        }
        .btn-theme-red:hover {
            background: #ef233c;
            color: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar shadow">
        <div class="brand-header d-flex align-items-center justify-content-between">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2">
                @if($adminLogo && file_exists(public_path($adminLogo)))
                    <div class="bg-white rounded p-1 d-inline-flex align-items-center justify-content-center" style="max-height: 40px; min-width: 40px;">
                        <img src="{{ asset($adminLogo) }}" alt="{{ $adminStoreName }}" style="max-height: 32px; max-width: 140px; object-fit: contain;">
                    </div>
                    <div>
                        <div class="fw-bold brand-font text-white" style="font-size: 14px;">{{ $adminStoreName }}</div>
                        <div class="text-secondary" style="font-size: 8px; letter-spacing: 1.5px;">ADMIN PANEL</div>
                    </div>
                @else
                    <div>
                        <div class="fw-bold brand-font text-white fs-5">
                            @php
                                $words = explode(' ', $adminStoreName, 2);
                            @endphp
                            @if(count($words) == 2)
                                {{ $words[0] }}<span class="text-danger">{{ $words[1] }}</span>
                            @else
                                {{ $adminStoreName }}
                            @endif
                        </div>
                        <div class="text-secondary small" style="font-size: 9px; letter-spacing: 2px;">ADMIN CONTROL CENTER</div>
                    </div>
                @endif
            </a>
        </div>

        <nav class="nav flex-column py-2">
            <div class="nav-header-text">Dashboards</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard*') && !request()->routeIs('admin.catalog*') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i> Sales & Orders
            </a>
            <a href="{{ route('admin.catalog.analytics') }}" class="nav-link {{ request()->routeIs('admin.catalog.analytics*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie text-danger"></i> Catalog Analytics
                <span class="badge bg-danger ms-auto" style="font-size: 8px; letter-spacing: 0.5px;">LIVE</span>
            </a>
            <a href="{{ route('home') }}" target="_blank" class="nav-link">
                <i class="fa-solid fa-arrow-up-right-from-square text-warning"></i> View Store Live
            </a>

            <div class="nav-header-text">Vehicle Management</div>
            <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands*') ? 'active' : '' }}">
                <i class="fa-solid fa-copyright"></i> Companies (Brands)
            </a>
            <a href="{{ route('admin.models.index') }}" class="nav-link {{ request()->routeIs('admin.models*') ? 'active' : '' }}">
                <i class="fa-solid fa-car-side"></i> Car Models
            </a>
            <a href="{{ route('admin.variants.index') }}" class="nav-link {{ request()->routeIs('admin.variants*') ? 'active' : '' }}">
                <i class="fa-solid fa-code-fork"></i> Car Variants
            </a>

            <div class="nav-header-text">Catalog & Inventory</div>
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                <i class="fa-solid fa-box-open"></i> All Products
            </a>
            <a href="{{ route('admin.products.create') }}" class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                <i class="fa-solid fa-plus-circle"></i> Add Product
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Categories
            </a>

            <div class="nav-header-text">Sales & Logistics</div>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i> Orders & Shipments
            </a>
            <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Customers
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}">
                <i class="fa-solid fa-ticket"></i> Coupons & Promos
            </a>

            <div class="nav-header-text">Marketing & CMS</div>
            <a href="{{ route('admin.banners.index') }}" class="nav-link {{ request()->routeIs('admin.banners*') ? 'active' : '' }}">
                <i class="fa-solid fa-images"></i> Hero & Banners
            </a>
            <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                <i class="fa-solid fa-star"></i> Reviews Moderation
            </a>
            <a href="{{ route('admin.reports.sales') }}" class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Sales Reports
            </a>
            <a href="{{ route('admin.pages.index') }}" class="nav-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> CMS Pages
            </a>
            <a href="{{ route('admin.inquiries.index') }}" class="nav-link {{ request()->routeIs('admin.inquiries*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope"></i> Inquiries
            </a>

            <div class="nav-header-text">Settings</div>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Store Settings
            </a>

            <div class="p-3 mt-3">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main-wrapper">
        <!-- Topbar -->
        <header class="admin-topbar d-flex justify-content-between align-items-center shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <strong class="font-sm d-block text-dark">{{ Auth::user()->name ?? 'Administrator' }}</strong>
                        <span class="badge bg-danger" style="font-size: 10px;">{{ Auth::user()->role->name ?? 'Admin' }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Body -->
        <main class="p-4 flex-grow-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-muted font-sm">
            AutoLogos Admin Control Center © {{ date('Y') }}. Production-Ready E-Commerce Architecture.
        </footer>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
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

        // Global showToast helper
        window.showToast = function (msg, type = 'success') {
            const iconType = ['success', 'error', 'warning', 'info', 'question'].includes(type) ? type : 'info';
            Toast.fire({
                icon: iconType,
                title: msg
            });
        };

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
                const btnText = form.getAttribute('data-confirm-btn') || 'Yes, delete';

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

        // Flash message handling via SweetAlert2
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
    @stack('scripts')
</body>
</html>
