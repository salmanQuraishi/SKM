@extends('layouts.app')

@section('title', 'Access Forbidden (403) - AutoLogos')

@section('content')
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="display-1 fw-bold text-warning mb-2 font-mono">403</div>
            <h3 class="fw-bold text-white mb-3">Access Denied</h3>
            <p class="text-secondary mb-4">
                You do not have administrative permissions or clearance to access this protected sector.
            </p>
            <a href="{{ route('home') }}" class="btn btn-theme-red px-4 py-2 fw-bold">
                <i class="fa-solid fa-house me-2"></i> Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
