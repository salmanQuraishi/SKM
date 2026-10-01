@extends('layouts.app')

@section('title', "Select {$company->name} {$car->name} Variant - AutoLogos")
@section('meta_description', "Choose your {$company->name} {$car->name} variant (Pure, Adventure, Accomplished, etc.) for tailored vehicle parts and emblems.")

@section('content')
<!-- Breadcrumbs -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles') }}" class="text-decoration-none text-muted">Vehicles</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicle.cars', $company->slug) }}" class="text-decoration-none text-muted">{{ $company->name }}</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $car->name }} Variants</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <!-- Step Indicator Progress Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <div class="row text-center g-2 align-items-center">
            <div class="col-3">
                <a href="{{ route('vehicles') }}" class="text-decoration-none d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle bg-success text-white fs-6" style="width: 32px; height: 32px; line-height: 22px;"><i class="fa-solid fa-check"></i></span>
                    <span class="fw-semibold text-dark d-none d-md-inline font-sm">{{ $company->name }}</span>
                </a>
            </div>
            <div class="col-3">
                <a href="{{ route('vehicle.cars', $company->slug) }}" class="text-decoration-none d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle bg-success text-white fs-6" style="width: 32px; height: 32px; line-height: 22px;"><i class="fa-solid fa-check"></i></span>
                    <span class="fw-semibold text-dark d-none d-md-inline font-sm">{{ $car->name }}</span>
                </a>
            </div>
            <div class="col-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle bg-danger text-white fs-6" style="width: 32px; height: 32px; line-height: 22px;">3</span>
                    <span class="fw-bold text-danger d-none d-md-inline font-sm">Select Variant</span>
                </div>
            </div>
            <div class="col-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle bg-light text-muted border fs-6" style="width: 32px; height: 32px; line-height: 22px;">4</span>
                    <span class="fw-semibold text-muted d-none d-md-inline font-sm">View Products</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Title -->
    <div class="text-center mb-5">
        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold mb-2 font-sm text-uppercase">
            <i class="fa-solid fa-code-fork me-1"></i> Step 3 of 4
        </span>
        <h1 class="fw-black brand-font text-uppercase text-dark" style="font-size: calc(1.5rem + 1vw);">
            Choose Your <span class="text-danger">{{ $company->name }} {{ $car->name }}</span> Variant
        </h1>
        <p class="text-muted font-sm mb-0">Select your specific car trim or model variant to see exact compatible products.</p>
    </div>

    <!-- Variants Grid -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        @forelse($variants as $variant)
            <div class="col">
                <a href="{{ route('vehicle.products', [$company->slug, $car->slug, $variant->slug]) }}" class="text-decoration-none">
                    <div class="card h-100 border shadow-sm rounded-3 p-4 vehicle-variant-card" style="transition: all 0.25s ease;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-danger-subtle text-danger fw-bold font-sm">
                                <i class="fa-solid fa-car me-1"></i> {{ $car->name }}
                            </span>
                            <span class="badge bg-light text-muted border rounded-pill small">
                                {{ $variant->products_count }} Products
                            </span>
                        </div>
                        <h4 class="fw-bold text-dark mb-2 brand-font fs-5">{{ $variant->name }}</h4>
                        <div class="text-secondary small mb-3">
                            Compatible with {{ $company->name }} {{ $car->name }} {{ $variant->name }}
                        </div>
                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center text-danger fw-bold font-sm">
                            <span>Browse Compatible Products</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-code-fork text-muted display-4 mb-3"></i>
                <h5 class="fw-bold">No Variants Currently Available for {{ $company->name }} {{ $car->name }}</h5>
                <p class="text-muted">Variants for this model are being updated. You can still browse universal products.</p>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <a href="{{ route('vehicle.cars', $company->slug) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Choose Another Car
                    </a>
                    <a href="{{ route('shop', ['model' => $car->slug]) }}" class="btn btn-theme-red btn-sm">
                        <i class="fa-solid fa-box-open me-1"></i> View All {{ $car->name }} Products
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.vehicle-variant-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-red) !important;
    box-shadow: 0 12px 24px rgba(217, 4, 41, 0.15) !important;
}
</style>
@endpush
@endsection
