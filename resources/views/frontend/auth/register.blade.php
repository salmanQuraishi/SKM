@extends('layouts.app')

@section('title', 'Customer Registration - AutoLogos')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border rounded-4 p-4 p-md-5 shadow-lg" style="background: #111827; border-color: #1e293b !important;">
                <div class="text-center mb-4">
                    <div class="logo-text brand-font mb-1 text-white">AUTO<span class="text-danger">LOGOS</span></div>
                    <h4 class="fw-bold text-white">Create Your Account</h4>
                    <p class="text-secondary font-sm">Sign up for faster checkout, order tracking, and exclusive discounts.</p>
                </div>

                <form action="{{ route('register.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-sm fw-bold text-light">Full Name *</label>
                        <input type="text" name="name" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Email Address *</label>
                            <input type="email" name="email" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="yourname@gmail.com" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">10-Digit Mobile Number</label>
                            <input type="text" name="phone" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="9876543210" maxlength="10" value="{{ old('phone') }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Password *</label>
                            <input type="password" name="password" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="Min. 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Confirm Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-theme-red w-100 py-3 fw-bold mb-3">
                        CREATE ACCOUNT
                    </button>

                    <div class="text-center font-sm text-secondary">
                        Already have an account? <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none">Log In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
