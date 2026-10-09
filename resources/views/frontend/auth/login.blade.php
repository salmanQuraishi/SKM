@extends('layouts.app')

@section('title', 'Customer Login - AutoLogos')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border rounded-4 p-4 p-md-5 shadow-lg" style="background: #111827; border-color: #1e293b !important;">
                <div class="text-center mb-4">
                    <div class="logo-text brand-font mb-1 text-white">AUTO<span class="text-danger">LOGOS</span></div>
                    <h4 class="fw-bold text-white">Welcome Back</h4>
                    <p class="text-secondary font-sm">Log in to view your orders, addresses, and wishlist.</p>
                </div>

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-sm fw-bold text-light">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #161f30; border-color: #334155;"><i class="fa-solid fa-envelope text-secondary"></i></span>
                            <input type="email" name="email" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="yourname@gmail.com" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-sm fw-bold text-light">Password</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background: #161f30; border-color: #334155;"><i class="fa-solid fa-lock text-secondary"></i></span>
                            <input type="password" name="password" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 font-sm">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-secondary" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-theme-red w-100 py-3 fw-bold mb-3">
                        LOG IN TO ACCOUNT
                    </button>

                    <div class="text-center font-sm text-secondary">
                        Don't have an account? <a href="{{ route('register') }}" class="text-danger fw-bold text-decoration-none">Create Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
