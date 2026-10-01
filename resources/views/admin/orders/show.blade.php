@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number . ' - AutoLogos Admin')
@section('page_title', 'Order Details: #' . $order->order_number)

@section('content')
<div class="container-fluid p-0">
    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">Order #{{ $order->order_number }}</h4>
                @php
                    $statusColors = [
                        'pending' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                        'confirmed' => 'bg-info-subtle text-info-emphasis border-info-subtle',
                        'processing' => 'bg-primary-subtle text-primary-emphasis border-primary-subtle',
                        'packed' => 'bg-secondary-subtle text-secondary-emphasis border-secondary-subtle',
                        'shipped' => 'bg-primary text-white',
                        'out_for_delivery' => 'bg-info text-white',
                        'delivered' => 'bg-success text-white',
                        'cancelled' => 'bg-danger text-white',
                    ];
                    $badgeClass = $statusColors[$order->order_status] ?? 'bg-light text-dark border';
                @endphp
                <span class="badge {{ $badgeClass }} text-uppercase px-3 py-2">
                    {{ str_replace('_', ' ', $order->order_status) }}
                </span>
                @if($order->payment_status === 'paid')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i> PAID</span>
                @else
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2"><i class="fa-solid fa-clock me-1"></i> {{ strtoupper($order->payment_status) }}</span>
                @endif
            </div>
            <p class="text-muted small mb-0 mt-1">Placed on {{ $order->created_at->format('d M Y, h:i A') }} &bull; Source: Web Checkout</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.orders.invoice.download', $order->id) }}" class="btn btn-outline-danger btn-sm">
                <i class="fa-solid fa-file-pdf me-1"></i> Download Invoice
            </a>
            <a href="{{ route('admin.orders.invoice.stream', $order->id) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-print me-1"></i> Print Invoice
            </a>
            <a href="{{ route('admin.orders.shipping.download', [$order->id, 'format' => 'thermal']) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-barcode me-1"></i> Shipping Slip (4x6)
            </a>
            <a href="{{ route('admin.orders.receipt.download', $order->id) }}" class="btn btn-outline-success btn-sm">
                <i class="fa-solid fa-receipt me-1"></i> Payment Receipt
            </a>
            <a href="{{ $whatsappConfirmUrl }}" target="_blank" class="btn btn-success btn-sm">
                <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp Customer
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-light border btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left 8 Columns: Products & History -->
        <div class="col-lg-8">
            <!-- Ordered Items Table Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-boxes-stacked text-danger me-2"></i> Ordered Items ({{ $order->items->count() }})</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 70px;">Item</th>
                                <th>Product Details</th>
                                <th>Unit Price</th>
                                <th>Tax</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end pe-4">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4" style="width: 80px;">
                                        <div class="product-thumb-box d-flex align-items-center justify-content-center bg-white border rounded-3 p-1 shadow-sm position-relative overflow-hidden" 
                                             style="width: 58px; height: 58px; min-width: 58px; border-color: #e2e8f0 !important; background: #ffffff;">
                                            @if($item->product)
                                                <a href="{{ route('admin.products.edit', $item->product->id) }}" 
                                                   title="View Product #{{ $item->product->id }}" 
                                                   target="_blank" 
                                                   class="d-flex align-items-center justify-content-center w-100 h-100 text-decoration-none">
                                                    <img src="{{ $item->image_url }}" 
                                                         alt="{{ $item->product_name }}" 
                                                         class="w-100 h-100 object-fit-contain" 
                                                         loading="lazy"
                                                         onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                                                </a>
                                            @else
                                                <img src="{{ $item->image_url }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     class="w-100 h-100 object-fit-contain" 
                                                     loading="lazy"
                                                     onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            @if($item->product)
                                                <a href="{{ route('admin.products.edit', $item->product->id) }}" target="_blank" class="text-dark text-decoration-none hover-theme-red">
                                                    {{ $item->product_name }}
                                                </a>
                                            @else
                                                {{ $item->product_name }}
                                            @endif
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                            <small class="text-muted">SKU: <code>{{ $item->product_sku ?: ($item->sku ?: 'N/A') }}</code></small>
                                            @if($item->variant_name)
                                                <span class="badge bg-light text-dark border">{{ $item->variant_name }}</span>
                                            @endif
                                            @if(!$item->product)
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">Archived Product</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-dark">&#x20B9;{{ number_format($item->price ?? $item->unit_price ?? 0, 2) }}</td>
                                    <td class="text-muted small">&#x20B9;{{ number_format($item->tax_amount ?? 0, 2) }}</td>
                                    <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                    <td class="text-end pe-4 fw-bold text-dark">&#x20B9;{{ number_format($item->total ?? $item->total_price ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Price Summary Footer -->
                <div class="card-footer bg-light border-top p-4">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Subtotal (Net Items):</span>
                                <span class="fw-bold text-dark">&#x20B9;{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 small text-success">
                                    <span>Coupon Discount ({{ $order->coupon_code }}):</span>
                                    <span>- &#x20B9;{{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">GST Tax:</span>
                                <span class="fw-bold text-dark">&#x20B9;{{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Shipping Charges:</span>
                                <span class="fw-bold text-dark">
                                    @if($order->shipping_amount > 0)
                                        &#x20B9;{{ number_format($order->shipping_amount, 2) }}
                                    @else
                                        <span class="text-success fw-bold">FREE</span>
                                    @endif
                                </span>
                            </div>
                            @if($order->cod_charge > 0)
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">COD Convenience Fee:</span>
                                    <span class="fw-bold text-dark">&#x20B9;{{ number_format($order->cod_charge, 2) }}</span>
                                </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-6 fw-bold">
                                <span class="text-dark">Grand Total:</span>
                                <span class="text-danger">&#x20B9;{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status History / Timeline Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock-rotate-left text-danger me-2"></i> Status History & Tracking Logs</h6>
                </div>
                <div class="card-body p-4">
                    <div class="timeline">
                        @forelse($order->statusHistory as $hist)
                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom position-relative">
                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">
                                    <i class="fa-solid fa-check small"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-dark text-uppercase small">{{ str_replace('_', ' ', $hist->status) }}</strong>
                                        <small class="text-muted">{{ $hist->created_at->format('d M Y, h:i A') }}</small>
                                    </div>
                                    @if($hist->comment)
                                        <p class="text-muted small mb-1 mt-1">{{ $hist->comment }}</p>
                                    @endif
                                    @if($hist->tracking_number)
                                        <div class="small text-secondary">AWB/Tracking: <strong>{{ $hist->tracking_number }}</strong> ({{ $hist->courier_name }})</div>
                                    @endif
                                    <small class="text-muted" style="font-size: 11px;">Updated by: {{ $hist->changed_by ?? 'System' }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">No status change logs available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 4 Columns: Management & Addresses -->
        <div class="col-lg-4">
            <!-- Update Order Status Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-truck-fast text-danger me-2"></i> Update Order Status</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.orders.status.update', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Order Status <span class="text-danger">*</span></label>
                            <select name="order_status" class="form-select" required>
                                <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending Confirmation</option>
                                <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing (Packaging)</option>
                                <option value="packed" {{ $order->order_status === 'packed' ? 'selected' : '' }}>Packed / Ready to Ship</option>
                                <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped / Handed to Courier</option>
                                <option value="out_for_delivery" {{ $order->order_status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered Successfully</option>
                                <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="returned" {{ $order->order_status === 'returned' ? 'selected' : '' }}>Returned</option>
                                <option value="refunded" {{ $order->order_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Courier Partner</label>
                            <select name="courier_name" class="form-select">
                                <option value="">Select Courier</option>
                                <option value="Blue Dart" {{ $order->courier_name === 'Blue Dart' ? 'selected' : '' }}>Blue Dart Express</option>
                                <option value="Delhivery" {{ $order->courier_name === 'Delhivery' ? 'selected' : '' }}>Delhivery Logistics</option>
                                <option value="DTDC" {{ $order->courier_name === 'DTDC' ? 'selected' : '' }}>DTDC Courier</option>
                                <option value="Ekart" {{ $order->courier_name === 'Ekart' ? 'selected' : '' }}>Ekart Logistics</option>
                                <option value="Speed Post" {{ $order->courier_name === 'Speed Post' ? 'selected' : '' }}>India Speed Post</option>
                                <option value="Shadowfax" {{ $order->courier_name === 'Shadowfax' ? 'selected' : '' }}>Shadowfax</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Tracking / AWB Number</label>
                            <input type="text" name="tracking_number" class="form-control" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g., BLU892374829IN">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Expected Delivery Date</label>
                            <input type="date" name="expected_delivery_date" class="form-control" value="{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('Y-m-d') : '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Update Comment / Notes</label>
                            <textarea name="comment" class="form-control" rows="2" placeholder="e.g., Package dispatched via Blue Dart AWB..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-theme-red w-100 fw-bold">
                            <i class="fa-solid fa-arrows-rotate me-2"></i> Update Order Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Customer & Shipping Details Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user text-danger me-2"></i> Customer & Address</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Customer Name</label>
                        <strong class="text-dark">{{ $order->customer_name }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block">Contact Details</label>
                        <div class="fw-semibold text-dark"><i class="fa-solid fa-phone me-2 text-danger"></i> {{ $order->customer_phone }}</div>
                        <div class="small text-muted mt-1"><i class="fa-solid fa-envelope me-2 text-secondary"></i> {{ $order->customer_email }}</div>
                    </div>
                    <hr class="my-3">
                    <div class="mb-3">
                        <label class="text-muted small d-block fw-bold">Delivery Address</label>
                        <p class="small text-dark mb-0">
                            {{ $order->shipping_address_line1 }}<br>
                            @if($order->shipping_address_line2) {{ $order->shipping_address_line2 }}<br> @endif
                            @if($order->shipping_landmark) Landmark: {{ $order->shipping_landmark }}<br> @endif
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} - <strong>{{ $order->shipping_pincode }}</strong><br>
                            {{ $order->shipping_country }}
                        </p>
                    </div>

                    @if($order->order_notes)
                        <hr class="my-3">
                        <div>
                            <label class="text-muted small d-block fw-bold">Customer Notes</label>
                            <p class="small text-muted mb-0 bg-light p-2 rounded">{{ $order->order_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Details Card -->
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-credit-card text-danger me-2"></i> Payment Details</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Payment Method:</span>
                        <strong class="text-uppercase">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Razorpay (Online/UPI)' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Payment Status:</span>
                        <strong class="text-uppercase text-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">{{ $order->payment_status }}</strong>
                    </div>
                    @if($order->payments->count() > 0)
                        @foreach($order->payments as $p)
                            <div class="p-2 bg-light rounded mt-2 small">
                                <div class="text-muted">Transaction Ref: <code>{{ $p->payment_gateway_reference ?: 'N/A' }}</code></div>
                                <div class="text-muted">Amount: &#x20B9;{{ number_format($p->amount, 2) }} &bull; {{ $p->created_at->format('d M, h:i A') }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-thumb-box {
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
    }
    .product-thumb-box:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(217, 4, 41, 0.15) !important;
        border-color: #d90429 !important;
    }
    .hover-theme-red {
        transition: color 0.15s ease;
    }
    .hover-theme-red:hover {
        color: #d90429 !important;
        text-decoration: underline !important;
    }
</style>
@endpush
