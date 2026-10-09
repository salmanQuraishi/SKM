@extends('layouts.app')

@section('title', "Order #{$order->order_number} - AutoLogos")

@section('content')
<div class="py-3 border-bottom mb-4" style="background: #090d16; border-color: #1e293b !important;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.orders') }}" class="text-decoration-none text-secondary">My Orders</a></li>
                <li class="breadcrumb-item active text-light fw-semibold" aria-current="page">{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold brand-font text-uppercase mb-1 text-white">ORDER <span class="text-danger">#{{ $order->order_number }}</span></h3>
            <span class="text-secondary font-sm">Placed on {{ $order->created_at->format('d F Y, h:i A') }}</span>
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
            <a href="{{ route('order.track.view', ['order_number' => $order->order_number, 'phone' => $order->customer_phone]) }}" class="btn btn-outline-light btn-sm">
                <i class="fa-solid fa-location-crosshairs me-1"></i> Live Tracking
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Items & Status Timeline -->
        <div class="col-lg-8">
            <!-- Items Card -->
            <div class="card border rounded-3 p-4 mb-4 shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-white" style="border-color: #1e293b !important;">Order Items ({{ $order->items->count() }})</h5>

                <div class="table-responsive">
                    <table class="table align-middle font-sm mb-0" style="border-color: #1e293b;">
                        <thead>
                            <tr style="background: #161f30; color: #f8fafc; border-color: #1e293b;">
                                <th class="py-2 px-3 border-0">Product</th>
                                <th class="py-2 px-3 border-0">Unit Price</th>
                                <th class="py-2 px-3 border-0 text-center">Quantity</th>
                                <th class="py-2 px-3 border-0 text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr style="border-color: #1e293b;">
                                    <td class="px-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width: 50px; height: 50px; background: #0b0f19; border-color: #1e293b !important;" class="border rounded p-1 shadow-sm d-flex align-items-center justify-content-center">
                                                <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-contain" alt="{{ $item->product_name }}" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-white">{{ $item->product_name }}</div>
                                                <span class="text-secondary small">SKU: {{ $item->product_sku }}</span>
                                                @if($item->variant_name)
                                                    <span class="badge bg-secondary-subtle text-secondary small ms-1">{{ $item->variant_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 text-secondary">&#x20B9;{{ number_format($item->price, 2) }}</td>
                                    <td class="px-3 text-center text-white">{{ $item->quantity }}</td>
                                    <td class="px-3 text-end fw-bold text-white">&#x20B9;{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card border rounded-3 p-4 shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <h5 class="fw-bold mb-3 border-bottom pb-2 text-white" style="border-color: #1e293b !important;">Order Status History</h5>
                <div class="d-flex flex-column gap-3">
                    @foreach($order->statusHistory as $hist)
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;">
                                <i class="fa-solid fa-check" style="font-size: 11px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-uppercase font-sm text-light">{{ str_replace('_', ' ', $hist->status) }}</strong>
                                    <span class="text-secondary small">{{ $hist->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="text-secondary small">{{ $hist->comment }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Summary & Address Sidebar -->
        <div class="col-lg-4">
            <!-- Payment & Order Status -->
            <div class="card border rounded-3 p-4 mb-4 font-sm shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <h6 class="fw-bold border-bottom pb-2 mb-3 text-white" style="border-color: #1e293b !important;">ORDER STATUS</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Order Status:</span>
                    <span class="badge {{ $order->status_badge_class }} text-uppercase">{{ $order->order_status }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Payment Method:</span>
                    <strong class="text-uppercase text-light">{{ $order->payment_method }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Payment Status:</span>
                    <span class="badge {{ $order->payment_badge_class }} text-uppercase">{{ $order->payment_status }}</span>
                </div>
                @if($order->courier_name)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Courier:</span>
                        <strong class="text-light">{{ $order->courier_name }}</strong>
                    </div>
                @endif
                @if($order->tracking_number)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Tracking No:</span>
                        <strong class="text-danger">{{ $order->tracking_number }}</strong>
                    </div>
                @endif
            </div>

            <!-- Price Breakdown -->
            <div class="card border rounded-3 p-4 mb-4 font-sm shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <h6 class="fw-bold border-bottom pb-2 mb-3 text-white" style="border-color: #1e293b !important;">PAYMENT BREAKDOWN</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Subtotal:</span>
                    <span class="text-light">&#x20B9;{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Discount ({{ $order->coupon_code }}):</span>
                        <span>-&#x20B9;{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Shipping:</span>
                    <span class="text-light">{{ $order->shipping_amount == 0 ? 'FREE' : '&#x20B9;' . number_format($order->shipping_amount, 2) }}</span>
                </div>
                @if($order->cod_charge > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">COD Charge:</span>
                        <span class="text-light">&#x20B9;{{ number_format($order->cod_charge, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between border-top pt-2 fw-bold fs-6" style="border-color: #1e293b !important;">
                    <span class="text-white">Total Amount:</span>
                    <span class="text-danger">&#x20B9;{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card border rounded-3 p-4 font-sm shadow-sm" style="background: #111827; border-color: #1e293b !important;">
                <h6 class="fw-bold border-bottom pb-2 mb-3 text-white" style="border-color: #1e293b !important;">SHIPPING ADDRESS</h6>
                <strong class="d-block mb-1 text-white">{{ $order->customer_name }}</strong>
                <div class="text-secondary leading-relaxed mb-2">{{ $order->formatted_shipping_address }}</div>
                <div class="text-secondary"><i class="fa-solid fa-phone me-1 text-danger"></i> {{ $order->customer_phone }}</div>
                <div class="text-secondary"><i class="fa-solid fa-envelope me-1 text-danger"></i> {{ $order->customer_email }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
