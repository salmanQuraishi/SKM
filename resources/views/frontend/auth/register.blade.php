@extends('layouts.app')

@section('title', 'Customer Registration - AutoLogos')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 bg-white shadow-sm rounded-4 p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="logo-text brand-font mb-1">AUTO<span class="text-danger">LOGOS</span></div>
                    <h4 class="fw-bold text-dark">Create Your Account</h4>
                    <p class="text-muted font-sm">Sign up for faster checkout, order tracking, and exclusive discounts.</p>
                </div>

                <form action="{{ route('register.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-sm fw-bold">Full Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="yourname@gmail.com" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">10-Digit Mobile Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="9876543210" maxlength="10" value="{{ old('phone') }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-theme-red w-100 py-3 fw-bold mb-3">
                        CREATE ACCOUNT
                    </button>

                    <div class="text-center font-sm text-muted">
                        Already have an account? <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none">Log In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
