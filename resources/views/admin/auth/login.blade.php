<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AutoLogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0b0f19; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .brand-font { font-family: 'Montserrat', sans-serif; }
        .btn-swal-confirm { background-color: #d90429 !important; color: #fff !important; border-radius: 8px !important; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 bg-dark text-white shadow-lg rounded-4 p-4 p-md-5" style="background: #11141d !important; border: 1px solid #1e293b !important;">
                    <div class="text-center mb-4">
                        <div class="brand-font fw-black fs-3 text-white mb-1">AUTO<span class="text-danger">LOGOS</span></div>
                        <div class="text-secondary small fw-bold" style="letter-spacing: 2px;">ADMIN CONTROL CENTER</div>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger py-2 px-3 small">{{ session('error') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger py-2 px-3 small">
                            @foreach($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.login.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Admin Email</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary" placeholder="admin@autologos.in" value="{{ old('email', 'admin@autologos.in') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control bg-dark text-white border-secondary" placeholder="••••••••" value="Admin@123456" required>
                        </div>

                        <div class="form-check mb-4 small text-secondary">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                            <label class="form-check-label" for="remember">Keep me logged in</label>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 py-3 fw-bold text-uppercase">
                            ACCESS ADMIN PANEL
                        </button>
                    </form>

                    <div class="mt-4 pt-3 border-top border-secondary text-center text-secondary small">
                        AutoLogos Secure Administrative Gateway
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: "{{ addslashes(session('error')) }}",
                    confirmButtonColor: '#d90429'
                });
            @endif

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ addslashes(session('success')) }}",
                    confirmButtonColor: '#d90429'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: '<ul class="text-start mb-0 ps-3">@foreach($errors->all() as $err)<li>{{ addslashes($err) }}</li>@endforeach</ul>',
                    confirmButtonColor: '#d90429'
                });
            @endif
        });
    </script>
</body>
</html>
