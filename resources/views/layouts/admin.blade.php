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

        <!-- Flash Messages -->
        <div class="container-fluid px-4 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

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
    @stack('scripts')
</body>
</html>
