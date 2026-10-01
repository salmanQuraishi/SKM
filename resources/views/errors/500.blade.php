@extends('layouts.app')

@section('title', 'Server Error (500) - AutoLogos')

@section('content')
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="display-1 fw-bold text-danger mb-2 font-mono">500</div>
            <h3 class="fw-bold text-dark mb-3">Internal Server Error</h3>
            <p class="text-muted mb-4">
                Something unexpected happened on our server. Our engineering team has been notified.
            </p>
            <a href="{{ route('home') }}" class="btn btn-theme-red px-4 py-2 fw-bold">
                <i class="fa-solid fa-house me-2"></i> Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
