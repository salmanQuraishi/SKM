@extends('layouts.app')

@section('title', 'Shopping Cart - AutoLogos')

@section('content')
<div class="py-3 border-bottom mb-4" style="background: #090d16; border-color: #1e293b !important;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Shopping Cart</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <h2 class="fw-bold brand-font text-uppercase mb-4 text-white">YOUR SHOPPING <span class="text-danger">CART</span></h2>

    @if($cart->items->isEmpty())
        <div class="card border p-5 text-center my-4" style="background: #111827; border-color: #1e293b !important;">
            <i class="fa-solid fa-cart-shopping text-secondary display-3 mb-3"></i>
            <h4 class="fw-bold text-white">Your Cart is Empty</h4>
            <p class="text-secondary">Looks like you haven't added any emblems or car logos to your cart yet.</p>
            <div class="mt-3">
                <a href="{{ route('shop') }}" class="btn btn-theme-red px-4 py-2">
                    <i class="fa-solid fa-bag-shopping me-2"></i> Continue Shopping
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Cart Items Table -->
            <div class="col-lg-8">
                <div class="card border shadow-sm rounded-3 overflow-hidden" style="background: #111827; border-color: #1e293b !important;">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="color: #e2e8f0;">
                            <thead>
                                <tr class="font-sm text-uppercase text-secondary" style="background: #090d16; border-color: #1e293b;">
                                    <th style="width: 100px;">Product</th>
                                    <th>Details</th>
                                    <th class="text-center" style="width: 140px;">Quantity</th>
                                    <th class="text-end">Total</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart->items as $item)
                                    <tr style="border-color: #1e293b;">
                                        <td>
                                            <div class="p-2 border rounded-2 text-center" style="width: 80px; height: 80px; background: #161f30; border-color: #1e293b !important;">
                                                <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-contain" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('product.detail', $item->product->slug) }}" class="fw-bold text-white text-decoration-none font-sm d-block">
                                                {{ $item->product->name }}
                                            </a>
                                            @if($item->variant)
                                                <span class="badge bg-secondary-subtle text-secondary small">Variant: {{ $item->variant->name }}</span>
                                            @endif
                                            <div class="text-danger fw-bold small mt-1">&#x20B9;{{ number_format($item->unit_price, 2) }}</div>
                                        </td>
                                        <td>
                                            <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center justify-content-center">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                <div class="input-group input-group-sm" style="width: 110px;">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty(this, -1)">-</button>
                                                    <input type="number" name="quantity" class="form-control text-center fw-bold px-1" value="{{ $item->quantity }}" min="1" onchange="this.form.submit()" style="background: #0b0f19; border-color: #334155; color: #f8fafc;">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateItemQty(this, 1)">+</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-end fw-bold text-white font-sm">
                                            &#x20B9;{{ number_format($item->total_price, 2) }}
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Remove item">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="{{ route('shop') }}" class="btn btn-outline-secondary text-light btn-sm" style="background: #111827; border-color: #334155;">
                        <i class="fa-solid fa-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <div class="card border shadow-sm rounded-3 p-4" style="background: #111827; border-color: #1e293b !important;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-white" style="border-color: #1e293b !important;">ORDER SUMMARY</h5>

                    <!-- Free Shipping Progress -->
                    @if($totals['free_shipping_eligible'])
                        <div class="alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-3" style="background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.3); color: #34d399;">
                            <i class="fa-solid fa-circle-check text-success fs-5"></i>
                            <span>You are eligible for <strong>FREE SHIPPING!</strong></span>
                        </div>
                    @else
                        <div class="alert alert-warning py-2 px-3 small d-flex align-items-center gap-2 mb-3" style="background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.3); color: #fbbf24;">
                            <i class="fa-solid fa-truck-fast text-warning fs-5"></i>
                            <span>Add <strong>&#x20B9;{{ number_format($totals['free_shipping_remaining'], 2) }}</strong> more to get Free Shipping!</span>
                        </div>
                    @endif

                    <!-- Coupon Code Input -->
                    <div class="mb-4 pb-3 border-bottom" style="border-color: #1e293b !important;">
                        <label class="fw-bold font-sm mb-2 text-white"><i class="fa-solid fa-ticket text-danger me-1"></i> Apply Coupon Code</label>
                        @if($totals['coupon'])
                            <div class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background: #161f30; border: 1px solid #1e293b;">
                                <div>
                                    <span class="badge bg-success font-sm">{{ $totals['coupon'] }}</span>
                                    <span class="text-success small ms-2 fw-semibold">-&#x20B9;{{ number_format($totals['discount'], 2) }} OFF</span>
                                </div>
                                <form action="{{ route('cart.coupon.remove') }}" method="POST" class="mb-0" id="couponRemoveForm">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" id="couponRemoveBtn">Remove</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon.apply') }}" method="POST" id="couponApplyForm">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="code" id="couponCodeInput" class="form-control form-control-sm text-uppercase" placeholder="e.g. AUTO10" required style="background: #0b0f19; border-color: #334155; color: #f8fafc;">
                                    <button class="btn btn-secondary btn-sm px-3" type="submit" id="couponApplyBtn" style="background: #1e293b; border-color: #334155;">Apply</button>
                                </div>
                                <div id="couponFeedback" class="small mt-2 d-none"></div>
                            </form>
                        @endif
                    </div>

                    <!-- Totals Breakdown -->
                    <div class="d-flex flex-column gap-2 font-sm mb-4">
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Subtotal:</span>
                            <span class="fw-bold text-white">&#x20B9;{{ number_format($totals['subtotal'], 2) }}</span>
                        </div>

                        @if($totals['discount'] > 0)
                            <div class="d-flex justify-content-between text-success">
                                <span>Coupon Discount:</span>
                                <span>-&#x20B9;{{ number_format($totals['discount'], 2) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Estimated GST (18% Included):</span>
                            <span class="text-white">&#x20B9;{{ number_format($totals['tax'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Shipping:</span>
                            <span>
                                @if($totals['shipping'] == 0)
                                    <strong class="text-success">FREE</strong>
                                @else
                                    <span class="text-white">&#x20B9;{{ number_format($totals['shipping'], 2) }}</span>
                                @endif
                            </span>
                        </div>

                        <div class="border-top pt-3 d-flex justify-content-between align-items-center" style="border-color: #1e293b !important;">
                            <span class="fw-bold fs-5 text-white">Grand Total:</span>
                            <span class="fw-black fs-4 text-danger">&#x20B9;{{ number_format($totals['total'], 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout') }}" class="btn btn-theme-red w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <span>PROCEED TO CHECKOUT</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function updateItemQty(btn, delta) {
        const form = btn.closest('form');
        const input = form.querySelector('input[name="quantity"]');
        const currentVal = parseInt(input.value) || 1;
        const newVal = currentVal + delta;
        if (newVal >= 1) {
            input.value = newVal;
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const couponForm = document.getElementById('couponApplyForm');
        if (couponForm) {
            couponForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('couponApplyBtn');
                const input = document.getElementById('couponCodeInput');
                const feedback = document.getElementById('couponFeedback');
                const code = input.value.trim();

                if (!code) return;

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
                feedback.classList.add('d-none');

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || couponForm.querySelector('input[name="_token"]')?.value;

                fetch("{{ route('cart.coupon.apply') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ code: code })
                })
                .then(async res => {
                    if (res.status === 419) {
                        window.location.reload();
                        return;
                    }
                    return res.json();
                })
                .then(data => {
                    if (!data) return;
                    if (data.success) {
                        window.location.reload();
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = 'Apply';
                        feedback.className = 'alert alert-danger py-1 px-2 small mt-2 mb-0';
                        feedback.innerHTML = '<i class="fa-solid fa-circle-exclamation me-1"></i> ' + (data.message || 'Invalid coupon code.');
                        feedback.classList.remove('d-none');
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Invalid coupon code.', 'error');
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = 'Apply';
                    feedback.className = 'alert alert-danger py-1 px-2 small mt-2 mb-0';
                    feedback.innerHTML = '<i class="fa-solid fa-circle-exclamation me-1"></i> Could not apply coupon. Please try again.';
                    feedback.classList.remove('d-none');
                    if (typeof showToast === 'function') {
                        showToast('Could not apply coupon. Please try again.', 'error');
                    }
                });
            });
        }

        const removeForm = document.getElementById('couponRemoveForm');
        if (removeForm) {
            removeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('couponRemoveBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || removeForm.querySelector('input[name="_token"]')?.value;

                fetch("{{ route('cart.coupon.remove') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(() => {
                    window.location.reload();
                })
                .catch(() => {
                    removeForm.submit();
                });
            });
        }
    });
</script>
@endpush
