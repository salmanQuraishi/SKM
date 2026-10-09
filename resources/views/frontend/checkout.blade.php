@extends('layouts.app')

@section('title', 'Secure Checkout - AutoLogos')

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">Cart</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <h2 class="fw-bold brand-font text-uppercase mb-4">SECURE <span class="text-danger">CHECKOUT</span></h2>

    <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Left Column: Customer & Address Information -->
            <div class="col-lg-7">
                <!-- 1. Customer Details -->
                <div class="card border-0 bg-white shadow-sm rounded-3 p-4 mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-user text-danger me-2"></i> 1. Contact Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user?->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">10-Digit Mobile Number *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">+91</span>
                                <input type="text" name="mobile" class="form-control" placeholder="9876543210" maxlength="10" value="{{ old('mobile', $user?->phone) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-sm fw-bold">Email Address *</label>
                            <input type="email" name="email" class="form-control" placeholder="yourname@gmail.com" value="{{ old('email', $user?->email) }}" required>
                            <div class="form-text">Order receipt and shipment tracking details will be sent here.</div>
                        </div>
                    </div>
                </div>

                <!-- 2. Shipping Address -->
                <div class="card border-0 bg-white shadow-sm rounded-3 p-4 mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-location-dot text-danger me-2"></i> 2. Delivery Address</h5>

                    @if($savedAddresses->isNotEmpty())
                        <div class="mb-4">
                            <label class="form-label font-sm fw-bold text-muted">Use a Saved Address:</label>
                            <div class="d-flex flex-column gap-2">
                                @foreach($savedAddresses as $addr)
                                    <div class="form-check p-3 border rounded-2 bg-light cursor-pointer">
                                        <input class="form-check-input ms-0 me-2" type="radio" name="saved_address_id" id="addr_{{ $addr->id }}" onchange="fillSavedAddress({{ json_encode($addr) }})">
                                        <label class="form-check-label font-sm w-100" for="addr_{{ $addr->id }}">
                                            <strong>{{ $addr->name }}</strong> ({{ $addr->mobile }}) - <span class="badge bg-secondary">{{ $addr->address_type }}</span><br>
                                            <span class="text-secondary">{{ $addr->formatted_address }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label font-sm fw-bold">Flat, House No., Building, Apartment *</label>
                            <input type="text" name="address_line_1" id="address_line_1" class="form-control" value="{{ old('address_line_1') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-sm fw-bold">Area, Colony, Street, Sector</label>
                            <input type="text" name="address_line_2" id="address_line_2" class="form-control" value="{{ old('address_line_2') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">Landmark</label>
                            <input type="text" name="landmark" id="landmark" class="form-control" placeholder="e.g. Near Metro Station" value="{{ old('landmark') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">6-Digit PIN Code *</label>
                            <input type="text" name="pincode" id="pincode" class="form-control" maxlength="6" value="{{ old('pincode') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">City / District *</label>
                            <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold">State *</label>
                            <input type="text" name="state" id="state" class="form-control" value="{{ old('state') }}" required>
                        </div>

                        @auth
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="save_address" id="save_address" value="1" checked>
                                    <label class="form-check-label font-sm" for="save_address">Save this address to my account for future orders</label>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- 3. Payment Method Selection -->
                <div class="card border-0 bg-white shadow-sm rounded-3 p-4 mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-credit-card text-danger me-2"></i> 3. Payment Method</h5>

                    <div class="d-flex flex-column gap-3">
                        <!-- Razorpay Online -->
                        <div class="form-check border rounded-3 p-3 cursor-pointer payment-method-card active" onclick="selectPaymentMethod('razorpay', this)">
                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="pay_razorpay" value="razorpay" checked>
                            <label class="form-check-label fw-bold font-sm d-flex justify-content-between align-items-center w-100" for="pay_razorpay">
                                <div>
                                    <span>Online Payment (UPI, Credit/Debit Card, NetBanking)</span>
                                    <div class="text-muted fw-normal small">Fast & 100% Secure via Razorpay Gateway</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-success-subtle text-success">UPI</span>
                                    <span class="badge bg-primary-subtle text-primary">Cards</span>
                                </div>
                            </label>
                        </div>

                        <!-- Cash On Delivery -->
                        @if($codEnabled)
                            <div class="form-check border rounded-3 p-3 cursor-pointer payment-method-card" onclick="selectPaymentMethod('cod', this)">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="pay_cod" value="cod">
                                <label class="form-check-label fw-bold font-sm d-flex justify-content-between align-items-center w-100" for="pay_cod">
                                    <div>
                                        <span>Cash On Delivery (COD)</span>
                                        <div class="text-muted fw-normal small">Pay cash or UPI at the time of delivery (&#x20B9;{{ number_format((float)($globalSettings['shipping']['cod_charge'] ?? 50), 0) }} COD handling fee)</div>
                                    </div>
                                    <span class="badge bg-secondary-subtle text-secondary">Pay on Delivery</span>
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Order Notes -->
                <div class="card border-0 bg-white shadow-sm rounded-3 p-4">
                    <label class="form-label font-sm fw-bold"><i class="fa-solid fa-comment-dots text-danger me-2"></i> Order Notes / Delivery Instructions (Optional)</label>
                    <textarea name="customer_notes" rows="2" class="form-control font-sm" placeholder="e.g. Please call before delivery, deliver in morning..."></textarea>
                </div>
            </div>

            <!-- Right Column: Order Summary & Place Order -->
            <div class="col-lg-5">
                <div class="card border-0 bg-white shadow-sm rounded-3 p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">ORDER ITEMS ({{ $cart->items->sum('quantity') }})</h5>

                    <div class="d-flex flex-column gap-3 mb-4 overflow-y-auto" style="max-height: 250px;">
                        @foreach($cart->items as $item)
                            <div class="d-flex align-items-center gap-3 border-bottom pb-2">
                                <div class="p-1 border rounded bg-light" style="width: 50px; height: 50px;">
                                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-contain" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold font-sm text-truncate" style="max-width: 220px;">{{ $item->product->name }}</div>
                                    <div class="text-muted small">Qty: {{ $item->quantity }} × &#x20B9;{{ number_format($item->unit_price, 0) }}</div>
                                </div>
                                <div class="fw-bold font-sm text-dark">
                                    &#x20B9;{{ number_format($item->total_price, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Price Calculations -->
                    <div class="d-flex flex-column gap-2 font-sm mb-4">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-bold" id="checkoutSubtotal">&#x20B9;{{ number_format($totals['subtotal'], 2) }}</span>
                        </div>

                        @if($totals['discount'] > 0)
                            <div class="d-flex justify-content-between text-success">
                                <span>Discount ({{ $totals['coupon'] }}):</span>
                                <span>-&#x20B9;{{ number_format($totals['discount'], 2) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Shipping:</span>
                            <span>{{ $totals['shipping'] == 0 ? 'FREE' : '&#x20B9;' . number_format($totals['shipping'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between" id="codRow" style="display: none !important;">
                            <span class="text-muted">COD Convenience Fee:</span>
                            <span>&#x20B9;{{ number_format((float)($globalSettings['shipping']['cod_charge'] ?? 50), 2) }}</span>
                        </div>

                        <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">Total Payable:</span>
                            <span class="fw-black fs-4 text-danger" id="checkoutGrandTotal">₹ {{ number_format($totals['total'], 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" id="placeOrderBtn" class="btn btn-theme-red w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow">
                        <i class="fa-solid fa-lock"></i>
                        <span id="placeOrderBtnText">PLACE ORDER NOW</span>
                    </button>

                    <div class="text-center text-muted small mt-3">
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> 256-Bit SSL Encrypted & Verified Checkout
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .payment-method-card.active {
        border-color: var(--primary-red) !important;
        background-color: #fff1f2 !important;
    }
</style>
@endpush

@push('scripts')
<!-- Razorpay Checkout SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    function selectPaymentMethod(method, elem) {
        document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
        elem.classList.add('active');
        const radio = elem.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;

        const codRow = document.getElementById('codRow');
        const grandTotalElem = document.getElementById('checkoutGrandTotal');
        const baseTotal = {{ $totals['total'] }};
        const codCharge = {{ (float)($globalSettings['shipping']['cod_charge'] ?? 50) }};

        if (method === 'cod') {
            if (codRow) codRow.style.setProperty('display', 'flex', 'important');
            grandTotalElem.innerText = '₹ ' + (baseTotal + codCharge).toFixed(2);
        } else {
            if (codRow) codRow.style.setProperty('display', 'none', 'important');
            grandTotalElem.innerText = '₹ ' + baseTotal.toFixed(2);
        }
    }

    function fillSavedAddress(addr) {
        document.getElementById('address_line_1').value = addr.address_line_1 || '';
        document.getElementById('address_line_2').value = addr.address_line_2 || '';
        document.getElementById('landmark').value = addr.landmark || '';
        document.getElementById('pincode').value = addr.pincode || '';
        document.getElementById('city').value = addr.city || '';
        document.getElementById('state').value = addr.state || '';
    }

    // Checkout Form Submission (handles COD and Razorpay popup modal)
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

        if (selectedMethod === 'cod') {
            // Normal post form submission for Cash on Delivery
            document.getElementById('placeOrderBtn').disabled = true;
            document.getElementById('placeOrderBtnText').innerText = 'Processing Order...';
            return true;
        }

        // For Online Payment (Razorpay)
        e.preventDefault();
        const btn = document.getElementById('placeOrderBtn');
        btn.disabled = true;
        document.getElementById('placeOrderBtnText').innerText = 'Initializing Payment...';

        const formData = new FormData(this);

        fetch('{{ route("checkout.process") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            document.getElementById('placeOrderBtnText').innerText = 'PLACE ORDER NOW';

            if (!data.success) {
                showToast(data.message || 'Error initiating order.', 'error');
                return;
            }

            // Open Razorpay Modal
            const options = {
                "key": data.key_id,
                "amount": data.amount,
                "currency": data.currency,
                "name": "AutoLogos",
                "description": `Order #${data.order_number}`,
                "image": "{{ asset('images/hero_car.svg') }}",
                "order_id": data.razorpay_order_id,
                "prefill": {
                    "name": data.customer_name,
                    "email": data.customer_email,
                    "contact": data.customer_phone
                },
                "theme": {
                    "color": "#d90429"
                },
                "handler": function (response) {
                    // Send signature for server-side verification
                    verifyPaymentServer(data.order_number, response);
                },
                "modal": {
                    "ondismiss": function() {
                        showToast('Payment window was closed.', 'error');
                    }
                }
            };

            // If in test environment without real API keys, provide sandbox simulation
            if (data.key_id.includes('sample') || data.key_id.includes('placeholder')) {
                // Simulate successful payment directly in test/dev environment
                const mockRzpPaymentId = 'pay_mock_' + Math.random().toString(36).substring(2, 10);
                verifyPaymentServer(data.order_number, {
                    razorpay_order_id: data.razorpay_order_id,
                    razorpay_payment_id: mockRzpPaymentId,
                    razorpay_signature: 'mock_signature_valid'
                });
                return;
            }

            const rzp = new Razorpay(options);
            rzp.open();
        })
        .catch(err => {
            btn.disabled = false;
            document.getElementById('placeOrderBtnText').innerText = 'PLACE ORDER NOW';
            console.error(err);
            showToast('Something went wrong. Please try again.', 'error');
        });
    });

    function verifyPaymentServer(orderNumber, rzpResponse) {
        showToast('Verifying payment...', 'info');

        fetch('{{ route("checkout.verify-payment") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                order_number: orderNumber,
                razorpay_order_id: rzpResponse.razorpay_order_id,
                razorpay_payment_id: rzpResponse.razorpay_payment_id,
                razorpay_signature: rzpResponse.razorpay_signature
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                showToast(data.message || 'Payment verification failed.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Payment verification error.', 'error');
        });
    }
</script>
@endpush
