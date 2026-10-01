@extends('layouts.app')

@section('title', 'Contact Us - AutoLogos Customer Support')

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Contact Us</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4 justify-content-center">
        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card border-0 bg-white shadow-sm rounded-4 p-4 p-md-5">
                <h3 class="fw-bold brand-font text-uppercase mb-2">GET IN <span class="text-danger">TOUCH</span></h3>
                <p class="text-muted font-sm mb-4">Need help finding a specific car logo or emblem? Send us a message and our specialists will respond within 2-4 hours.</p>

                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Your Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="Rohit Sharma" value="{{ old('name', Auth::user()?->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="yourname@gmail.com" value="{{ old('email', Auth::user()?->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Mobile Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="9876543210" maxlength="10" value="{{ old('phone', Auth::user()?->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Car Make & Model</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Hyundai Creta 2023 Front Logo" value="{{ old('subject') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-sm fw-bold">Your Message / Requirement *</label>
                            <textarea name="message" rows="4" class="form-control" placeholder="Tell us which emblem, monogram, or custom size you are looking for..." required>{{ old('message') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-theme-red px-5 py-3 fw-bold">
                                SEND MESSAGE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contact Information Card -->
        <div class="col-lg-5">
            <div class="card border-0 bg-dark text-white shadow-sm rounded-4 p-4 p-md-5 h-100 d-flex flex-column justify-content-between">
                <div>
                    <h4 class="fw-bold brand-font text-uppercase mb-4">STORE <span class="text-danger">LOCATION & INFO</span></h4>

                    <div class="d-flex gap-3 mb-4">
                        <i class="fa-solid fa-location-dot text-danger fs-4 mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">Our Registered Office:</strong>
                            <div class="text-secondary font-sm leading-relaxed">{{ $settings['general']['store_address'] ?? '123, Auto Street, New Delhi, India - 110001' }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <i class="fa-solid fa-phone text-danger fs-4 mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">Phone & WhatsApp Support:</strong>
                            <div class="text-secondary font-sm">{{ $settings['general']['store_mobile'] ?? $settings['general']['store_phone'] ?? '+91 98765 43210' }}</div>
                            <small class="text-muted">Monday - Saturday (9:30 AM to 7:00 PM)</small>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <i class="fa-solid fa-envelope text-danger fs-4 mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">Email Inquiries:</strong>
                            <div class="text-secondary font-sm">{{ $settings['general']['store_email'] ?? 'info@autologos.in' }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-certificate text-danger fs-4 mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">GSTIN Number:</strong>
                            <div class="text-secondary font-sm">{{ $settings['general']['gstin'] ?? '07AAAAA0000A1Z5' }}</div>
                        </div>
                    </div>
                </div>

                @php
                    $contactWa = preg_replace('/[^0-9]/', '', $settings['whatsapp']['whatsapp_support_number'] ?? $settings['whatsapp']['whatsapp_phone'] ?? $settings['general']['store_mobile'] ?? '919876543210');
                    $storeTitle = $settings['general']['store_name'] ?? 'AutoLogos';
                @endphp
                <div class="mt-4 pt-4 border-top border-secondary">
                    <a href="https://api.whatsapp.com/send?phone={{ $contactWa }}&text=Hi%20{{ urlencode($storeTitle) }},%20I%20need%20assistance." target="_blank" class="btn btn-success w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <i class="fa-brands fa-whatsapp fs-5"></i> CHAT WITH US ON WHATSAPP
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
