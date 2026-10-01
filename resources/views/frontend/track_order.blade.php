@extends('layouts.app')

@section('title', 'Track Order Status - AutoLogos')

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Track Order</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Search Order Card -->
            <div class="card border-0 bg-white shadow-sm rounded-4 p-4 p-md-5 mb-4">
                <div class="text-center mb-4">
                    <h2 class="fw-bold brand-font text-uppercase mb-2">TRACK YOUR <span class="text-danger">ORDER</span></h2>
                    <p class="text-muted font-sm">Enter your Order Number and Registered Mobile / Email to check live shipment status.</p>
                </div>

                <form action="{{ route('order.track.view') }}" method="GET">
                    <div class="row g-3 justify-content-center">
                        <div class="col-md-5">
                            <label class="form-label font-sm fw-bold">Order Number *</label>
                            <input type="text" name="order_number" class="form-control text-uppercase" placeholder="e.g. AL-202609-1001" value="{{ request('order_number') }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label font-sm fw-bold">Mobile Number or Email *</label>
                            <input type="text" name="phone" class="form-control" placeholder="10-digit mobile or email address" value="{{ request('phone') }}" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-theme-red w-100 py-2 fw-bold">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> TRACK
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Results Section -->
            @if($order)
                @php
                    $steps = [
                        'pending' => ['label' => 'Order Placed', 'icon' => 'fa-clipboard-check', 'step' => 1],
                        'confirmed' => ['label' => 'Confirmed', 'icon' => 'fa-check-double', 'step' => 2],
                        'processing' => ['label' => 'Processing', 'icon' => 'fa-gear', 'step' => 3],
                        'packed' => ['label' => 'Packed', 'icon' => 'fa-box', 'step' => 4],
                        'shipped' => ['label' => 'Shipped', 'icon' => 'fa-truck-fast', 'step' => 5],
                        'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => 'fa-motorcycle', 'step' => 6],
                        'delivered' => ['label' => 'Delivered', 'icon' => 'fa-house-chimney-check', 'step' => 7],
                    ];

                    $statusStepMap = [
                        'pending' => 1,
                        'confirmed' => 2,
                        'processing' => 3,
                        'packed' => 4,
                        'shipped' => 5,
                        'out_for_delivery' => 6,
                        'delivered' => 7,
                        'cancelled' => -1,
                    ];

                    $currentStepNum = $statusStepMap[$order->order_status] ?? 1;
                @endphp

                <div class="card border-0 bg-white shadow-sm rounded-4 p-4 p-md-5 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div>
                            <span class="text-muted font-sm d-block">Tracking Details For:</span>
                            <h4 class="fw-bold text-dark mb-0">{{ $order->order_number }}</h4>
                        </div>
                        <div class="mt-2 mt-sm-0 text-sm-end">
                            <span class="badge {{ $order->status_badge_class }} fs-6 text-uppercase px-3 py-2">
                                Status: {{ str_replace('_', ' ', $order->order_status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Visual Progress Timeline -->
                    @if($order->order_status === 'cancelled')
                        <div class="alert alert-danger text-center p-4">
                            <i class="fa-solid fa-ban display-5 mb-2"></i>
                            <h5 class="fw-bold">Order Cancelled</h5>
                            <p class="mb-0">This order has been cancelled. If you have questions regarding a refund, please contact customer support.</p>
                        </div>
                    @else
                        <div class="py-4 position-relative">
                            <div class="progress position-absolute top-50 start-0 w-100 translate-middle-y" style="height: 4px; z-index: 1;">
                                @php
                                    $percent = min(100, max(0, (($currentStepNum - 1) / 6) * 100));
                                @endphp
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $percent }}%;"></div>
                            </div>

                            <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                                @foreach($steps as $key => $s)
                                    @php $isCompleted = $currentStepNum >= $s['step']; @endphp
                                    <div class="text-center">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $isCompleted ? 'bg-danger text-white shadow' : 'bg-light border text-muted' }}" style="width: 44px; height: 44px;">
                                            <i class="fa-solid {{ $s['icon'] }}"></i>
                                        </div>
                                        <div class="font-sm fw-bold mt-2 d-none d-md-block {{ $isCompleted ? 'text-dark' : 'text-muted' }}">{{ $s['label'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Courier / Tracking Details -->
                    @if($order->courier_name || $order->tracking_number)
                        <div class="alert alert-light border p-3 rounded-3 mt-4">
                            <div class="row g-2 align-items-center font-sm">
                                <div class="col-md-4">
                                    <span class="text-muted">Courier Partner:</span>
                                    <strong class="d-block text-dark">{{ $order->courier_name ?: 'Express Surface' }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted">Tracking Number:</span>
                                    <strong class="d-block text-danger">{{ $order->tracking_number ?: 'Pending Assignment' }}</strong>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="text-muted">Expected Delivery:</span>
                                    <strong class="d-block text-success">{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('d M, Y') : '3-5 Business Days' }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Status Logs History -->
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2 font-sm text-uppercase">Tracking Updates Timeline</h6>
                        <div class="timeline d-flex flex-column gap-3">
                            @foreach($order->statusHistory as $hist)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle bg-danger-subtle text-danger p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-check" style="font-size: 12px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-dark font-sm text-uppercase">{{ str_replace('_', ' ', $hist->status) }}</strong>
                                            <span class="text-muted small">{{ $hist->created_at->format('d M, Y h:i A') }}</span>
                                        </div>
                                        <div class="text-secondary small">{{ $hist->comment }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Items Summary -->
                    <div class="mt-4 border-top pt-4">
                        <h6 class="fw-bold mb-3 font-sm text-uppercase">Items in this Shipment</h6>
                        <div class="row g-2">
                            @foreach($order->items as $item)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3 p-2 border rounded-2 bg-light">
                                        <div style="width: 45px; height: 45px;">
                                            <img src="{{ $item->product ? $item->product->primary_image_url : asset('images/placeholder-emblem.svg') }}" class="w-100 h-100 object-fit-contain" alt="">
                                        </div>
                                        <div>
                                            <div class="fw-bold font-sm text-truncate" style="max-width: 250px;">{{ $item->product_name }}</div>
                                            <div class="text-muted small">Qty: {{ $item->quantity }} | Price: &#x20B9;{{ number_format($item->price, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
