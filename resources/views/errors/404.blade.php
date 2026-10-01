@extends('layouts.app')

@section('title', 'Page Not Found (404) - AutoLogos')

@section('content')
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="display-1 fw-bold text-danger mb-2 font-mono">404</div>
            <h3 class="fw-bold text-dark mb-3">Oops! Vehicle Emblem / Page Not Found</h3>
            <p class="text-muted mb-4">
                The page or automotive accessory you are looking for might have been removed, had its name changed, or is temporarily unavailable.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-theme-red px-4 py-2 fw-bold">
                    <i class="fa-solid fa-house me-2"></i> Back to Home
                </a>
                <a href="{{ route('shop') }}" class="btn btn-outline-dark px-4 py-2 fw-bold">
                    <i class="fa-solid fa-magnifying-glass me-2"></i> Browse Emblems
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
