@extends('layouts.app')

@section('title', 'My Wishlist - AutoLogos')

@section('content')
<!-- Breadcrumbs -->
<div class="py-3 border-bottom mb-4" style="background: #090d16; border-color: #1e293b !important;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 font-sm">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active text-white fw-semibold" aria-current="page">Wishlist</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <h2 class="fw-bold brand-font text-uppercase mb-4 text-white">
        MY <span class="text-danger">WISHLIST</span> 
        @if($items->isNotEmpty())
            <span class="badge bg-secondary font-sm fw-semibold fs-6 align-middle ms-2">{{ $items->total() }} {{ \Illuminate\Support\Str::plural('item', $items->total()) }}</span>
        @endif
    </h2>

    @if($items->isEmpty())
        <div class="card border p-5 text-center my-4" style="background: #111827; border-color: #1e293b !important;">
            <i class="fa-regular fa-heart text-secondary display-3 mb-3"></i>
            <h4 class="fw-bold text-white">Your Wishlist is Empty</h4>
            <p class="text-secondary">Save your favorite car emblems and monograms here for later.</p>
            <div class="mt-3">
                <a href="{{ route('shop') }}" class="btn btn-theme-red px-4 py-2">
                    <i class="fa-solid fa-bag-shopping me-2"></i> Explore Emblems
                </a>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4">
            @foreach($items as $item)
                @php $prod = $item->product; @endphp
                <div class="col">
                    <div class="product-card">
                        <div class="img-wrapper">
                            <a href="{{ route('product.detail', $prod->slug) }}">
                                <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" onerror="this.onerror=null;this.src='{{ asset('images/dummy-product.svg') }}';">
                            </a>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('product.detail', $prod->slug) }}" class="product-title">{{ $prod->name }}</a>
                            <div class="product-subtitle">{{ $prod->compatible_models_text ?: 'All Models' }}</div>

                            <div class="mt-auto pt-2 border-top">
                                <div class="d-flex align-items-baseline mb-2">
                                    <span class="price-tag">&#x20B9;{{ number_format($prod->active_price, 0) }}</span>
                                    @if($prod->has_discount)
                                        <span class="old-price">&#x20B9;{{ number_format($prod->regular_price, 0) }}</span>
                                    @endif
                                </div>

                                <div class="d-flex gap-2">
                                    <form action="{{ route('wishlist.move_to_cart', $prod->id) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-theme-red w-100 py-2 font-sm d-flex align-items-center justify-content-center gap-1">
                                            <i class="fa-solid fa-cart-plus"></i> <span>MOVE TO CART</span>
                                        </button>
                                    </form>
                                    <button type="button" onclick="toggleWishlist({{ $prod->id }}, this); location.reload();" class="btn btn-outline-danger px-3 py-2" title="Remove">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center">
            {{ $items->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
