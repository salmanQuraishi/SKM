@extends('layouts.app')

@section('title', 'My Account - AutoLogos')

@section('content')
<div class="py-3 border-bottom mb-4" style="background: #090d16; border-color: #1e293b !important;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active text-light fw-semibold" aria-current="page">My Account</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Account Sidebar -->
        <div class="col-lg-3">
            <div class="card border rounded-3 p-3 shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <div class="text-center py-3 border-bottom mb-3" style="border-color: #1e293b !important;">
                    <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center fw-bold fs-3 mb-2" style="width: 70px; height: 70px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-0 text-white">{{ $user->name }}</h5>
                    <span class="text-secondary small">{{ $user->email }}</span>
                </div>

                <div class="list-group list-group-flush border-0 font-sm">
                    <a href="{{ route('account.dashboard') }}" class="list-group-item list-group-item-action active bg-danger border-0 rounded-2 mb-1 text-white">
                        <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-1" style="background: transparent; color: #cbd5e1;">
                        <i class="fa-solid fa-box-open me-2 text-danger"></i> My Orders
                    </a>
                    <a href="{{ route('account.addresses') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-1" style="background: transparent; color: #cbd5e1;">
                        <i class="fa-solid fa-address-book me-2 text-danger"></i> Saved Addresses
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-1" style="background: transparent; color: #cbd5e1;">
                        <i class="fa-regular fa-heart me-2 text-danger"></i> My Wishlist
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="list-group-item list-group-item-action border-0 rounded-2 text-danger" style="background: transparent;">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Main Area -->
        <div class="col-lg-9">
            <!-- Welcome Banner -->
            <div class="card border rounded-3 p-4 mb-4 shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Hello, {{ $user->name }}!</h4>
                        <p class="text-secondary small mb-0">From your account dashboard, you can view recent orders, manage shipping addresses, and download GST tax invoices.</p>
                    </div>
                    <span class="badge bg-danger fs-6 px-3 py-2">Customer Account</span>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-md-4">
                    <div class="card border rounded-3 p-3 text-center shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                        <i class="fa-solid fa-box-archive text-danger fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0 text-white">{{ $totalOrdersCount }}</h3>
                        <span class="text-secondary small">Total Orders</span>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="card border rounded-3 p-3 text-center shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                        <i class="fa-regular fa-heart text-danger fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0 text-white">{{ $globalWishlistCount }}</h3>
                        <span class="text-secondary small">Wishlist Items</span>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="card border rounded-3 p-3 text-center shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                        <i class="fa-solid fa-location-dot text-danger fs-2 mb-2"></i>
                        <h3 class="fw-bold mb-0 text-white">{{ $user->addresses()->count() }}</h3>
                        <span class="text-secondary small">Saved Addresses</span>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="card border rounded-3 p-4 mb-4 shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-white">Recent Orders</h5>
                    <a href="{{ route('account.orders') }}" class="text-danger small fw-semibold text-decoration-none">View All Orders →</a>
                </div>

                @if($recentOrders->isEmpty())
                    <p class="text-secondary small mb-0">You have not placed any orders yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 font-sm" style="border-color: #1e293b;">
                            <thead>
                                <tr style="background: #161f30; color: #f8fafc; border-color: #1e293b;">
                                    <th class="py-2 px-3 border-0">Order #</th>
                                    <th class="py-2 px-3 border-0">Date</th>
                                    <th class="py-2 px-3 border-0">Amount</th>
                                    <th class="py-2 px-3 border-0">Status</th>
                                    <th class="py-2 px-3 border-0 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $ord)
                                    <tr style="border-color: #1e293b;">
                                        <td class="px-3"><a href="{{ route('account.order.detail', $ord->order_number) }}" class="fw-bold text-danger text-decoration-none">{{ $ord->order_number }}</a></td>
                                        <td class="px-3 text-secondary">{{ $ord->created_at->format('d M, Y') }}</td>
                                        <td class="px-3 fw-bold text-white">&#x20B9;{{ number_format($ord->total_amount, 2) }}</td>
                                        <td class="px-3"><span class="badge {{ $ord->status_badge_class }}">{{ ucfirst($ord->order_status) }}</span></td>
                                        <td class="px-3 text-end">
                                            <a href="{{ route('account.order.detail', $ord->order_number) }}" class="btn btn-outline-light btn-sm">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Profile Edit Form -->
            <div class="card border rounded-3 p-4 shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-white" style="border-color: #1e293b !important;">Profile & Security Settings</h5>
                <form action="{{ route('account.profile.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Full Name *</label>
                            <input type="text" name="name" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Mobile Number</label>
                            <input type="text" name="phone" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" value="{{ $user->phone }}" placeholder="10-digit mobile">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">Current Password</label>
                            <input type="password" name="current_password" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="Leave blank if not changing">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-light">New Password</label>
                            <input type="password" name="new_password" class="form-control" style="background: #161f30; border-color: #334155; color: #f8fafc;" placeholder="New password">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-theme-red px-4">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
