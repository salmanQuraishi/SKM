@extends('layouts.app')

@section('title', 'Secure Checkout - AutoLogos')

@section('content')
<div class="py-3 border-bottom mb-4" style="background: #090d16; border-color: #1e293b !important;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none text-secondary">Cart</a></li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <h2 class="fw-bold brand-font text-uppercase mb-4 text-white">SECURE <span class="text-danger">CHECKOUT</span></h2>

    <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Left Column: Customer & Address Information -->
            <div class="col-lg-7">
                <!-- 1. Customer Details -->
                <div class="card border rounded-3 p-4 mb-4" style="background: #111827; border-color: #1e293b !important;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-white" style="border-color: #1e293b !important;"><i class="fa-solid fa-user text-danger me-2"></i> 1. Contact Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-white">Full Name *</label>
                            <input type="text" name="name" id="cust_name" class="form-control" value="{{ old('name', $defaultAddress?->name ?? $user?->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-sm fw-bold text-white">10-Digit Mobile Number *</label>
                            <div class="input-group">
                                <span class="input-group-text">+91</span>
                                <input type="text" name="mobile" id="cust_mobile" class="form-control" placeholder="9876543210" maxlength="10" value="{{ old('mobile', $defaultAddress?->mobile ?? $user?->phone) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-sm fw-bold text-white">Email Address *</label>
                            <input type="email" name="email" id="cust_email" class="form-control" placeholder="yourname@gmail.com" value="{{ old('email', $defaultAddress?->email ?? $user?->email) }}" required>
                            <div class="form-text text-secondary">Order receipt and shipment tracking details will be sent here.</div>
                        </div>
                    </div>
                </div>

                <!-- 2. Shipping Address -->
                <div class="card border rounded-3 p-4 mb-4" style="background: #111827; border-color: #1e293b !important;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3" style="border-color: #1e293b !important;">
                        <h5 class="fw-bold mb-0 text-white"><i class="fa-solid fa-location-dot text-danger me-2"></i> 2. Delivery Address</h5>
                        @if($savedAddresses->isNotEmpty())
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 font-sm">
                                <i class="fa-solid fa-check-circle me-1"></i> Auto Selected
                            </span>
                        @else
                            <span id="liveLocationIndicator" class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 font-sm" style="display: none;">
                                <i class="fa-solid fa-location-dot me-1"></i> <span id="liveLocationCityText">Live Location</span>
                            </span>
                        @endif
                    </div>

                    @if($savedAddresses->isNotEmpty())
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label font-sm fw-bold text-secondary mb-0">Saved Addresses:</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-danger text-decoration-none fw-semibold font-sm" onclick="addNewAddress()">
                                    <i class="fa-solid fa-plus-circle me-1"></i> Deliver to New Address
                                </button>
                            </div>
                            <div class="d-flex flex-column gap-2" id="savedAddressesContainer">
                                @foreach($savedAddresses as $addr)
                                    @php
                                        $isSelected = ($defaultAddress && $defaultAddress->id === $addr->id) || ($loop->first && !$defaultAddress);
                                    @endphp
                                    <div class="form-check p-3 border rounded-2 cursor-pointer saved-address-card {{ $isSelected ? 'active' : '' }}" 
                                         id="card_addr_{{ $addr->id }}" 
                                         style="background: #161f30; border-color: {{ $isSelected ? 'var(--primary-red)' : '#1e293b' }} !important; transition: all 0.2s;"
                                         onclick="selectSavedAddress({{ $addr->id }}, {{ json_encode($addr) }})">
                                        <input class="form-check-input ms-0 me-2 cursor-pointer" type="radio" name="saved_address_id" id="addr_{{ $addr->id }}" value="{{ $addr->id }}" {{ $isSelected ? 'checked' : '' }} onchange="fillSavedAddress({{ json_encode($addr) }})">
                                        <label class="form-check-label font-sm w-100 text-white cursor-pointer" for="addr_{{ $addr->id }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><strong>{{ $addr->name }}</strong> <span class="text-secondary small">({{ $addr->mobile }})</span></span>
                                                <div>
                                                    <span class="badge bg-secondary">{{ $addr->address_type ?? 'Home' }}</span>
                                                    @if($addr->is_default)
                                                        <span class="badge bg-danger ms-1">DEFAULT</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="text-secondary small d-block mt-1">{{ $addr->formatted_address }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row g-3" id="addressFormFields">
                        <!-- 1. Pincode, City & State (Auto-Fill Priority) -->
                        <div class="col-md-4">
                            <label class="form-label font-sm fw-bold text-white d-flex justify-content-between align-items-center">
                                <span>6-Digit PIN Code *</span>
                                <span id="pincodeStatus" class="font-sm" style="font-size: 11px;"></span>
                            </label>
                            <input type="text" name="pincode" id="pincode" class="form-control" maxlength="6" placeholder="e.g. 110001" value="{{ old('pincode', $defaultAddress?->pincode) }}" required oninput="handlePincodeAutoLookup(this.value)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-sm fw-bold text-white">City / District *</label>
                            <input type="text" name="city" id="city" class="form-control" placeholder="Auto-fills from PIN" value="{{ old('city', $defaultAddress?->city) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-sm fw-bold text-white">State *</label>
                            <input type="text" name="state" id="state" class="form-control" placeholder="Auto-fills from PIN" value="{{ old('state', $defaultAddress?->state) }}" required>
                        </div>

                        <!-- 2. Area, Colony, Street, Sector -->
                        <div class="col-12">
                            <label class="form-label font-sm fw-bold text-white d-flex justify-content-between align-items-center">
                                <span>Area, Colony, Street, Sector *</span>
                                <span id="areaSuggestionsHint" class="text-secondary" style="font-size: 11px;"></span>
                            </label>
                            <input type="text" name="address_line_2" id="address_line_2" class="form-control" placeholder="e.g. Sector 14, Civil Lines, Main Market" value="{{ old('address_line_2', $defaultAddress?->address_line_2) }}">
                            <div id="pincodeAreaChips" class="d-flex flex-wrap gap-1 mt-2" style="display: none;"></div>
                        </div>

                        <!-- 3. House / Flat / Building -->
                        <div class="col-12">
                            <label class="form-label font-sm fw-bold text-white">Flat, House No., Building, Apartment *</label>
                            <input type="text" name="address_line_1" id="address_line_1" class="form-control" placeholder="e.g. Flat 302, Royal Residency / House No. 45" value="{{ old('address_line_1', $defaultAddress?->address_line_1) }}" required>
                        </div>

                        <!-- 4. Landmark -->
                        <div class="col-12">
                            <label class="form-label font-sm fw-bold text-white">Landmark (Optional)</label>
                            <input type="text" name="landmark" id="landmark" class="form-control" placeholder="e.g. Near Metro Station / Opposite State Bank" value="{{ old('landmark', $defaultAddress?->landmark) }}">
                        </div>

                        @auth
                            <div class="col-12" id="saveAddressCheckboxWrapper" style="{{ $savedAddresses->isNotEmpty() ? 'display: none;' : '' }}">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="save_address" id="save_address" value="1" checked>
                                    <label class="form-check-label font-sm text-secondary" for="save_address">Save this address to my account for future orders</label>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- 3. Payment Method Selection -->
                <div class="card border rounded-3 p-4 mb-4" style="background: #111827; border-color: #1e293b !important;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-white" style="border-color: #1e293b !important;"><i class="fa-solid fa-credit-card text-danger me-2"></i> 3. Payment Method</h5>

                    <div class="d-flex flex-column gap-3">
                        @if($razorpayEnabled)
                            <!-- Razorpay Online -->
                            <div class="form-check border rounded-3 p-3 cursor-pointer payment-method-card active" onclick="selectPaymentMethod('razorpay', this)">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="pay_razorpay" value="razorpay" checked>
                                <label class="form-check-label fw-bold font-sm d-flex justify-content-between align-items-center w-100 text-white cursor-pointer" for="pay_razorpay">
                                    <div>
                                        <span>Online Payment (UPI, Credit/Debit Card, NetBanking)</span>
                                        <div class="text-secondary fw-normal small">Fast & 100% Secure via Razorpay Gateway</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <span class="badge bg-success-subtle text-success">UPI / QR</span>
                                        <span class="badge bg-primary-subtle text-primary">Cards / NetBanking</span>
                                    </div>
                                </label>
                            </div>
                        @endif

                        @if($codEnabled)
                            <!-- Cash On Delivery -->
                            <div class="form-check border rounded-3 p-3 cursor-pointer payment-method-card {{ !$razorpayEnabled ? 'active' : '' }}" onclick="selectPaymentMethod('cod', this)">
                                <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" id="pay_cod" value="cod" {{ !$razorpayEnabled ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold font-sm d-flex justify-content-between align-items-center w-100 text-white cursor-pointer" for="pay_cod">
                                    <div>
                                        <span>Cash On Delivery (COD)</span>
                                        <div class="text-secondary fw-normal small">Pay cash or UPI at the time of delivery (&#x20B9;{{ number_format((float)($globalSettings['shipping']['cod_charge'] ?? 50), 0) }} COD handling fee)</div>
                                    </div>
                                    <span class="badge bg-secondary-subtle text-secondary">Pay on Delivery</span>
                                </label>
                            </div>
                        @endif

                        @if(!$razorpayEnabled && !$codEnabled)
                            <div class="alert alert-warning mb-0 font-sm">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> No payment methods are currently active. Please contact support.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Order Notes -->
                <div class="card border rounded-3 p-4" style="background: #111827; border-color: #1e293b !important;">
                    <label class="form-label font-sm fw-bold text-white"><i class="fa-solid fa-comment-dots text-danger me-2"></i> Order Notes / Delivery Instructions (Optional)</label>
                    <textarea name="customer_notes" rows="2" class="form-control font-sm" placeholder="e.g. Please call before delivery, deliver in morning..."></textarea>
                </div>
            </div>

            <!-- Right Column: Order Summary & Place Order -->
            <div class="col-lg-5">
                <div class="card border rounded-3 p-4 sticky-top" style="top: 20px; background: #111827; border-color: #1e293b !important;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-white" style="border-color: #1e293b !important;">ORDER ITEMS ({{ $cart->items->sum('quantity') }})</h5>

                    <div class="d-flex flex-column gap-3 mb-4 overflow-y-auto" style="max-height: 250px;">
                        @foreach($cart->items as $item)
                            <div class="d-flex align-items-center gap-3 border-bottom pb-2" style="border-color: #1e293b !important;">
                                <div class="p-1 border rounded" style="width: 50px; height: 50px; background: #161f30; border-color: #1e293b !important;">
                                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-100 h-100 object-fit-contain" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold font-sm text-truncate text-white" style="max-width: 220px;">{{ $item->product->name }}</div>
                                    <div class="text-secondary small">Qty: {{ $item->quantity }} × &#x20B9;{{ number_format($item->unit_price, 0) }}</div>
                                </div>
                                <div class="fw-bold font-sm text-white">
                                    &#x20B9;{{ number_format($item->total_price, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Price Calculations -->
                    <div class="d-flex flex-column gap-2 font-sm mb-4">
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Subtotal:</span>
                            <span class="fw-bold text-white" id="checkoutSubtotal">&#x20B9;{{ number_format($totals['subtotal'], 2) }}</span>
                        </div>

                        @if($totals['discount'] > 0)
                            <div class="d-flex justify-content-between text-success">
                                <span>Discount ({{ $totals['coupon'] }}):</span>
                                <span>-&#x20B9;{{ number_format($totals['discount'], 2) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Shipping:</span>
                            <span class="text-white">{{ $totals['shipping'] == 0 ? 'FREE' : '₹ ' . number_format($totals['shipping'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between" id="codRow" style="display: none !important;">
                            <span class="text-secondary">COD Convenience Fee:</span>
                            <span class="text-white">&#x20B9;{{ number_format((float)($globalSettings['shipping']['cod_charge'] ?? 50), 2) }}</span>
                        </div>

                        <div class="border-top pt-3 d-flex justify-content-between align-items-center" style="border-color: #1e293b !important;">
                            <span class="fw-bold fs-5 text-white">Total Payable:</span>
                            <span class="fw-black fs-4 text-danger" id="checkoutGrandTotal">₹ {{ number_format($totals['total'], 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" id="placeOrderBtn" class="btn btn-theme-red w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow">
                        <i class="fa-solid fa-lock"></i>
                        <span id="placeOrderBtnText">PLACE ORDER NOW</span>
                    </button>

                    <div class="text-center text-secondary small mt-3">
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
    .payment-method-card, .saved-address-card {
        background: #161f30;
        border-color: #1e293b !important;
        color: #f8fafc;
    }
    .payment-method-card.active, .saved-address-card.active {
        border-color: var(--primary-red) !important;
        background-color: rgba(217, 4, 41, 0.12) !important;
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

    function selectSavedAddress(addrId, addr) {
        document.querySelectorAll('.saved-address-card').forEach(c => {
            c.classList.remove('active');
            c.style.setProperty('border-color', '#1e293b', 'important');
        });

        const activeCard = document.getElementById('card_addr_' + addrId);
        if (activeCard) {
            activeCard.classList.add('active');
            activeCard.style.setProperty('border-color', 'var(--primary-red)', 'important');
        }

        const radio = document.getElementById('addr_' + addrId);
        if (radio) radio.checked = true;

        fillSavedAddress(addr);

        const saveWrapper = document.getElementById('saveAddressCheckboxWrapper');
        if (saveWrapper) saveWrapper.style.display = 'none';
    }

    function fillSavedAddress(addr) {
        if (!addr) return;
        if (addr.name && document.getElementById('cust_name')) document.getElementById('cust_name').value = addr.name;
        if (addr.mobile && document.getElementById('cust_mobile')) document.getElementById('cust_mobile').value = addr.mobile;
        if (addr.email && document.getElementById('cust_email')) document.getElementById('cust_email').value = addr.email;

        if (document.getElementById('address_line_1')) document.getElementById('address_line_1').value = addr.address_line_1 || '';
        if (document.getElementById('address_line_2')) document.getElementById('address_line_2').value = addr.address_line_2 || '';
        if (document.getElementById('landmark')) document.getElementById('landmark').value = addr.landmark || '';
        if (document.getElementById('pincode')) document.getElementById('pincode').value = addr.pincode || '';
        if (document.getElementById('city')) document.getElementById('city').value = addr.city || '';
        if (document.getElementById('state')) document.getElementById('state').value = addr.state || '';
    }

    function addNewAddress() {
        document.querySelectorAll('.saved-address-card').forEach(c => {
            c.classList.remove('active');
            c.style.setProperty('border-color', '#1e293b', 'important');
        });
        document.querySelectorAll('input[name="saved_address_id"]').forEach(r => r.checked = false);

        if (document.getElementById('address_line_1')) document.getElementById('address_line_1').value = '';
        if (document.getElementById('address_line_2')) document.getElementById('address_line_2').value = '';
        if (document.getElementById('landmark')) document.getElementById('landmark').value = '';
        if (document.getElementById('pincode')) document.getElementById('pincode').value = '';
        if (document.getElementById('city')) document.getElementById('city').value = '';
        if (document.getElementById('state')) document.getElementById('state').value = '';

        const saveWrapper = document.getElementById('saveAddressCheckboxWrapper');
        if (saveWrapper) {
            saveWrapper.style.display = 'block';
            const chk = document.getElementById('save_address');
            if (chk) chk.checked = true;
        }

        const firstAddrInput = document.getElementById('address_line_1');
        if (firstAddrInput) {
            firstAddrInput.focus();
            firstAddrInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    let pincodeTimer = null;
    function handlePincodeAutoLookup(pincode) {
        pincode = (pincode || '').trim();
        const statusElem = document.getElementById('pincodeStatus');
        if (pincode.length !== 6 || isNaN(pincode)) {
            if (statusElem) statusElem.innerHTML = '';
            return;
        }

        clearTimeout(pincodeTimer);
        pincodeTimer = setTimeout(() => {
            if (statusElem) statusElem.innerHTML = '<span class="text-secondary"><i class="fa-solid fa-spinner fa-spin text-danger me-1"></i> Checking...</span>';
            fetch('https://api.postalpincode.in/pincode/' + pincode)
                .then(res => res.json())
                .then(data => {
                    if (Array.isArray(data) && data[0] && data[0].Status === 'Success' && data[0].PostOffice && data[0].PostOffice.length > 0) {
                        const po = data[0].PostOffice[0];
                        const cityField = document.getElementById('city');
                        const stateField = document.getElementById('state');
                        const addr2Field = document.getElementById('address_line_2');

                        if (cityField && (!cityField.value || cityField.value.trim() === '')) {
                            cityField.value = po.District || po.Block || po.Circle || '';
                        }
                        if (stateField && (!stateField.value || stateField.value.trim() === '')) {
                            stateField.value = po.State || '';
                        }
                        if (addr2Field && (!addr2Field.value || addr2Field.value.trim() === '') && po.Name) {
                            addr2Field.value = po.Name;
                        }
                        if (statusElem) {
                            statusElem.innerHTML = '<span class="text-success"><i class="fa-solid fa-circle-check"></i> ' + (po.District || '') + ', ' + (po.State || '') + '</span>';
                        }
                    } else {
                        if (statusElem) statusElem.innerHTML = '';
                    }
                })
                .catch(() => {
                    if (statusElem) statusElem.innerHTML = '';
                });
        }, 300);
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
                "name": "{{ \App\Models\Setting::get('store_name', 'AutoLogos') }}",
                "description": `Order #${data.order_number}`,
                "image": "{{ asset('images/hero_monogram.svg') }}",
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
                        btn.disabled = false;
                        document.getElementById('placeOrderBtnText').innerText = 'PLACE ORDER NOW';
                        showToast('Payment window closed. Your items are safe in your cart.', 'info');
                    }
                }
            };

            // If in demo / mock test environment without real API keys
            if (data.is_mock) {
                showToast('Demo / Sandbox Mode: Simulating successful Razorpay payment...', 'info');
                setTimeout(() => {
                    const mockRzpPaymentId = 'pay_mock_' + Math.random().toString(36).substring(2, 10);
                    verifyPaymentServer(data.order_number, {
                        razorpay_order_id: data.razorpay_order_id,
                        razorpay_payment_id: mockRzpPaymentId,
                        razorpay_signature: 'mock_signature_valid'
                    });
                }, 800);
                return;
            }

            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function (response) {
                btn.disabled = false;
                document.getElementById('placeOrderBtnText').innerText = 'PLACE ORDER NOW';
                const desc = response.error ? (response.error.description || response.error.reason) : 'Payment failed';
                showToast('Payment cancelled or failed: ' + desc + '. Your items remain in your cart.', 'warning');
            });
            rzp.open();
        })
        .catch(err => {
            btn.disabled = false;
            document.getElementById('placeOrderBtnText').innerText = 'PLACE ORDER NOW';
            console.error(err);
            showToast('Something went wrong. Please try again.', 'error');
        });
    });

    // Automatic Direct Real GPS Live Location Detection (No ISP/Fake IP guesses)
    function autoDetectLiveAddressDirectly() {
        const addr1 = document.getElementById('address_line_1');
        const cityField = document.getElementById('city');

        // Clear any stale IP-based Delhi guess from previous test
        try {
            const raw = localStorage.getItem('autologos_guest_shipping');
            if (raw) {
                const parsed = JSON.parse(raw);
                if (parsed && (!parsed.address_line_1 || parsed.address_line_1.trim() === '') && (parsed.city === 'Delhi' || parsed.city === 'New Delhi')) {
                    localStorage.removeItem('autologos_guest_shipping');
                }
            }
        } catch (e) {}

        // Check previous real guest storage first
        loadGuestAddressFromStorage();

        // Trigger precise device GPS location (Uses actual device satellite/WiFi GPS chip)
        if (navigator.geolocation) {
            const indicator = document.getElementById('liveLocationIndicator');
            const cityText = document.getElementById('liveLocationCityText');
            if (indicator && cityText && (!cityField || !cityField.value)) {
                cityText.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-danger me-1"></i> Detecting Live Location...';
                indicator.style.display = 'inline-block';
            }

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    if (pos && pos.coords) {
                        const lat = pos.coords.latitude;
                        const lon = pos.coords.longitude;
                        reverseGeocodeCoords(lat, lon);
                    }
                },
                function(err) {
                    // If GPS is denied or unavailable, quietly hide the indicator - do NOT fill fake IP location
                    if (indicator) {
                        indicator.style.display = 'none';
                    }
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 }
            );
        }
    }

    function reverseGeocodeCoords(lat, lon) {
        // Query OpenStreetMap Nominatim with zoom 18 for high-precision address components
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}&addressdetails=1&zoom=18`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.address) {
                const a = data.address;
                const displayName = data.display_name || '';
                const tokens = displayName.split(',').map(s => s.trim()).filter(Boolean);

                // 1. PIN Code
                const realPincode = (a.postcode || '').replace(/\D/g, '').substring(0, 6) ||
                    (tokens.find(t => /^\d{6}$/.test(t)) || '');

                // 2. State
                const realState = a.state || '';

                // 3. City / District
                const realCity = a.city || a.town || a.village || a.city_district || a.state_district || a.county || '';

                // 4. House / Flat / Building (Address Line 1)
                let buildingOrHouse = '';
                if (a.house_number || a.building || a.house_name) {
                    buildingOrHouse = [a.house_number, a.house_name, a.building].filter(Boolean).join(', ');
                }

                // 5. Area / Colony / Street / Sector (Address Line 2)
                const areaParts = [];
                if (a.road) areaParts.push(a.road);
                if (a.neighbourhood && !areaParts.includes(a.neighbourhood)) areaParts.push(a.neighbourhood);
                if (a.suburb && !areaParts.includes(a.suburb)) areaParts.push(a.suburb);
                if (a.residential && !areaParts.includes(a.residential)) areaParts.push(a.residential);

                // Match sectors, colonies, nagars, vihars from full display_name tokens
                tokens.forEach(tok => {
                    if (/sector|colony|nagar|vihar|phase|block|enclave|marg|road|mohalla/i.test(tok)) {
                        if (!areaParts.some(p => p.toLowerCase() === tok.toLowerCase()) &&
                            tok.toLowerCase() !== realCity.toLowerCase() &&
                            tok.toLowerCase() !== realState.toLowerCase()) {
                            areaParts.push(tok);
                        }
                    }
                });

                let line1 = buildingOrHouse;
                let line2 = '';
                if (!line1) {
                    if (areaParts.length > 1) {
                        line1 = areaParts[0]; // Road / Street goes to Line 1
                        line2 = areaParts.slice(1, 4).join(', '); // Colony, Sector, Area goes to Line 2
                    } else if (areaParts.length === 1) {
                        line2 = areaParts[0];
                    }
                } else {
                    line2 = areaParts.slice(0, 3).join(', ');
                }

                // 6. Landmark (Only actual nearby POI like Metro station, Hospital, Mandir, School)
                let realLandmark = '';
                const poi = a.amenity || a.shop || a.office || a.place_of_worship || a.tourism || a.historic || a.hospital || a.school || a.railway;
                if (poi && !areaParts.some(p => p.toLowerCase().includes(poi.toLowerCase())) && poi.toLowerCase() !== line1.toLowerCase()) {
                    realLandmark = 'Near ' + poi;
                }

                applyLiveAddressFields({
                    address_line_1: line1,
                    address_line_2: line2,
                    landmark: realLandmark,
                    pincode: realPincode,
                    city: realCity,
                    state: realState
                });
            } else {
                fallbackBigDataCloudDirect(lat, lon);
            }
        })
        .catch(() => {
            fallbackBigDataCloudDirect(lat, lon);
        });
    }

    function fallbackBigDataCloudDirect(lat, lon) {
        fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=en`)
            .then(res => res.json())
            .then(data => {
                const realCity = data.city || data.locality || '';
                const realState = data.principalSubdivision || '';
                const realPincode = data.postcode ? data.postcode.replace(/\D/g, '').substring(0, 6) : '';
                const realArea = data.locality || '';

                applyLiveAddressFields({
                    address_line_1: realArea,
                    address_line_2: data.principalSubdivision ? realArea + ', ' + realCity : '',
                    landmark: '',
                    pincode: realPincode,
                    city: realCity,
                    state: realState
                });
            })
            .catch(() => {
                const indicator = document.getElementById('liveLocationIndicator');
                if (indicator) indicator.style.display = 'none';
            });
    }

    function applyLiveAddressFields(info) {
        const addr1 = document.getElementById('address_line_1');
        const addr2 = document.getElementById('address_line_2');
        const lmark = document.getElementById('landmark');
        const pin = document.getElementById('pincode');
        const city = document.getElementById('city');
        const state = document.getElementById('state');

        if (info.address_line_1 && addr1) {
            addr1.value = info.address_line_1;
        }
        if (info.address_line_2 && addr2) {
            addr2.value = info.address_line_2;
        }
        if (info.landmark && lmark) {
            lmark.value = info.landmark;
        }
        if (info.pincode && pin) {
            pin.value = info.pincode;
        }
        if (info.city && city) {
            city.value = info.city;
        }
        if (info.state && state) {
            state.value = info.state;
        }

        const indicator = document.getElementById('liveLocationIndicator');
        const cityText = document.getElementById('liveLocationCityText');
        if (indicator && cityText && info.city) {
            cityText.innerText = info.city + (info.state ? ', ' + info.state : '') + ' (Live GPS)';
            indicator.style.display = 'inline-block';
        }

        saveGuestAddressToStorage();
    }

    // Save Guest Address to localStorage
    function saveGuestAddressToStorage() {
        const guestData = {
            name: document.getElementById('cust_name')?.value || '',
            mobile: document.getElementById('cust_mobile')?.value || '',
            email: document.getElementById('cust_email')?.value || '',
            address_line_1: document.getElementById('address_line_1')?.value || '',
            address_line_2: document.getElementById('address_line_2')?.value || '',
            landmark: document.getElementById('landmark')?.value || '',
            pincode: document.getElementById('pincode')?.value || '',
            city: document.getElementById('city')?.value || '',
            state: document.getElementById('state')?.value || ''
        };
        try {
            localStorage.setItem('autologos_guest_shipping', JSON.stringify(guestData));
        } catch (e) {}
    }

    // Auto-load Guest Address from localStorage on load if unauthenticated/empty
    function loadGuestAddressFromStorage() {
        const addr1 = document.getElementById('address_line_1');
        if (!addr1 || addr1.value.trim() !== '') return false;

        try {
            const raw = localStorage.getItem('autologos_guest_shipping');
            if (raw) {
                const data = JSON.parse(raw);
                if (data && (data.address_line_1 || data.pincode || data.city)) {
                    if (data.name && document.getElementById('cust_name') && !document.getElementById('cust_name').value) {
                        document.getElementById('cust_name').value = data.name;
                    }
                    if (data.mobile && document.getElementById('cust_mobile') && !document.getElementById('cust_mobile').value) {
                        document.getElementById('cust_mobile').value = data.mobile;
                    }
                    if (data.email && document.getElementById('cust_email') && !document.getElementById('cust_email').value) {
                        document.getElementById('cust_email').value = data.email;
                    }
                    if (data.address_line_1) document.getElementById('address_line_1').value = data.address_line_1;
                    if (data.address_line_2) document.getElementById('address_line_2').value = data.address_line_2;
                    if (data.landmark) document.getElementById('landmark').value = data.landmark;
                    if (data.pincode) document.getElementById('pincode').value = data.pincode;
                    if (data.city) document.getElementById('city').value = data.city;
                    if (data.state) document.getElementById('state').value = data.state;

                    const indicator = document.getElementById('liveLocationIndicator');
                    const cityText = document.getElementById('liveLocationCityText');
                    if (indicator && cityText && data.city) {
                        cityText.innerText = data.city + (data.state ? ', ' + data.state : '') + ' (Saved)';
                        indicator.style.display = 'inline-block';
                    }
                    return true;
                }
            }
        } catch (e) {}
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Automatically detect and populate live address directly on page load
        autoDetectLiveAddressDirectly();

        // Listen for input changes to persist guest address
        const fields = ['cust_name', 'cust_mobile', 'cust_email', 'address_line_1', 'address_line_2', 'landmark', 'pincode', 'city', 'state'];
        fields.forEach(fid => {
            const el = document.getElementById(fid);
            if (el) {
                el.addEventListener('change', saveGuestAddressToStorage);
            }
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
