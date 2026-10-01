@extends('layouts.app')

@section('title', "Order #{$order->order_number} - AutoLogos")

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.orders') }}" class="text-decoration-none text-muted">My Orders</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold brand-font text-uppercase mb-1">ORDER <span class="text-danger">#{{ $order->order_number }}</span></h3>
            <span class="text-muted font-sm">Placed on {{ $order->created_at->format('d F Y, h:i A') }}</span>
        </div>
        <div class="d-flex gap-2 mt-3 mt-sm-0 flex-wrap">
            <a href="{{ route('account.order.invoice', $order->order_number) }}" class="btn btn-outline-danger btn-sm">
                <i class="fa-solid fa-file-pdf me-1"></i> Download Tax Invoice
            </a>
            @if($order->payment_status === 'paid')
                <a href="{{ route('account.order.receipt', $order->order_number) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-receipt me-1"></i> Download Receipt
                </a>
            @endif
            <a href="{{ route('order.track.view', ['order_number' => $order->order_number, 'phone' => $order->customer_phone]) }}" class="btn btn-dark btn-sm">
                <i class="fa-solid fa-location-crosshairs me-1"></i> Live Tracking
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Items & Status Timeline -->
        <div class="col-lg-8">
            <!-- Items Card -->
            <div class="card border-0 bg-white shadow-sm rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Order Items ({{ $order->items->count() }})</h5>

                <div class="table-responsive">
                    <table class="table align-middle font-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width: 50px; height: 50px;" class="border rounded p-1 bg-white shadow-sm d-flex align-items-center justify-content-center">
                                                <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-contain" alt="{{ $item->product_name }}" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                                <span class="text-muted small">SKU: {{ $item->product_sku }}</span>
                                                @if($item->variant_name)
                                                    <span class="badge bg-secondary-subtle text-secondary small ms-1">{{ $item->variant_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>&#x20B9;{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">&#x20B9;{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card border-0 bg-white shadow-sm rounded-3 p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Order Status History</h5>
                <div class="d-flex flex-column gap-3">
                    @foreach($order->statusHistory as $hist)
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;">
                                <i class="fa-solid fa-check" style="font-size: 11px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-uppercase font-sm">{{ str_replace('_', ' ', $hist->status) }}</strong>
                                    <span class="text-muted small">{{ $hist->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="text-muted small">{{ $hist->comment }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Summary & Address Sidebar -->
        <div class="col-lg-4">
            <!-- Payment & Order Status -->
            <div class="card border-0 bg-white shadow-sm rounded-3 p-4 mb-4 font-sm">
                <h6 class="fw-bold border-bottom pb-2 mb-3">ORDER STATUS</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Order Status:</span>
                    <span class="badge {{ $order->status_badge_class }} text-uppercase">{{ $order->order_status }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Method:</span>
                    <strong class="text-uppercase">{{ $order->payment_method }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Status:</span>
                    <span class="badge {{ $order->payment_badge_class }} text-uppercase">{{ $order->payment_status }}</span>
                </div>
                @if($order->courier_name)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Courier:</span>
                        <strong>{{ $order->courier_name }}</strong>
                    </div>
                @endif
                @if($order->tracking_number)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tracking No:</span>
                        <strong class="text-danger">{{ $order->tracking_number }}</strong>
                    </div>
                @endif
            </div>

            <!-- Price Breakdown -->
            <div class="card border-0 bg-white shadow-sm rounded-3 p-4 mb-4 font-sm">
                <h6 class="fw-bold border-bottom pb-2 mb-3">PAYMENT BREAKDOWN</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span>&#x20B9;{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount ({{ $order->coupon_code }}):</span>
                        <span>-&#x20B9;{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping:</span>
                    <span>{{ $order->shipping_amount == 0 ? 'FREE' : '&#x20B9;' . number_format($order->shipping_amount, 2) }}</span>
                </div>
                @if($order->cod_charge > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">COD Charge:</span>
                        <span>&#x20B9;{{ number_format($order->cod_charge, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between border-top pt-2 fw-bold fs-6">
                    <span>Total Amount:</span>
                    <span class="text-danger">&#x20B9;{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card border-0 bg-white shadow-sm rounded-3 p-4 font-sm">
                <h6 class="fw-bold border-bottom pb-2 mb-3">SHIPPING ADDRESS</h6>
                <strong class="d-block mb-1">{{ $order->customer_name }}</strong>
                <div class="text-muted leading-relaxed mb-2">{{ $order->formatted_shipping_address }}</div>
                <div class="text-muted"><i class="fa-solid fa-phone me-1"></i> {{ $order->customer_phone }}</div>
                <div class="text-muted"><i class="fa-solid fa-envelope me-1"></i> {{ $order->customer_email }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
