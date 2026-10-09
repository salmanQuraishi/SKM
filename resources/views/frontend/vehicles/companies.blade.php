@extends('layouts.app')

@section('title', 'Select Your Car Brand / Company - AutoLogos')
@section('meta_description', 'Choose your automobile brand to find authentic compatible car logos, chrome emblems, and monogram badges.')

@section('content')
<!-- Breadcrumbs -->
<div class="py-3 border-bottom mb-4" style="background: #090d16; border-color: #1e293b !important;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Select Vehicle Brand</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <!-- Step Indicator Progress Bar -->
    <div class="card border shadow-sm rounded-4 mb-4 p-3" style="background: #111827; border-color: #1e293b !important;">
        <div class="row text-center g-2 align-items-center">
            <div class="col-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle bg-danger text-white fs-6" style="width: 32px; height: 32px; line-height: 22px;">1</span>
                    <span class="fw-bold text-danger d-none d-md-inline font-sm">Select Brand</span>
                </div>
            </div>
            <div class="col-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle border fs-6" style="width: 32px; height: 32px; line-height: 22px; background: #161f30; border-color: #334155 !important; color: #94a3b8;">2</span>
                    <span class="fw-semibold text-secondary d-none d-md-inline font-sm">Select Car</span>
                </div>
            </div>
            <div class="col-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle border fs-6" style="width: 32px; height: 32px; line-height: 22px; background: #161f30; border-color: #334155 !important; color: #94a3b8;">3</span>
                    <span class="fw-semibold text-secondary d-none d-md-inline font-sm">Select Variant</span>
                </div>
            </div>
            <div class="col-3">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="badge rounded-circle border fs-6" style="width: 32px; height: 32px; line-height: 22px; background: #161f30; border-color: #334155 !important; color: #94a3b8;">4</span>
                    <span class="fw-semibold text-secondary d-none d-md-inline font-sm">View Products</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Title -->
    <div class="text-center mb-5">
        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold mb-2 font-sm text-uppercase">
            <i class="fa-solid fa-car me-1"></i> Step 1 of 4
        </span>
        <h1 class="fw-black brand-font text-uppercase text-white" style="font-size: calc(1.5rem + 1vw);">
            Choose Your <span class="text-danger">Car Brand</span>
        </h1>
        <p class="text-secondary font-sm mb-0">Select your car manufacturer to see compatible car models and genuine accessories.</p>
    </div>

    <!-- Companies Grid -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
        @forelse($companies as $company)
            <div class="col">
                <a href="{{ route('vehicle.cars', $company->slug) }}" class="text-decoration-none">
                    <div class="card h-100 border shadow-sm rounded-3 text-center p-3 vehicle-brand-card" style="transition: all 0.25s ease; background: #111827; border-color: #1e293b !important;">
                        <div class="d-flex align-items-center justify-content-center p-2 mb-2 rounded-2" style="height: 90px; background: #161f30; border: 1px solid #1e293b;">
                            <img src="{{ asset($company->logo ?: 'images/brands/' . $company->slug . '.svg') }}" alt="{{ $company->name }}" class="img-fluid" style="max-height: 70px; max-width: 100px; object-fit: contain;">
                        </div>
                        <div class="fw-bold text-white fs-6 mb-1">{{ $company->name }}</div>
                        <span class="badge rounded-pill small" style="background: #161f30; border: 1px solid #334155; color: #94a3b8;">
                            {{ $company->car_models_count }} Models Available
                        </span>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-car text-secondary display-4 mb-3"></i>
                <h5 class="fw-bold text-white">No Car Brands Available</h5>
                <p class="text-secondary">Please check back soon or contact support.</p>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.vehicle-brand-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-red) !important;
    box-shadow: 0 12px 24px rgba(217, 4, 41, 0.15) !important;
}
</style>
@endpush
@endsection
