<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AutoLogos - Genuine Car Logos, Monograms & Emblems')</title>
    <meta name="description" content="@yield('meta_description', 'India\'s leading online specialist for genuine car logos, chrome emblems, car monograms, and custom badges.')">
    
    @php
        $appLogo = \App\Models\Setting::get('site_logo');
        $appFavicon = \App\Models\Setting::get('site_favicon');
        $appName = \App\Models\Setting::get('store_name', 'AutoLogos');
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
    
    <style>
        :root {
            --primary-red: #d90429;
            --primary-red-hover: #ef233c;
            --dark-black: #0b0f19;
            --header-black: #11141d;
            --card-border: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        /* ================= SELECT2 CUSTOM STYLING ================= */
        /* Only in-page Select2 trigger containers should fill their parent column */
        .select2.select2-container {
            width: 100% !important;
            font-size: 13px;
        }

        /* Prevent dropdowns attached to body from inheriting 100% width or causing page horizontal scroll */
        body > .select2-container {
            font-size: 13px;
            max-width: 100vw;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b;
            line-height: normal;
            padding-left: 12px;
            padding-right: 28px;
            font-weight: 500;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #64748b transparent transparent transparent;
            border-width: 5px 4px 0 4px;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #d90429 transparent;
            border-width: 0 4px 5px 4px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary-red) !important;
            box-shadow: 0 0 0 3px rgba(217, 4, 41, 0.15);
            outline: none;
        }

        /* Small select variant */
        .select2-container-sm .select2-selection--single,
        .select2-container--default.select2-container-sm .select2-selection--single {
            height: 32px !important;
            font-size: 12.5px;
        }
        .select2-container-sm .select2-selection--single .select2-selection__arrow,
        .select2-container--default.select2-container-sm .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }

        /* Quick Vehicle Selector (Hero section) Dark Theme Inputs */
        #vehicleQuickSearchForm .select2.select2-container {
            width: 100% !important;
        }
        #vehicleQuickSearchForm .select2-container--default .select2-selection--single {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            border-radius: 6px !important;
            height: 42px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease !important;
        }
        #vehicleQuickSearchForm .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff !important;
            line-height: 40px !important;
            padding-left: 14px !important;
            padding-right: 28px !important;
            font-weight: 500 !important;
            font-size: 13px !important;
        }
        #vehicleQuickSearchForm .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
        }
        #vehicleQuickSearchForm .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 10px !important;
        }
        #vehicleQuickSearchForm .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #94a3b8 transparent transparent transparent !important;
            border-width: 5px 4px 0 4px !important;
        }
        #vehicleQuickSearchForm .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #ef4444 transparent !important;
            border-width: 0 4px 5px 4px !important;
        }
        #vehicleQuickSearchForm .select2-container--default.select2-container--focus .select2-selection--single,
        #vehicleQuickSearchForm .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25) !important;
        }
        #vehicleQuickSearchForm .select2-container--default.select2-container--open .select2-selection--single {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        #vehicleQuickSearchForm .select2-container--default.select2-container--disabled .select2-selection--single {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            opacity: 0.55 !important;
            cursor: not-allowed !important;
        }
        #vehicleQuickSearchForm .select2-container--default.select2-container--disabled .select2-selection--single .select2-selection__rendered {
            color: #64748b !important;
        }

        /* Quick Vehicle Selector Dropdown (Dark Theme) */
        .quick-search-dropdown.select2-dropdown {
            background-color: #111827 !important;
            border: 1px solid #ef4444 !important;
            border-top: none !important;
            border-radius: 0 0 8px 8px !important;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.65) !important;
            color: #f8fafc !important;
            z-index: 999999 !important;
            overflow: hidden !important;
            font-size: 13px !important;
            box-sizing: border-box !important;
            margin-top: -1px;
        }
        .quick-search-dropdown .select2-search--dropdown {
            background-color: #0b0f19 !important;
            padding: 8px 10px !important;
            border-bottom: 1px solid #1f2937 !important;
        }
        .quick-search-dropdown .select2-search--dropdown .select2-search__field {
            background-color: #1f2937 !important;
            border: 1px solid #374151 !important;
            border-radius: 6px !important;
            color: #ffffff !important;
            padding: 7px 12px 7px 32px !important;
            font-size: 13px !important;
            outline: none !important;
            width: 100% !important;
            box-sizing: border-box !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
            background-position: 10px center !important;
            background-repeat: no-repeat !important;
            background-size: 14px 14px !important;
            transition: all 0.2s ease;
        }
        .quick-search-dropdown .select2-search--dropdown .select2-search__field::placeholder {
            color: #64748b !important;
            font-size: 12.5px;
        }
        .quick-search-dropdown .select2-search--dropdown .select2-search__field:focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.25) !important;
            background-color: #111827 !important;
        }

        /* Sleek Dark Scrollbar for Quick Search Dropdown */
        .quick-search-dropdown .select2-results__options {
            max-height: 230px !important;
            overflow-y: auto !important;
            padding: 6px !important;
            scrollbar-width: thin;
            scrollbar-color: #374151 #111827;
        }
        .quick-search-dropdown .select2-results__options::-webkit-scrollbar {
            width: 6px;
        }
        .quick-search-dropdown .select2-results__options::-webkit-scrollbar-track {
            background: #111827;
        }
        .quick-search-dropdown .select2-results__options::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 4px;
        }
        .quick-search-dropdown .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }

        /* Items inside Quick Search Dropdown */
        .quick-search-dropdown .select2-results__option {
            padding: 8px 12px !important;
            font-size: 13px !important;
            color: #e2e8f0 !important;
            border-radius: 5px !important;
            margin-bottom: 2px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }
        .quick-search-dropdown .select2-results__option--highlighted[aria-selected] {
            background-color: #d90429 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }
        .quick-search-dropdown .select2-results__option[aria-selected="true"] {
            background-color: rgba(217, 4, 41, 0.2) !important;
            color: #fca5a5 !important;
            font-weight: 600 !important;
        }
        .quick-search-dropdown .select2-results__option--highlighted[aria-selected="true"] {
            background-color: #d90429 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        /* Hide empty placeholder item from the dropdown list */
        .select2-container--default .select2-results__option[aria-disabled="true"],
        .quick-search-dropdown .select2-results__option:first-child[aria-selected="true"][id$="-"] {
            display: none !important;
        }

        /* Standard Light Dropdowns (Shop filters, sorting, account) */
        .select2-dropdown-light.select2-dropdown,
        .select2-dropdown {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            z-index: 999999 !important;
            overflow: hidden;
            font-size: 13px;
            box-sizing: border-box;
        }
        .select2-dropdown-light .select2-search--dropdown,
        .select2-dropdown .select2-search--dropdown {
            padding: 8px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .select2-dropdown-light .select2-search--dropdown .select2-search__field,
        .select2-dropdown .select2-search--dropdown .select2-search__field {
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 6px 10px;
            font-size: 12.5px;
            outline: none;
            width: 100% !important;
            box-sizing: border-box;
        }
        .select2-dropdown-light .select2-search--dropdown .select2-search__field:focus,
        .select2-dropdown .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 2px rgba(217, 4, 41, 0.12);
        }
        .select2-dropdown-light .select2-results__options,
        .select2-dropdown .select2-results__options {
            max-height: 220px;
            overflow-y: auto;
            padding: 4px 0;
        }
        .select2-dropdown-light .select2-results__option,
        .select2-dropdown .select2-results__option {
            padding: 7px 12px;
            font-size: 13px;
            color: #1e293b;
        }
        .select2-dropdown-light .select2-results__option--highlighted[aria-selected],
        .select2-dropdown .select2-results__option--highlighted[aria-selected] {
            background-color: #fff1f2 !important;
            color: var(--primary-red) !important;
            font-weight: 600;
        }
        .select2-dropdown-light .select2-results__option[aria-selected="true"],
        .select2-dropdown .select2-results__option[aria-selected="true"] {
            background-color: var(--primary-red) !important;
            color: #ffffff !important;
            font-weight: 700;
        }

        html, body {
            overflow-x: hidden;
            max-width: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: #f8fafc;
        }

        .brand-font {
            font-family: 'Montserrat', sans-serif;
        }

        /* Top Bar */
        .top-bar {
            background-color: #0b0f19;
            color: #cbd5e1;
            font-size: 13px;
            padding: 7px 0;
            border-bottom: 1px solid #1e293b;
        }
        .top-bar a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .top-bar a:hover {
            color: #ffffff;
        }

        /* Main Header */
        .main-header {
            background: #ffffff;
            padding: 16px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .logo-text {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #0b0f19;
            text-transform: uppercase;
            line-height: 1;
        }
        .logo-text span {
            color: var(--primary-red);
        }
        .logo-subtext {
            font-size: 9px;
            letter-spacing: 2.5px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Search Bar */
        .header-search-form {
            display: flex;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            background: #ffffff;
            transition: border-color 0.2s;
        }
        .header-search-form:focus-within {
            border-color: var(--primary-red);
        }
        .search-category-select {
            border: none;
            border-right: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            outline: none;
            cursor: pointer;
            max-width: 150px;
        }
        .header-search-input {
            border: none;
            padding: 10px 16px;
            font-size: 14px;
            width: 100%;
            outline: none;
        }
        .header-search-btn {
            background: var(--primary-red);
            color: #ffffff;
            border: none;
            padding: 0 24px;
            font-size: 16px;
            transition: background 0.2s;
        }
        .header-search-btn:hover {
            background: var(--primary-red-hover);
        }

        /* Nav Header */
        .nav-header {
            background: var(--dark-black);
            border-bottom: 2px solid var(--primary-red);
        }
        .category-trigger-btn {
            background: var(--primary-red);
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.5px;
            padding: 14px 20px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s;
            text-transform: uppercase;
        }
        .category-trigger-btn:hover {
            background: var(--primary-red-hover);
        }
        .main-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap;
            width: 100%;
        }
        .main-nav .nav-item {
            flex-shrink: 0;
            white-space: nowrap;
        }
        .main-nav .nav-link {
            color: #ffffff;
            font-weight: 600;
            font-size: 12.5px;
            text-transform: uppercase;
            padding: 13px 8px !important;
            letter-spacing: 0.2px;
            transition: color 0.2s;
            position: relative;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }
        @media (min-width: 1200px) {
            .main-nav .nav-link {
                font-size: 13px;
                padding: 13px 11px !important;
                letter-spacing: 0.3px;
            }
        }
        @media (min-width: 1400px) {
            .main-nav .nav-link {
                padding: 13px 14px !important;
            }
        }
        .main-nav .nav-link:hover, .main-nav .nav-link.active {
            color: var(--primary-red);
        }

        /* Left Category & Vehicle Menu */
        .sidebar-category-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .sidebar-category-card .cat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 16px;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .sidebar-category-card .cat-item:hover {
            background-color: #fff1f2;
            color: var(--primary-red);
            padding-left: 20px;
        }
        .sidebar-category-card .cat-item i.cat-icon {
            width: 24px;
            color: #64748b;
            font-size: 14px;
            margin-right: 8px;
        }
        .sidebar-category-card .cat-item:hover i.cat-icon {
            color: var(--primary-red);
        }

        /* Vehicle Mega Menu Styles */
        .vehicle-mega-menu-wrapper {
            position: relative;
        }
        .vehicle-menu-list {
            position: relative;
        }
        .vehicle-menu-item {
            position: static;
            border-bottom: 1px solid #f1f5f9;
        }
        @media (min-width: 992px) {
            .vehicle-menu-item {
                position: relative;
            }
        }
        .vehicle-menu-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            color: #1e293b;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .vehicle-menu-link:hover,
        .vehicle-menu-item:hover > .vehicle-menu-link {
            background-color: #fff1f2;
            color: var(--primary-red) !important;
            padding-left: 18px;
        }
        .vehicle-menu-link:hover .brand-name,
        .vehicle-menu-item:hover > .vehicle-menu-link .brand-name {
            color: var(--primary-red) !important;
        }
        .vehicle-menu-link:hover .arrow-icon,
        .vehicle-menu-item:hover > .vehicle-menu-link .arrow-icon {
            color: var(--primary-red) !important;
            transform: translateX(2px);
        }
        .vehicle-flyout-pane {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            width: 580px;
            min-height: 100%;
            max-height: 480px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 3px solid var(--primary-red);
            border-radius: 0 10px 10px 10px;
            padding: 16px;
            z-index: 99999 !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.18), 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        /* Invisible hover bridge to prevent mouse leaving flyout */
        .vehicle-flyout-pane::before {
            content: '';
            position: absolute;
            top: 0;
            left: -15px;
            width: 15px;
            height: 100%;
            background: transparent;
        }
        /* Custom scrollbar for flyout */
        .vehicle-flyout-pane::-webkit-scrollbar {
            width: 6px;
        }
        .vehicle-flyout-pane::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }
        .vehicle-flyout-pane::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .vehicle-flyout-pane::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .vehicle-menu-item:hover > .vehicle-flyout-pane {
            display: block;
        }
        /* Smart upward positioning for bottom half items */
        .vehicle-menu-item:nth-child(n+6) > .vehicle-flyout-pane,
        #moreVehicleBrands .vehicle-menu-item > .vehicle-flyout-pane {
            top: auto;
            bottom: -1px;
            border-radius: 10px 10px 10px 0;
        }
        #moreVehicleBrands.show {
            overflow: visible !important;
        }
        .flyout-car-box {
            transition: all 0.15s ease;
        }
        .flyout-car-box:hover {
            border-color: #cbd5e1;
            background: #ffffff !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .flyout-variant-pill {
            display: inline-block;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .flyout-variant-pill:hover {
            background: var(--primary-red);
            color: #ffffff;
            border-color: var(--primary-red);
            transform: translateY(-1px);
        }
        .hover-red:hover {
            color: var(--primary-red) !important;
        }

        /* Product Cards */
        .product-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }
        .product-card .img-wrapper {
            position: relative;
            background: #ffffff;
            padding: 20px;
            text-align: center;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-card .img-wrapper img {
            max-height: 140px;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s;
        }
        .product-card:hover .img-wrapper img {
            transform: scale(1.05);
        }
        .product-card .card-body {
            padding: 14px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .product-card .product-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            line-height: 1.3;
            text-decoration: none;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 36px;
        }
        .product-card .product-title:hover {
            color: var(--primary-red);
        }
        .product-card .product-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .product-card .price-tag {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        .product-card .old-price {
            font-size: 13px;
            color: #94a3b8;
            text-decoration: line-through;
            margin-left: 6px;
        }

        /* Buttons */
        .btn-theme-red {
            background: var(--primary-red);
            color: #ffffff;
            font-weight: 700;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.2s;
            text-transform: uppercase;
            font-size: 13px;
        }
        .btn-theme-red:hover {
            background: var(--primary-red-hover);
            color: #ffffff;
        }

        /* Footer */
        .site-footer {
            background: #0b0f19;
            color: #94a3b8;
            padding-top: 50px;
            border-top: 3px solid var(--primary-red);
            font-size: 13px;
        }
        .site-footer h5 {
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .site-footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
            display: block;
            margin-bottom: 8px;
        }
        .site-footer a:hover {
            color: #ffffff;
            padding-left: 4px;
        }
        .footer-bottom {
            background: #060911;
            padding: 18px 0;
            border-top: 1px solid #1e293b;
            font-size: 12px;
        }

        /* Autocomplete dropdown */
        #searchSuggestionsBox {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-radius: 0 0 8px 8px;
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }
        .search-item-row {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            color: #1e293b;
            transition: background 0.2s;
        }
        .search-item-row:hover {
            background: #f8fafc;
        }
        .search-item-row img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 12px;
        }

        /* WhatsApp floating badge */
        .whatsapp-float {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #25d366;
            color: #ffffff;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4);
            z-index: 999;
            text-decoration: none;
            transition: transform 0.2s;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            color: #ffffff;
        }
    </style>
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
                            <img src="{{ asset($appLogo) }}" alt="{{ $appName }}" class="site-header-logo" style="max-height: 50px; max-width: 220px; object-fit: contain;">
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
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop') && !request('category') && !request('sort') ? 'active' : '' }}" href="{{ route('shop') }}">ALL PRODUCTS</a></li>
                <li class="nav-item"><a class="nav-link {{ request('category') == 'front-logo' ? 'active' : '' }}" href="{{ route('shop', ['category' => 'front-logo']) }}">FRONT LOGO</a></li>
                <li class="nav-item"><a class="nav-link {{ request('category') == 'rear-logo' ? 'active' : '' }}" href="{{ route('shop', ['category' => 'rear-logo']) }}">REAR LOGO</a></li>
                <li class="nav-item"><a class="nav-link {{ request('category') == 'bonnet-tailgate-letters' ? 'active' : '' }}" href="{{ route('shop', ['category' => 'bonnet-tailgate-letters']) }}">BONNET & LETTERS</a></li>
                <li class="nav-item"><a class="nav-link {{ request('category') == 'kit-full-monogram' ? 'active' : '' }}" href="{{ route('shop', ['category' => 'kit-full-monogram']) }}">FULL MONOGRAM KITS</a></li>
                <li class="nav-item"><a class="nav-link {{ request('category') == 'single-name-badge' ? 'active' : '' }}" href="{{ route('shop', ['category' => 'single-name-badge']) }}">SINGLE BADGES</a></li>
                <li class="nav-item"><a class="nav-link text-warning {{ request('sort') == 'newest' ? 'active' : '' }}" href="{{ route('shop', ['sort' => 'newest']) }}"><i class="fa-solid fa-sparkles me-1"></i> NEW ARRIVALS</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}"><i class="fa-solid fa-headset me-1 text-danger"></i> CONTACT US</a></li>
            </ul>
        </div>
    </nav>

    <!-- Flashed Session Alerts -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- WhatsApp Floating Button -->
    <a href="https://api.whatsapp.com/send?phone={{ $appWhatsappClean }}&text=Hello%20{{ urlencode($appName) }},%20I%20have%20an%20inquiry%20regarding%20car%20emblems." target="_blank" class="whatsapp-float shadow" title="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Mobile Offcanvas Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header bg-dark text-white">
            @if($appLogo && file_exists(public_path($appLogo)))
                <img src="{{ asset($appLogo) }}" alt="{{ $appName }}" style="max-height: 38px; max-width: 150px; object-fit: contain;">
            @else
                <h5 class="offcanvas-title brand-font">{{ $appName }}</h5>
            @endif
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ route('home') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-house me-2 text-danger"></i> Home</a></li>
                <li class="list-group-item"><a href="{{ route('vehicles') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-car-side me-2 text-danger"></i> Vehicles / Brands</a></li>
                <li class="list-group-item"><a href="{{ route('shop') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-border-all me-2 text-danger"></i> All Products</a></li>
                <li class="list-group-item"><a href="{{ route('shop', ['category' => 'front-logo']) }}" class="text-decoration-none text-dark"><i class="fa-solid fa-shield-halved me-2 text-danger"></i> Front Logo</a></li>
                <li class="list-group-item"><a href="{{ route('shop', ['category' => 'rear-logo']) }}" class="text-decoration-none text-dark"><i class="fa-solid fa-car-rear me-2 text-danger"></i> Rear Logo</a></li>
                <li class="list-group-item"><a href="{{ route('shop', ['category' => 'bonnet-tailgate-letters']) }}" class="text-decoration-none text-dark"><i class="fa-solid fa-font me-2 text-danger"></i> Bonnet & Tailgate Letters</a></li>
                <li class="list-group-item"><a href="{{ route('shop', ['category' => 'kit-full-monogram']) }}" class="text-decoration-none text-dark"><i class="fa-solid fa-boxes-stacked me-2 text-danger"></i> Kit Full Monogram</a></li>
                <li class="list-group-item"><a href="{{ route('shop', ['category' => 'single-name-badge']) }}" class="text-decoration-none text-dark"><i class="fa-solid fa-certificate me-2 text-danger"></i> Single Name Badges</a></li>
                <li class="list-group-item"><a href="{{ route('order.track.view') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-location-crosshairs me-2 text-danger"></i> Track Order</a></li>
                <li class="list-group-item"><a href="{{ route('contact') }}" class="text-decoration-none text-dark fw-bold"><i class="fa-solid fa-envelope me-2 text-danger"></i> Contact Us</a></li>
            </ul>
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
                    <a href="{{ route('shop', ['category' => 'front-logo']) }}">Front Logo</a>
                    <a href="{{ route('shop', ['category' => 'rear-logo']) }}">Rear Logo</a>
                    <a href="{{ route('shop', ['category' => 'bonnet-tailgate-letters']) }}">Bonnet & Letters</a>
                    <a href="{{ route('shop', ['category' => 'kit-full-monogram']) }}">Kit Full Monogram</a>
                    <a href="{{ route('shop', ['category' => 'single-name-badge']) }}">Single Name Badges</a>
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

    <!-- Mobile Offcanvas Menu with Vehicle Hierarchy Accordion -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header bg-dark text-white border-bottom">
            <h5 class="offcanvas-title font-sm fw-bold brand-font" id="mobileMenuLabel">
                <i class="fa-solid fa-car text-danger me-2"></i> SELECT VEHICLE & CATEGORIES
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="accordion accordion-flush" id="mobileVehicleAccordion">
                @foreach($globalBrands as $brand)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBrand{{ $brand->id }}">
                            <button class="accordion-button collapsed py-2 font-sm fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBrand{{ $brand->id }}" aria-expanded="false">
                                <img src="{{ asset($brand->logo ?: 'images/brands/' . $brand->slug . '.svg') }}" alt="{{ $brand->name }}" style="width: 20px; height: 20px; object-fit: contain;" class="me-2">
                                <span>{{ $brand->name }}</span>
                                <span class="badge bg-light text-muted border ms-auto me-2 font-sm" style="font-size: 10px;">{{ $brand->carModels->count() }}</span>
                            </button>
                        </h2>
                        <div id="collapseBrand{{ $brand->id }}" class="accordion-collapse collapse" data-bs-parent="#mobileVehicleAccordion">
                            <div class="accordion-body p-2 bg-light">
                                <a href="{{ route('vehicle.cars', $brand->slug) }}" class="btn btn-outline-danger btn-sm w-100 mb-2 py-1 font-sm fw-bold">
                                    All {{ $brand->name }} Models &rarr;
                                </a>
                                <div class="list-group list-group-flush">
                                    @foreach($brand->carModels as $car)
                                        <div class="list-group-item bg-white rounded border mb-1 p-2">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <a href="{{ route('vehicle.variants', [$brand->slug, $car->slug]) }}" class="fw-bold text-dark text-decoration-none font-sm">
                                                    <i class="fa-solid fa-car text-danger me-1"></i> {{ $car->name }}
                                                </a>
                                                <a href="{{ route('vehicle.variants', [$brand->slug, $car->slug]) }}" class="text-muted font-sm text-decoration-none" style="font-size: 11px;">
                                                    All &rarr;
                                                </a>
                                            </div>
                                            @if($car->variants->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach($car->variants as $variant)
                                                        <a href="{{ route('vehicle.products', [$brand->slug, $car->slug, $variant->slug]) }}" class="flyout-variant-pill">
                                                            {{ $variant->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-3 border-top bg-light mt-2">
                <a href="{{ route('vehicles') }}" class="btn btn-danger w-100 fw-bold font-sm mb-2">
                    <i class="fa-solid fa-car-side me-1"></i> VIEW ALL VEHICLES
                </a>
                <a href="{{ route('shop') }}" class="btn btn-outline-dark w-100 fw-bold font-sm">
                    <i class="fa-solid fa-boxes-stacked me-1"></i> ALL PRODUCTS & CATEGORIES
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
                    fetch(`{{ route('search.suggestions') }}?q=${encodeURIComponent(val)}`)
                        .then(res => res.json())
                        .then(data => {
                            let html = '';
                            if (data.products && data.products.length > 0) {
                                html += '<div class="p-2 bg-light text-uppercase fw-bold text-muted" style="font-size:11px;">Products</div>';
                                data.products.forEach(p => {
                                    html += `
                                        <a href="${p.url}" class="search-item-row">
                                            <img src="${p.image}" alt="${p.name}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder-emblem.svg') }}';">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold font-sm">${p.name}</div>
                                                <div class="text-danger fw-bold font-sm">${p.price}</div>
                                            </div>
                                        </a>
                                    `;
                                });
                            }
                            if (data.brands && data.brands.length > 0) {
                                html += '<div class="p-2 bg-light text-uppercase fw-bold text-muted" style="font-size:11px;">Brands</div>';
                                data.brands.forEach(b => {
                                    html += `
                                        <a href="${b.url}" class="search-item-row">
                                            <i class="fa-solid fa-car text-danger me-2"></i>
                                            <div class="fw-bold font-sm">${b.name}</div>
                                        </a>
                                    `;
                                });
                            }
                            if (html) {
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

        function showToast(msg, type = 'info') {
            const toastDiv = document.createElement('div');
            toastDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed bottom-0 start-50 translate-middle-x shadow-lg text-center px-4 py-2`;
            toastDiv.style.zIndex = 9999;
            toastDiv.style.minWidth = '300px';
            toastDiv.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'} me-2"></i> ${msg}`;
            document.body.appendChild(toastDiv);
            setTimeout(() => toastDiv.remove(), 3500);
        }
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
        });
    </script>
    @stack('scripts')
</body>
</html>
