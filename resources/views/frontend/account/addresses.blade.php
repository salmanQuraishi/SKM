@extends('layouts.app')

@section('title', 'Saved Addresses - AutoLogos')

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.dashboard') }}" class="text-decoration-none text-muted">My Account</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Saved Addresses</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Account Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 bg-white shadow-sm rounded-3 p-3">
                <div class="list-group list-group-flush border-0 font-sm">
                    <a href="{{ route('account.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-1">
                        <i class="fa-solid fa-gauge-high me-2 text-danger"></i> Dashboard
                    </a>
                    <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-1">
                        <i class="fa-solid fa-box-open me-2 text-danger"></i> My Orders
                    </a>
                    <a href="{{ route('account.addresses') }}" class="list-group-item list-group-item-action active bg-danger border-0 rounded-2 mb-1">
                        <i class="fa-solid fa-address-book me-2"></i> Saved Addresses
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-1">
                        <i class="fa-regular fa-heart me-2 text-danger"></i> My Wishlist
                    </a>
                </div>
            </div>
        </div>

        <!-- Addresses Main Area -->
        <div class="col-lg-9">
            <div class="card border-0 bg-white shadow-sm rounded-3 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold brand-font text-uppercase mb-0">SAVED <span class="text-danger">ADDRESSES</span></h4>
                    <button class="btn btn-theme-red btn-sm" data-bs-toggle="collapse" data-bs-target="#newAddressForm">
                        <i class="fa-solid fa-plus me-1"></i> Add New Address
                    </button>
                </div>

                <!-- Add New Address Collapse Form -->
                <div class="collapse mb-4" id="newAddressForm">
                    <div class="p-4 bg-light rounded-3 border">
                        <h6 class="fw-bold mb-3">Add New Shipping Address</h6>
                        <form action="{{ route('account.address.store') }}" method="POST">
                            @csrf
                            <div class="row g-3 font-sm">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Full Name *</label>
                                    <input type="text" name="name" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Mobile Number *</label>
                                    <input type="text" name="mobile" class="form-control form-control-sm" maxlength="10" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Address Line 1 (Flat, House No., Building) *</label>
                                    <input type="text" name="address_line_1" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Address Line 2 (Street, Area, Sector)</label>
                                    <input type="text" name="address_line_2" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Landmark</label>
                                    <input type="text" name="landmark" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">PIN Code *</label>
                                    <input type="text" name="pincode" class="form-control form-control-sm" maxlength="6" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">City *</label>
                                    <input type="text" name="city" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">State *</label>
                                    <input type="text" name="state" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Address Type *</label>
                                    <select name="address_type" class="form-select form-select-sm" required>
                                        <option value="Home">Home</option>
                                        <option value="Office">Office</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1">
                                        <label class="form-check-label" for="is_default">Set as default address</label>
                                    </div>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-theme-red btn-sm px-4">Save Address</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Existing Addresses Grid -->
                @if($addresses->isEmpty())
                    <p class="text-muted small">No addresses saved yet. Add your first address using the button above.</p>
                @else
                    <div class="row g-3">
                        @foreach($addresses as $addr)
                            <div class="col-md-6">
                                <div class="card h-100 border p-3 rounded-3 shadow-sm font-sm position-relative {{ $addr->is_default ? 'border-danger' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge {{ $addr->is_default ? 'bg-danger' : 'bg-secondary' }}">{{ $addr->address_type }}</span>
                                        @if($addr->is_default)
                                            <span class="badge bg-success-subtle text-success">DEFAULT</span>
                                        @endif
                                    </div>
                                    <strong class="text-dark">{{ $addr->name }}</strong>
                                    <div class="text-muted leading-relaxed my-2">{{ $addr->formatted_address }}</div>
                                    <div class="text-muted small mb-3"><i class="fa-solid fa-phone text-danger me-1"></i> {{ $addr->mobile }}</div>

                                    <div class="mt-auto pt-2 border-top text-end">
                                        <form action="{{ route('account.address.delete', $addr->id) }}" method="POST" class="confirm-action" data-confirm="Are you sure you want to delete this address?" data-confirm-title="Delete Address?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger p-0 small text-decoration-none">
                                                <i class="fa-solid fa-trash-can me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
