@extends('layouts.app')

@section('title', 'Order Confirmed - AutoLogos')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border rounded-4 p-4 p-md-5 text-center" style="background: #111827; border-color: #1e293b !important;">
                <div class="mb-4">
                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-check display-5"></i>
                    </div>
                </div>

                <h2 class="fw-bold brand-font text-uppercase mb-2 text-white">THANK YOU FOR YOUR <span class="text-danger">ORDER!</span></h2>
                <p class="text-secondary fs-6 mb-4">Your order has been confirmed and is now being processed for dispatch.</p>

                <div class="card border p-4 text-start rounded-3 mb-4 font-sm" style="background: #161f30; border-color: #1e293b !important; color: #e2e8f0;">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-secondary d-block">Order Number:</span>
                            <strong class="fs-5 text-danger">{{ $order->order_number }}</strong>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <span class="text-secondary d-block">Order Date:</span>
                            <strong class="text-white">{{ $order->created_at->format('d M, Y h:i A') }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-secondary d-block">Customer:</span>
                            <strong class="text-white">{{ $order->customer_name }} ({{ $order->customer_phone }})</strong>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <span class="text-secondary d-block">Payment Method:</span>
                            <span class="badge {{ $order->payment_method === 'cod' ? 'bg-secondary' : 'bg-success' }} text-uppercase">
                                {{ $order->payment_method === 'cod' ? 'Cash on Delivery (Pending)' : 'Online Payment (Paid)' }}
                            </span>
                        </div>
                        <div class="col-12 border-top pt-3" style="border-color: #1e293b !important;">
                            <span class="text-secondary d-block">Shipping To:</span>
                            <div class="text-white">{{ $order->formatted_shipping_address }}</div>
                        </div>
                    </div>
                </div>

                <!-- Order Items Mini Breakdown -->
                <div class="card border p-3 text-start rounded-3 mb-4" style="background: #161f30; border-color: #1e293b !important; color: #e2e8f0;">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-white" style="border-color: #1e293b !important;">Order Items:</h6>
                    <div class="d-flex flex-column gap-2 font-sm">
                        @foreach($order->items as $item)
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-white">{{ $item->product_name }}</strong>
                                    @if($item->variant_name)
                                        <span class="text-secondary small">({{ $item->variant_name }})</span>
                                    @endif
                                    <span class="text-secondary"> × {{ $item->quantity }}</span>
                                </div>
                                <span class="fw-bold text-white">&#x20B9;{{ number_format($item->total, 2) }}</span>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between border-top pt-2 mt-2 fw-bold fs-6" style="border-color: #1e293b !important;">
                            <span class="text-white">Total Paid / Payable:</span>
                            <span class="text-danger">&#x20B9;{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions: WhatsApp, Invoices, Track -->
                <div class="row g-2 justify-content-center mb-4">
                    <div class="col-md-6">
                        <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="fa-brands fa-whatsapp fs-5"></i> SEND DETAILS VIA WHATSAPP
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('order.track.view', ['order_number' => $order->order_number, 'phone' => $order->customer_phone]) }}" class="btn btn-secondary w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2" style="background: #1e293b; border-color: #334155;">
                            <i class="fa-solid fa-location-crosshairs"></i> TRACK YOUR ORDER
                        </a>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ route('account.order.invoice', $order->order_number) }}" class="btn btn-outline-danger btn-sm">
                        <i class="fa-solid fa-file-pdf me-1"></i> Download Tax Invoice (PDF)
                    </a>
                    @if($order->payment_status === 'paid')
                        <a href="{{ route('account.order.receipt', $order->order_number) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-receipt me-1"></i> Download Payment Receipt (PDF)
                        </a>
                    @endif
                    <a href="{{ route('shop') }}" class="btn btn-outline-secondary text-light btn-sm" style="background: #111827; border-color: #334155;">
                        <i class="fa-solid fa-bag-shopping me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
