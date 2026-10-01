@extends('layouts.app')

@section('title', "Select {$company->name} Car Model - AutoLogos")
@section('meta_description', "Choose your {$company->name} vehicle model to view genuine emblems, logos and monograms.")

@section('content')
<!-- Breadcrumbs -->
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles') }}" class="text-decoration-none text-muted">Vehicles</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $company->name }}</li>
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
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle bg-danger text-white fs-6" style="width: 32px; height: 32px; line-height: 22px;">2</span>
                    <span class="fw-bold text-danger d-none d-md-inline font-sm">Select Car</span>
                </div>
            </div>
            <div class="col-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle bg-light text-muted border fs-6" style="width: 32px; height: 32px; line-height: 22px;">3</span>
                    <span class="fw-semibold text-muted d-none d-md-inline font-sm">Select Variant</span>
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
            <i class="fa-solid fa-car-side me-1"></i> Step 2 of 4
        </span>
        <h1 class="fw-black brand-font text-uppercase text-dark" style="font-size: calc(1.5rem + 1vw);">
            Select Your <span class="text-danger">{{ $company->name }}</span> Car
        </h1>
        <p class="text-muted font-sm mb-0">Choose your specific vehicle model to explore matching variants and parts.</p>
    </div>

    <!-- Cars Grid -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
        @forelse($cars as $car)
            <div class="col">
                <a href="{{ route('vehicle.variants', [$company->slug, $car->slug]) }}" class="text-decoration-none">
                    <div class="card h-100 border shadow-sm rounded-3 text-center p-3 vehicle-car-card" style="transition: all 0.25s ease;">
                        <div class="d-flex align-items-center justify-content-center p-2 mb-2" style="height: 80px;">
                            @if($car->badge_image)
                                <img src="{{ asset($car->badge_image) }}" alt="{{ $car->name }}" class="img-fluid" style="max-height: 60px; max-width: 100px; object-fit: contain;">
                            @else
                                <div class="p-2 rounded bg-dark text-white fw-black brand-font fst-italic w-100" style="letter-spacing: 1px;">
                                    {{ strtoupper($car->name) }}
                                </div>
                            @endif
                        </div>
                        <div class="fw-bold text-dark fs-6 mb-1">{{ $car->name }}</div>
                        <div class="d-flex justify-content-center gap-1">
                            <span class="badge bg-light text-primary border rounded-pill small">
                                <i class="fa-solid fa-code-fork me-1"></i> {{ $car->variants_count }} Variants
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-car-side text-muted display-4 mb-3"></i>
                <h5 class="fw-bold">No Car Models Found for {{ $company->name }}</h5>
                <p class="text-muted">We are actively updating models for this brand. Please check back shortly.</p>
                <a href="{{ route('vehicles') }}" class="btn btn-outline-danger btn-sm mt-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Brands
                </a>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.vehicle-car-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-red) !important;
    box-shadow: 0 12px 24px rgba(217, 4, 41, 0.15) !important;
}
</style>
@endpush
@endsection
