@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name . ' - AutoLogos')
@section('meta_description', $product->meta_description ?: $product->short_description)

@section('content')
<div class="bg-light py-3 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                @if($product->modelVariant && $product->modelVariant->carModel && $product->modelVariant->carModel->brand)
                    <li class="breadcrumb-item"><a href="{{ route('vehicles') }}" class="text-decoration-none text-muted">Vehicles</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicle.cars', $product->modelVariant->carModel->brand->slug) }}" class="text-decoration-none text-muted">{{ $product->modelVariant->carModel->brand->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicle.variants', [$product->modelVariant->carModel->brand->slug, $product->modelVariant->carModel->slug]) }}" class="text-decoration-none text-muted">{{ $product->modelVariant->carModel->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicle.products', [$product->modelVariant->carModel->brand->slug, $product->modelVariant->carModel->slug, $product->modelVariant->slug]) }}" class="text-decoration-none text-muted">{{ $product->modelVariant->name }}</a></li>
                @elseif($product->carModel && $product->carModel->brand)
                    <li class="breadcrumb-item"><a href="{{ route('vehicles') }}" class="text-decoration-none text-muted">Vehicles</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicle.cars', $product->carModel->brand->slug) }}" class="text-decoration-none text-muted">{{ $product->carModel->brand->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicle.variants', [$product->carModel->brand->slug, $product->carModel->slug]) }}" class="text-decoration-none text-muted">{{ $product->carModel->name }}</a></li>
                @elseif($product->brand)
                    <li class="breadcrumb-item"><a href="{{ route('vehicles') }}" class="text-decoration-none text-muted">Vehicles</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicle.cars', $product->brand->slug) }}" class="text-decoration-none text-muted">{{ $product->brand->name }}</a></li>
                @elseif($product->primaryCategory)
                    <li class="breadcrumb-item"><a href="{{ route('shop', ['category' => $product->primaryCategory->slug]) }}" class="text-decoration-none text-muted">{{ $product->primaryCategory->name }}</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('shop') }}" class="text-decoration-none text-muted">Shop</a></li>
                @endif
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Product Gallery -->
        <div class="col-lg-5">
            <div class="card border shadow-sm rounded-3 p-3 bg-white sticky-top" style="top: 20px;">
                <div class="text-center p-4 bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center" style="min-height: 340px;">
                    <img id="mainProductImage" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="img-fluid" style="max-height: 300px; object-fit: contain; transition: transform 0.3s;" onerror="this.onerror=null;this.src='{{ asset('images/placeholder-emblem.svg') }}';">
                </div>

                @if($product->images->count() > 1)
                    <div class="d-flex gap-2 justify-content-center overflow-x-auto pb-2">
                        @foreach($product->images as $img)
                            <div class="border rounded-2 p-1 cursor-pointer thumb-box {{ $loop->first ? 'border-danger' : '' }}" onclick="changeMainImage('{{ $img->url }}', this)" style="width: 60px; height: 60px;">
                                <img src="{{ $img->url }}" class="w-100 h-100 object-fit-contain" alt="Thumbnail">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="row text-center mt-3 pt-3 border-top g-2 text-muted font-sm">
                    <div class="col-4"><i class="fa-solid fa-shield-halved text-danger me-1"></i> 100% Genuine</div>
                    <div class="col-4"><i class="fa-solid fa-rotate-left text-danger me-1"></i> 7 Days Return</div>
                    <div class="col-4"><i class="fa-solid fa-truck-fast text-danger me-1"></i> Fast Shipping</div>
                </div>
            </div>
        </div>

        <!-- Product Details & Buy Box -->
        <div class="col-lg-7">
            <div class="card border-0 bg-transparent">
                <!-- Brand & Model -->
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if($product->brand)
                        <span class="badge bg-dark text-uppercase px-2 py-1">{{ $product->brand->name }}</span>
                    @endif
                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">SKU: {{ $product->sku }}</span>
                    @if($product->is_featured)
                        <span class="badge bg-danger px-2 py-1">FEATURED</span>
                    @endif
                </div>

                <!-- Title -->
                <h1 class="fw-bold brand-font text-dark mb-2" style="font-size: 26px;">{{ $product->name }}</h1>

                @if($product->modelVariant && $product->modelVariant->carModel && $product->modelVariant->carModel->brand)
                    <div class="alert alert-light border border-danger-subtle d-flex align-items-center justify-content-between py-2 px-3 mb-3 rounded-3 shadow-sm bg-white">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-car-side text-danger fs-4"></i>
                            <div>
                                <div class="text-secondary small" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tailored Vehicle Fitment</div>
                                <div class="fw-bold text-dark font-sm">
                                    {{ $product->modelVariant->carModel->brand->name }} {{ $product->modelVariant->carModel->name }} &ndash; <span class="text-danger">{{ $product->modelVariant->name }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('vehicle.products', [$product->modelVariant->carModel->brand->slug, $product->modelVariant->carModel->slug, $product->modelVariant->slug]) }}" class="btn btn-outline-danger btn-sm" style="font-size: 11px;">
                            View All for this Variant <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                @elseif($product->carModel && $product->carModel->brand)
                    <div class="alert alert-light border d-flex align-items-center justify-content-between py-2 px-3 mb-3 rounded-3 shadow-sm bg-white">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-car-side text-danger fs-4"></i>
                            <div>
                                <div class="text-secondary small" style="font-size: 11px; text-transform: uppercase;">Vehicle Fitment</div>
                                <div class="fw-bold text-dark font-sm">
                                    {{ $product->carModel->brand->name }} {{ $product->carModel->name }}
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('vehicle.variants', [$product->carModel->brand->slug, $product->carModel->slug]) }}" class="btn btn-outline-secondary btn-sm" style="font-size: 11px;">
                            Select Variant <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                @else
                    <p class="text-muted font-sm mb-3">Compatible with: <strong class="text-dark">{{ $product->compatible_models_text ?: 'All Models' }}</strong></p>
                @endif

                <!-- Rating -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="text-warning small">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-{{ $i <= round($product->average_rating) ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor
                    </div>
                    <span class="fw-bold text-dark font-sm">{{ $product->average_rating }}</span>
                    <span class="text-muted font-sm">({{ $product->reviews->count() }} customer reviews)</span>
                </div>

                <!-- Pricing -->
                <div class="p-3 bg-white border rounded-3 mb-4">
                    <div class="d-flex align-items-baseline gap-3">
                        <span class="display-6 fw-bold text-danger" id="displayPrice">&#x20B9;{{ number_format($product->active_price, 0) }}</span>
                        @if($product->has_discount)
                            <span class="text-muted text-decoration-line-through fs-5" id="displayRegularPrice">&#x20B9;{{ number_format($product->regular_price, 0) }}</span>
                            <span class="badge bg-success fs-6" id="displayDiscountBadge">{{ $product->discount_percentage }}% OFF</span>
                        @endif
                    </div>
                    <div class="text-success small mt-1"><i class="fa-solid fa-circle-check me-1"></i> Inclusive of all taxes (GST)</div>
                </div>

                <!-- Stock Status -->
                <div class="mb-3">
                    @if($product->in_stock)
                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fs-6">
                            <i class="fa-solid fa-circle-check me-1"></i> In Stock ({{ $product->stock_quantity }} units available)
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 fs-6">
                            <i class="fa-solid fa-circle-xmark me-1"></i> Out of Stock
                        </span>
                    @endif
                </div>

                <form action="{{ route('cart.add') }}" method="POST" id="productForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <!-- Variant Selection (if available) -->
                    @if($product->variants->isNotEmpty())
                        <div class="mb-4">
                            <label class="fw-bold text-dark font-sm text-uppercase mb-2 d-block">Select Variant / Finish:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($product->variants as $var)
                                    <label class="btn btn-outline-dark font-sm px-3 py-2 rounded-2 cursor-pointer variant-option-btn {{ $loop->first ? 'active' : '' }}">
                                        <input type="radio" name="variant_id" value="{{ $var->id }}" class="d-none variant-radio" data-price="&#x20B9;{{ number_format($var->active_price, 0) }}" data-regprice="&#x20B9;{{ number_format($var->regular_price, 0) }}" {{ $loop->first ? 'checked' : '' }} onchange="selectVariant(this)">
                                        {{ $var->name }} - &#x20B9;{{ number_format($var->active_price, 0) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quantity & Action Buttons -->
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="decrementQty()">-</button>
                            <input type="number" name="quantity" id="productQty" class="form-control text-center fw-bold" value="1" min="1" max="{{ $product->stock_quantity }}">
                            <button class="btn btn-outline-secondary" type="button" onclick="incrementQty()">+</button>
                        </div>

                        <button type="submit" class="btn btn-theme-red px-4 py-3 fw-bold d-flex align-items-center gap-2 flex-grow-1 justify-content-center" {{ !$product->in_stock ? 'disabled' : '' }}>
                            <i class="fa-solid fa-cart-shopping"></i> ADD TO CART
                        </button>

                        <button type="submit" name="buy_now" value="1" class="btn btn-dark px-4 py-3 fw-bold flex-grow-1" {{ !$product->in_stock ? 'disabled' : '' }}>
                            BUY NOW
                        </button>

                        <button type="button" onclick="toggleWishlist({{ $product->id }}, this)" class="btn btn-outline-secondary p-3" title="Add to Wishlist">
                            <i class="{{ in_array($product->id, $globalWishlistProductIds ?? []) ? 'fa-solid fa-heart text-danger' : 'fa-regular fa-heart' }} fs-5"></i>
                        </button>
                    </div>
                </form>

                <!-- Pincode Delivery Estimator -->
                <div class="card border p-3 rounded-3 bg-white mb-4">
                    <label class="fw-bold font-sm mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> Delivery & COD Check</label>
                    <div class="input-group">
                        <input type="text" id="pincodeInput" class="form-control form-control-sm" placeholder="Enter 6-digit Pincode" maxlength="6">
                        <button class="btn btn-danger btn-sm px-3" type="button" onclick="checkPincode()">Check</button>
                    </div>
                    <div id="pincodeResult" class="mt-2 small font-sm" style="display: none;"></div>
                </div>

                <!-- Short Description -->
                <div class="text-secondary mb-4 leading-relaxed font-sm">
                    {{ $product->short_description }}
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs (Description, Specs, Reviews) -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 bg-white shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom p-0">
                    <ul class="nav nav-tabs border-0" id="productTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold py-3 px-4 text-uppercase font-sm border-0 border-bottom border-danger border-3" id="desc-tab" data-bs-toggle="tab" data-bs-target="#descContent">Description</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold py-3 px-4 text-uppercase font-sm border-0" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specsContent">Specifications</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold py-3 px-4 text-uppercase font-sm border-0" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviewsContent">Reviews ({{ $product->reviews->count() }})</button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="productTabsContent">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="descContent">
                            <div class="product-description-html leading-relaxed">
                                {!! $product->full_description ?: '<p>' . e($product->short_description) . '</p>' !!}
                            </div>
                        </div>

                        <!-- Specifications Tab -->
                        <div class="tab-pane fade" id="specsContent">
                            <table class="table table-bordered table-striped font-sm mb-0">
                                <tbody>
                                    <tr><th style="width: 250px;">Placement</th><td>{{ $product->placement ?: 'Universal Fit' }}</td></tr>
                                    <tr><th>Finish / Color</th><td>{{ $product->finish ?: 'Triple Plated Mirror Chrome' }}</td></tr>
                                    <tr><th>Compatible Models</th><td>{{ $product->compatible_models_text ?: 'All Models' }}</td></tr>
                                    <tr><th>HSN / SAC Code</th><td>{{ $product->hsn_sac ?: '8708' }}</td></tr>
                                    <tr><th>GST Tax Rate</th><td>{{ $product->tax_rate }}% (Included in Price)</td></tr>
                                    @if(is_array($product->specifications))
                                        @foreach($product->specifications as $k => $v)
                                            <tr><th>{{ $k }}</th><td>{{ $v }}</td></tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="reviewsContent">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h5 class="fw-bold mb-3">Customer Reviews</h5>
                                    @if($product->reviews->isEmpty())
                                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                                    @else
                                        <div class="d-flex flex-column gap-3">
                                            @foreach($product->reviews as $rev)
                                                <div class="border-bottom pb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold text-dark">{{ $rev->customer_name }}</span>
                                                        <span class="text-muted small">{{ $rev->created_at->format('d M, Y') }}</span>
                                                    </div>
                                                    <div class="text-warning small mb-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fa-{{ $i <= $rev->rating ? 'solid' : 'regular' }} fa-star"></i>
                                                        @endfor
                                                        @if($rev->is_verified_purchase)
                                                            <span class="badge bg-success-subtle text-success ms-2 font-sm"><i class="fa-solid fa-circle-check"></i> Verified Buyer</span>
                                                        @endif
                                                    </div>
                                                    @if($rev->title)
                                                        <div class="fw-semibold small text-dark">{{ $rev->title }}</div>
                                                    @endif
                                                    <p class="text-secondary small mb-0">{{ $rev->comment }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Submit Review Form -->
                                <div class="col-lg-6">
                                    <div class="p-3 bg-light rounded-3">
                                        <h5 class="fw-bold mb-3">Write a Review</h5>
                                        <form action="{{ route('review.store', $product->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label font-sm fw-bold">Your Rating *</label>
                                                <select name="rating" class="form-select form-select-sm" required>
                                                    <option value="5">★★★★★ (5/5 Stars - Excellent)</option>
                                                    <option value="4">★★★★☆ (4/5 Stars - Good)</option>
                                                    <option value="3">★★★☆☆ (3/5 Stars - Average)</option>
                                                    <option value="2">★★☆☆☆ (2/5 Stars - Poor)</option>
                                                    <option value="1">★☆☆☆☆ (1/5 Stars - Very Poor)</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label font-sm fw-bold">Your Name *</label>
                                                <input type="text" name="customer_name" class="form-control form-control-sm" value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label font-sm fw-bold">Review Title</label>
                                                <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Perfect OEM fit!">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label font-sm fw-bold">Your Feedback *</label>
                                                <textarea name="comment" rows="3" class="form-control form-control-sm" placeholder="Tell us about the quality, installation, and finish..." required></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-theme-red btn-sm px-4">Submit Review</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5">
            <h4 class="fw-bold brand-font text-uppercase mb-4">RELATED <span class="text-danger">PRODUCTS</span></h4>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
                @foreach($relatedProducts as $rel)
                    <div class="col">
                        <div class="product-card">
                            <div class="img-wrapper">
                                <a href="{{ route('product.detail', $rel->slug) }}">
                                    <img src="{{ $rel->primary_image_url }}" alt="{{ $rel->name }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder-emblem.svg') }}';">
                                </a>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('product.detail', $rel->slug) }}" class="product-title">{{ $rel->name }}</a>
                                <div class="product-subtitle">{{ $rel->compatible_models_text ?: 'All Models' }}</div>
                                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="price-tag">&#x20B9;{{ number_format($rel->active_price, 0) }}</span>
                                    <a href="{{ route('product.detail', $rel->slug) }}" class="btn btn-outline-danger btn-sm">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function changeMainImage(src, elem) {
        document.getElementById('mainProductImage').src = src;
        document.querySelectorAll('.thumb-box').forEach(el => el.classList.remove('border-danger'));
        elem.classList.add('border-danger');
    }

    function incrementQty() {
        const input = document.getElementById('productQty');
        const max = parseInt(input.getAttribute('max')) || 99;
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function decrementQty() {
        const input = document.getElementById('productQty');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function selectVariant(radio) {
        document.querySelectorAll('.variant-option-btn').forEach(b => b.classList.remove('active'));
        radio.parentElement.classList.add('active');

        const price = radio.getAttribute('data-price');
        if (price) {
            document.getElementById('displayPrice').innerText = price;
        }
    }

    function checkPincode() {
        const pin = document.getElementById('pincodeInput').value.trim();
        const resultDiv = document.getElementById('pincodeResult');

        if (pin.length !== 6) {
            resultDiv.className = 'mt-2 text-danger small';
            resultDiv.innerText = 'Please enter a valid 6-digit PIN code.';
            resultDiv.style.display = 'block';
            return;
        }

        fetch(`{{ route('pincode.check') }}?pincode=${pin}`)
            .then(res => res.json())
            .then(data => {
                resultDiv.style.display = 'block';
                if (data.success) {
                    resultDiv.className = 'mt-2 text-success small fw-semibold';
                    resultDiv.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i> ${data.message}`;
                } else {
                    resultDiv.className = 'mt-2 text-danger small';
                    resultDiv.innerText = data.message;
                }
            })
            .catch(err => console.error(err));
    }
</script>
@endpush
