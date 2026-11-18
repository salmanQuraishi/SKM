@extends('layouts.app')

@section('title', 'Customer Login - SKM Saller')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border rounded-4 p-4 p-md-5 shadow-lg" style="background: #111827; border-color: #1e293b !important;">
                <div class="text-center mb-4">
                    <div class="logo-text brand-font mb-1 text-white">AUTO<span class="text-danger">LOGOS</span></div>
                    <h4 class="fw-bold text-white mb-1">Welcome Back</h4>
                    <p class="text-secondary font-sm mb-0">Sign in to complete your checkout, manage orders & addresses.</p>
                </div>

                @if(session('warning'))
                    <div class="alert alert-warning border-0 p-3 mb-4 rounded-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-lock text-warning fs-5"></i>
                        <span class="font-sm fw-semibold text-dark">{{ session('warning') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success border-0 p-3 mb-4 rounded-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check text-success fs-5"></i>
                        <span class="font-sm fw-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 p-3 mb-4 rounded-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-danger fs-5"></i>
                        <span class="font-sm fw-semibold">{{ session('error') }}</span>
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

                <div id="loginAjaxAlert" class="alert d-none mb-4 rounded-3 font-sm"></div>

                @php
                    $isOtpMode = session('login_mode') === 'otp' || old('login_mode') === 'otp';
                @endphp

                <!-- Login Method Toggle Tabs -->
                <ul class="nav nav-pills nav-fill mb-4 p-1 rounded-3 border" style="background: #161f30; border-color: #334155 !important;" id="loginTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ !$isOtpMode ? 'active bg-danger text-white' : 'text-secondary' }} font-sm fw-bold py-2 rounded-2" id="pwdTabBtn" data-bs-toggle="pill" data-bs-target="#passwordLoginTab" type="button" role="tab" onclick="switchLoginTab('password')">
                            <i class="fa-solid fa-key me-1"></i> Password
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $isOtpMode ? 'active bg-danger text-white' : 'text-secondary' }} font-sm fw-bold py-2 rounded-2" id="otpTabBtn" data-bs-toggle="pill" data-bs-target="#otpLoginTab" type="button" role="tab" onclick="switchLoginTab('otp')">
                            <i class="fa-solid fa-envelope me-1"></i> Gmail OTP
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="loginTabsContent">
                    <!-- TAB 1: Password Login -->
                    <div class="tab-pane fade {{ !$isOtpMode ? 'show active' : '' }}" id="passwordLoginTab" role="tabpanel">
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
                                <a href="javascript:void(0)" onclick="switchLoginTab('otp')" class="text-danger text-decoration-none">Forgot / Use OTP?</a>
                            </div>

                            <button type="submit" class="btn btn-theme-red w-100 py-3 fw-bold mb-3 d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <span>LOG IN TO ACCOUNT</span>
                            </button>
                        </form>
                    </div>

                    <!-- TAB 2: Gmail OTP Login -->
                    <div class="tab-pane fade {{ $isOtpMode ? 'show active' : '' }}" id="otpLoginTab" role="tabpanel">
                        <!-- Step 1: Send OTP -->
                        <div id="loginOtpStep1" style="{{ session('otp_sent') ? 'display: none;' : '' }}">
                            <form id="sendLoginOtpForm" action="{{ route('login.send-otp') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label font-sm fw-bold text-light">Registered Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background: #161f30; border-color: #334155;"><i class="fa-solid fa-envelope text-secondary"></i></span>
                                        <input type="email" name="email" id="login_otp_email" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="yourname@gmail.com" value="{{ old('email', session('otp_email')) }}" required>
                                    </div>
                                    <div class="form-text text-secondary" style="font-size: 11px;">We will send a 6-digit login code to your Gmail.</div>
                                </div>

                                <button type="submit" id="sendLoginOtpBtn" class="btn btn-theme-red w-100 py-3 fw-bold mb-3 d-flex align-items-center justify-content-center gap-2">
                                    <span>SEND GMAIL LOGIN OTP</span>
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Step 2: Verify OTP -->
                        <div id="loginOtpStep2" style="{{ session('otp_sent') ? '' : 'display: none;' }}">
                            <div class="p-3 rounded-3 mb-3 text-center border" style="background: #161f30; border-color: #334155 !important;">
                                <i class="fa-solid fa-envelope-open-text text-danger fs-3 mb-1"></i>
                                <div class="text-secondary font-sm">OTP sent to:</div>
                                <strong class="text-white font-sm" id="displayLoginEmail">{{ session('otp_email') }}</strong>
                            </div>

                            <form id="verifyLoginOtpForm" action="{{ route('login.verify-otp') }}" method="POST">
                                @csrf
                                <input type="hidden" name="email" id="hidden_login_email" value="{{ session('otp_email') }}">

                                <div class="mb-3">
                                    <label class="form-label font-sm fw-bold text-light text-center d-block">Enter 6-Digit OTP</label>
                                    <input type="text" name="otp" id="login_otp_input" class="form-control text-center fw-bold fs-3 text-white" style="background: #161f30; border-color: #dc2626; letter-spacing: 8px;" maxlength="6" placeholder="------" pattern="[0-9]{6}" required>
                                </div>

                                <button type="submit" id="verifyLoginOtpBtn" class="btn btn-theme-red w-100 py-3 fw-bold mb-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fa-solid fa-lock-open"></i>
                                    <span>VERIFY & SIGN IN</span>
                                </button>
                            </form>

                            <div class="d-flex justify-content-between align-items-center font-sm">
                                <button type="button" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none" onclick="backToLoginStep1()">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Change Email
                                </button>
                                <button type="button" id="loginResendBtn" class="btn btn-link btn-sm text-danger p-0 fw-bold text-decoration-none" onclick="resendLoginOtp()">
                                    <i class="fa-solid fa-rotate-right me-1"></i> Resend Code
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center font-sm text-secondary mt-3 pt-3 border-top" style="border-color: #1e293b !important;">
                    Don't have an account? <a href="{{ route('register') }}" class="text-danger fw-bold text-decoration-none">Create Account (Gmail OTP)</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showLoginAlert(type, message) {
        const box = document.getElementById('loginAjaxAlert');
        box.className = `alert alert-${type} p-3 mb-4 rounded-3 font-sm`;
        box.innerHTML = message;
        box.classList.remove('d-none');
    }

    function clearLoginAlert() {
        const box = document.getElementById('loginAjaxAlert');
        box.classList.add('d-none');
    }

    function switchLoginTab(mode) {
        clearLoginAlert();
        const pwdBtn = document.getElementById('pwdTabBtn');
        const otpBtn = document.getElementById('otpTabBtn');
        const pwdTab = document.getElementById('passwordLoginTab');
        const otpTab = document.getElementById('otpLoginTab');

        if (mode === 'otp') {
            pwdBtn.className = 'nav-link text-secondary font-sm fw-bold py-2 rounded-2';
            otpBtn.className = 'nav-link active bg-danger text-white font-sm fw-bold py-2 rounded-2';
            pwdTab.className = 'tab-pane fade';
            otpTab.className = 'tab-pane fade show active';
        } else {
            pwdBtn.className = 'nav-link active bg-danger text-white font-sm fw-bold py-2 rounded-2';
            otpBtn.className = 'nav-link text-secondary font-sm fw-bold py-2 rounded-2';
            pwdTab.className = 'tab-pane fade show active';
            otpTab.className = 'tab-pane fade';
        }
    }

    function backToLoginStep1() {
        document.getElementById('loginOtpStep1').style.display = 'block';
        document.getElementById('loginOtpStep2').style.display = 'none';
        clearLoginAlert();
    }

    // Send Login OTP via AJAX
    document.getElementById('sendLoginOtpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearLoginAlert();

        const email = document.getElementById('login_otp_email').value.trim();
        const btn = document.getElementById('sendLoginOtpBtn');

        if (!email) {
            showLoginAlert('danger', 'Please enter your email address.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Sending OTP...';

        fetch("{{ route('login.send-otp') }}", {
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
            btn.disabled = false;
            btn.innerHTML = '<span>SEND GMAIL LOGIN OTP</span> <i class="fa-solid fa-paper-plane"></i>';

            if (status === 200 && body.success) {
                showLoginAlert('success', `<i class="fa-solid fa-check-circle me-1"></i> ${body.message}`);
                document.getElementById('displayLoginEmail').textContent = body.email;
                document.getElementById('hidden_login_email').value = body.email;
                document.getElementById('loginOtpStep1').style.display = 'none';
                document.getElementById('loginOtpStep2').style.display = 'block';
                setTimeout(() => {
                    const inp = document.getElementById('login_otp_input');
                    if (inp) inp.focus();
                }, 300);
            } else {
                showLoginAlert('danger', body.message || 'Error sending OTP. Please try again.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<span>SEND GMAIL LOGIN OTP</span> <i class="fa-solid fa-paper-plane"></i>';
            console.error('Login OTP error:', err);
            showLoginAlert('danger', 'Network error. Please try again.');
        });
    });

    // Verify Login OTP via AJAX
    document.getElementById('verifyLoginOtpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearLoginAlert();

        const email = document.getElementById('hidden_login_email').value.trim();
        const otp = document.getElementById('login_otp_input').value.trim();
        const btn = document.getElementById('verifyLoginOtpBtn');

        if (!otp || otp.length !== 6) {
            showLoginAlert('danger', 'Please enter the 6-digit OTP code.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Signing In...';

        fetch("{{ route('login.verify-otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email, otp: otp })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 && body.success) {
                showLoginAlert('success', `<i class="fa-solid fa-check-circle me-1"></i> ${body.message}`);
                btn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Verified! Redirecting...';
                window.location.href = body.redirect || "{{ route('account.dashboard') }}";
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-lock-open"></i> <span>VERIFY & SIGN IN</span>';
                showLoginAlert('danger', body.message || 'Invalid OTP code.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-lock-open"></i> <span>VERIFY & SIGN IN</span>';
            console.error('Verify login OTP error:', err);
            showLoginAlert('danger', 'Network error. Please try again.');
        });
    });

    function resendLoginOtp() {
        const email = document.getElementById('hidden_login_email').value.trim();
        const btn = document.getElementById('loginResendBtn');
        clearLoginAlert();

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Sending...';

        fetch("{{ route('login.send-otp') }}", {
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
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Resend Code';

            if (status === 200 && body.success) {
                showLoginAlert('success', `<i class="fa-solid fa-check-circle me-1"></i> Fresh OTP sent to ${email}`);
            } else {
                showLoginAlert('danger', body.message || 'Failed to resend code.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-rotate-right me-1"></i> Resend Code';
            showLoginAlert('danger', 'Network error. Please try again.');
        });
    }
</script>
@endpush
