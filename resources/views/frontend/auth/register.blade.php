@extends('layouts.app')

@section('title', 'Customer Registration with Gmail Verification - SKM Saller')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border rounded-4 p-4 p-md-5 shadow-lg" style="background: #111827; border-color: #1e293b !important;">
                <div class="text-center mb-4">
                    <div class="logo-text brand-font mb-1 text-white">AUTO<span class="text-danger">LOGOS</span></div>
                    <h4 class="fw-bold text-white mb-1">Create Your Account</h4>
                    <p class="text-secondary font-sm mb-0">Sign up with instant Gmail OTP verification to place orders & track shipments.</p>
                </div>

                @if(session('warning'))
                    <div class="alert alert-warning border-0 p-3 mb-4 rounded-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-warning fs-5"></i>
                        <span class="font-sm fw-semibold text-dark">{{ session('warning') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success border-0 p-3 mb-4 rounded-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check text-success fs-5"></i>
                        <span class="font-sm fw-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 p-3 mb-4 rounded-3">
                        <ul class="mb-0 ps-3 font-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="ajaxAlert" class="alert d-none mb-4 rounded-3 font-sm"></div>

                <!-- Step Progress Indicators -->
                <div class="d-flex justify-content-center align-items-center gap-2 mb-4 font-sm">
                    <div class="d-flex align-items-center gap-1 fw-bold text-danger" id="step1Indicator">
                        <span class="badge rounded-pill bg-danger text-white">1</span>
                        <span>Account Details</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-secondary font-sm"></i>
                    <div class="d-flex align-items-center gap-1 text-secondary" id="step2Indicator">
                        <span class="badge rounded-pill bg-secondary text-white" id="step2Badge">2</span>
                        <span id="step2Label">Gmail OTP</span>
                    </div>
                </div>

                @php
                    $isOtpStep = session('otp_sent', false);
                    $otpEmail = session('otp_email', old('email'));
                @endphp

                <!-- STEP 1: Registration Form -->
                <form id="registerDetailsForm" action="{{ route('register.send-otp') }}" method="POST" style="{{ $isOtpStep ? 'display: none;' : '' }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-sm fw-bold text-light">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="reg_name" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="e.g. Rahul Sharma" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Gmail / Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="reg_email" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="yourname@gmail.com" value="{{ old('email') }}" required>
                            <div class="form-text text-secondary" style="font-size: 11px;">Verification OTP will be sent to this email.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">10-Digit Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: #161f30; border-color: #334155; color: #94a3b8;">+91</span>
                                <input type="text" name="phone" id="reg_phone" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="9876543210" maxlength="10" pattern="[0-9]{10}" value="{{ old('phone') }}" required>
                            </div>
                            <div class="form-text text-secondary" style="font-size: 11px;">Must be a unique 10-digit mobile number.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Create Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="reg_password" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="Min. 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="reg_password_confirmation" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" id="sendOtpBtn" class="btn btn-theme-red w-100 py-3 fw-bold mb-3 d-flex align-items-center justify-content-center gap-2">
                        <span>CONTINUE & GET GMAIL OTP</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>

                    <div class="text-center font-sm text-secondary">
                        Already have an account? <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none">Log In</a>
                    </div>
                </form>

                <!-- STEP 2: OTP Verification Form -->
                <div id="otpVerifyContainer" style="{{ $isOtpStep ? '' : 'display: none;' }}">
                    <div class="p-3 rounded-3 mb-4 text-center border" style="background: #161f30; border-color: #334155 !important;">
                        <i class="fa-solid fa-envelope-circle-check text-danger fs-2 mb-2"></i>
                        <h6 class="text-white fw-bold mb-1">Verify Your Gmail Address</h6>
                        <p class="text-secondary font-sm mb-1">We have sent a 6-digit OTP code to:</p>
                        <div class="badge bg-danger-subtle text-danger px-3 py-2 fw-bold font-sm border border-danger-subtle" id="displayOtpEmail">
                            {{ $otpEmail }}
                        </div>
                    </div>

                    <form id="verifyOtpForm" action="{{ route('register.verify-otp') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" id="hidden_verify_email" value="{{ $otpEmail }}">

                        <div class="mb-4">
                            <label class="form-label font-sm fw-bold text-light text-center d-block">Enter 6-Digit OTP Code</label>
                            <input type="text" name="otp" id="input_otp" class="form-control text-center fw-bold fs-3 text-white letter-spacing-lg" style="background: #161f30; border-color: #dc2626; letter-spacing: 8px;" maxlength="6" placeholder="------" pattern="[0-9]{6}" required autofocus>
                            <div class="form-text text-secondary text-center mt-2" style="font-size: 11px;">
                                Please check your inbox (or spam folder). Valid for 10 minutes.
                            </div>
                        </div>

                        <button type="submit" id="verifyOtpBtn" class="btn btn-theme-red w-100 py-3 fw-bold mb-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>VERIFY GMAIL & CREATE ACCOUNT</span>
                        </button>
                    </form>

                    <!-- Resend OTP & Edit Details Row -->
                    <div class="d-flex justify-content-between align-items-center font-sm mt-3 pt-3 border-top" style="border-color: #1e293b !important;">
                        <button type="button" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none" onclick="backToStep1()">
                            <i class="fa-solid fa-arrow-left me-1"></i> Change Details
                        </button>
                        <div>
                            <span id="resendCountdownText" class="text-muted" style="font-size: 12px;">Resend OTP in <span id="countdownTimer">30</span>s</span>
                            <button type="button" id="resendOtpBtn" class="btn btn-link btn-sm text-danger p-0 fw-bold text-decoration-none" style="display: none;" onclick="resendRegisterOtp()">
                                <i class="fa-solid fa-rotate-right me-1"></i> Resend OTP
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let resendInterval = null;

    function showAlert(type, message) {
        const alertBox = document.getElementById('ajaxAlert');
        alertBox.className = `alert alert-${type} p-3 mb-4 rounded-3 font-sm`;
        alertBox.innerHTML = message;
        alertBox.classList.remove('d-none');
    }

    function clearAlert() {
        const alertBox = document.getElementById('ajaxAlert');
        alertBox.classList.add('d-none');
    }

    function startResendTimer(seconds = 30) {
        const countdownSpan = document.getElementById('countdownTimer');
        const countdownText = document.getElementById('resendCountdownText');
        const resendBtn = document.getElementById('resendOtpBtn');

        if (resendInterval) clearInterval(resendInterval);

        countdownText.style.display = 'inline';
        resendBtn.style.display = 'none';
        countdownSpan.textContent = seconds;

        resendInterval = setInterval(() => {
            seconds--;
            countdownSpan.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(resendInterval);
                countdownText.style.display = 'none';
                resendBtn.style.display = 'inline';
            }
        }, 1000);
    }

    function showOtpStep(email) {
        document.getElementById('registerDetailsForm').style.display = 'none';
        document.getElementById('otpVerifyContainer').style.display = 'block';
        document.getElementById('displayOtpEmail').textContent = email;
        document.getElementById('hidden_verify_email').value = email;

        // Update step UI indicators
        document.getElementById('step1Indicator').className = 'd-flex align-items-center gap-1 text-secondary';
        document.getElementById('step1Indicator').querySelector('.badge').className = 'badge rounded-pill bg-secondary text-white';
        document.getElementById('step2Indicator').className = 'd-flex align-items-center gap-1 fw-bold text-danger';
        document.getElementById('step2Badge').className = 'badge rounded-pill bg-danger text-white';

        startResendTimer(30);
        setTimeout(() => {
            const inputOtp = document.getElementById('input_otp');
            if (inputOtp) inputOtp.focus();
        }, 300);
    }

    function backToStep1() {
        document.getElementById('registerDetailsForm').style.display = 'block';
        document.getElementById('otpVerifyContainer').style.display = 'none';

        document.getElementById('step1Indicator').className = 'd-flex align-items-center gap-1 fw-bold text-danger';
        document.getElementById('step1Indicator').querySelector('.badge').className = 'badge rounded-pill bg-danger text-white';
        document.getElementById('step2Indicator').className = 'd-flex align-items-center gap-1 text-secondary';
        document.getElementById('step2Badge').className = 'badge rounded-pill bg-secondary text-white';
        clearAlert();
    }

    // Step 1: Submit Details & Send OTP via AJAX
    document.getElementById('registerDetailsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearAlert();

        const name = document.getElementById('reg_name').value.trim();
        const email = document.getElementById('reg_email').value.trim();
        const phone = document.getElementById('reg_phone').value.trim();
        const password = document.getElementById('reg_password').value;
        const password_confirmation = document.getElementById('reg_password_confirmation').value;
        const btn = document.getElementById('sendOtpBtn');

        if (!name || !email || !password || !password_confirmation) {
            showAlert('danger', 'Please fill in all required fields.');
            return;
        }

        if (password !== password_confirmation) {
            showAlert('danger', 'Passwords do not match. Please verify your password.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Sending OTP to Gmail...';

        fetch("{{ route('register.send-otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                email: email,
                phone: phone,
                password: password,
                password_confirmation: password_confirmation
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            btn.disabled = false;
            btn.innerHTML = '<span>CONTINUE & GET GMAIL OTP</span> <i class="fa-solid fa-arrow-right"></i>';

            if (status === 200 && body.success) {
                showAlert('success', `<i class="fa-solid fa-check-circle me-1"></i> ${body.message}`);
                showOtpStep(body.email);
            } else {
                let errorMsg = body.message || 'An error occurred. Please check your inputs.';
                if (body.errors) {
                    errorMsg = Object.values(body.errors).flat().join('<br>');
                }
                showAlert('danger', errorMsg);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span>CONTINUE & GET GMAIL OTP</span> <i class="fa-solid fa-arrow-right"></i>';
            console.error('Error sending OTP:', err);
            showAlert('danger', 'Network error. Please try again.');
        });
    });

    // Step 2: Verify OTP via AJAX
    document.getElementById('verifyOtpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearAlert();

        const email = document.getElementById('hidden_verify_email').value.trim();
        const otp = document.getElementById('input_otp').value.trim();
        const btn = document.getElementById('verifyOtpBtn');

        if (!otp || otp.length !== 6) {
            showAlert('danger', 'Please enter a valid 6-digit OTP code.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Verifying Code...';

        fetch("{{ route('register.verify-otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                otp: otp
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 && body.success) {
                showAlert('success', `<i class="fa-solid fa-check-circle me-1"></i> ${body.message}`);
                btn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Verified! Redirecting...';
                window.location.href = body.redirect || "{{ route('account.dashboard') }}";
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-shield-halved"></i> <span>VERIFY GMAIL & CREATE ACCOUNT</span>';
                let errorMsg = body.message || 'Invalid OTP code. Please try again.';
                showAlert('danger', errorMsg);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-shield-halved"></i> <span>VERIFY GMAIL & CREATE ACCOUNT</span>';
            console.error('Error verifying OTP:', err);
            showAlert('danger', 'Network error. Please try again.');
        });
    });

    // Resend OTP function
    function resendRegisterOtp() {
        const email = document.getElementById('hidden_verify_email').value.trim();
        const resendBtn = document.getElementById('resendOtpBtn');
        clearAlert();

        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Sending...';

        fetch("{{ route('register.resend-otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Resend OTP';

            if (status === 200 && body.success) {
                showAlert('success', `<i class="fa-solid fa-check-circle me-1"></i> ${body.message}`);
                startResendTimer(30);
            } else {
                showAlert('danger', body.message || 'Failed to resend OTP. Please try again.');
            }
        })
        .catch(err => {
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Resend OTP';
            showAlert('danger', 'Network error. Please try again.');
        });
    }

    @if($isOtpStep)
        startResendTimer(30);
    @endif
</script>
@endpush
