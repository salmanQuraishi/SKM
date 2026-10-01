@extends('layouts.app')

@section('title', 'My Orders - AutoLogos')

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.dashboard') }}" class="text-decoration-none text-muted">My Account</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">My Orders</li>
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
                    <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action active bg-danger border-0 rounded-2 mb-1">
                        <i class="fa-solid fa-box-open me-2"></i> My Orders
                    </a>
                    <a href="{{ route('account.addresses') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-1">
                        <i class="fa-solid fa-address-book me-2 text-danger"></i> Saved Addresses
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action border-0 rounded-2 mb-1">
                        <i class="fa-regular fa-heart me-2 text-danger"></i> My Wishlist
                    </a>
                </div>
            </div>
        </div>

        <!-- Orders List Area -->
        <div class="col-lg-9">
            <div class="card border-0 bg-white shadow-sm rounded-3 p-4">
                <h4 class="fw-bold brand-font text-uppercase mb-4">MY <span class="text-danger">ORDERS</span></h4>

                @if($orders->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa-solid fa-box-open text-muted display-4 mb-3"></i>
                        <h5>No Orders Placed Yet</h5>
                        <p class="text-muted small">When you purchase emblems or monograms, your order history will appear here.</p>
                        <a href="{{ route('shop') }}" class="btn btn-theme-red px-4 py-2 mt-2">Start Shopping</a>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($orders as $ord)
                            <div class="card border rounded-3 p-3 shadow-sm">
                                <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-2 mb-3">
                                    <div>
                                        <span class="text-muted small">ORDER NUMBER:</span>
                                        <strong class="text-danger font-sm ms-1">{{ $ord->order_number }}</strong>
                                        <span class="text-muted small ms-3">Placed on: {{ $ord->created_at->format('d M, Y') }}</span>
                                    </div>
                                    <div class="mt-2 mt-sm-0">
                                        <span class="badge {{ $ord->status_badge_class }} text-uppercase px-3 py-2">{{ ucfirst($ord->order_status) }}</span>
                                    </div>
                                </div>

                                <div class="row g-3 align-items-center">
                                    <div class="col-md-7">
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($ord->items as $item)
                                                <div class="d-flex align-items-center gap-2">
                                                    <div style="width: 40px; height: 40px;" class="border rounded p-1 bg-light">
                                                        <img src="{{ $item->product ? $item->product->primary_image_url : asset('images/placeholder-emblem.svg') }}" class="w-100 h-100 object-fit-contain" alt="">
                                                    </div>
                                                    <div class="font-sm">
                                                        <span class="fw-bold text-dark">{{ $item->product_name }}</span>
                                                        <span class="text-muted small"> × {{ $item->quantity }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-md-5 text-md-end border-start-md">
                                        <div class="fw-bold fs-5 text-dark mb-2">&#x20B9;{{ number_format($ord->total_amount, 2) }}</div>
                                        <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                            <a href="{{ route('account.order.detail', $ord->order_number) }}" class="btn btn-danger btn-sm">
                                                Order Details
                                            </a>
                                            <a href="{{ route('account.order.invoice', $ord->order_number) }}" class="btn btn-outline-secondary btn-sm" title="Download Invoice">
                                                <i class="fa-solid fa-file-pdf text-danger"></i> Invoice
                                            </a>
                                            <a href="{{ route('order.track.view', ['order_number' => $ord->order_number, 'phone' => $ord->customer_phone]) }}" class="btn btn-outline-dark btn-sm" title="Track">
                                                <i class="fa-solid fa-location-crosshairs"></i> Track
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
