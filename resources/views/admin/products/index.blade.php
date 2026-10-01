@extends('layouts.admin')

@section('title', 'Products Management - AutoLogos')
@section('page_title', 'Product Catalog & Inventory')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Catalog Products</h5>
            <p class="text-muted small mb-0">Manage vehicle logos, emblems, monogram lettering, stock levels and pricing.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-theme-red px-3">
                <i class="fa-solid fa-plus me-1"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Search by name, SKU or model...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="brand_id" id="filterBrandSelect" class="form-select">
                        <option value="">All Companies</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="car_model_id" id="filterModelSelect" class="form-select">
                        <option value="">All Car Models</option>
                        @foreach($models as $m)
                            <option value="{{ $m->id }}" {{ request('car_model_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="variant_id" id="filterVariantSelect" class="form-select">
                        <option value="">All Variants</option>
                        @foreach($variants as $v)
                            <option value="{{ $v->id }}" {{ request('variant_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="stock_status" class="form-select">
                        <option value="">All Stock Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock (>0)</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock (≤5)</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock (0)</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-dark w-100" title="Filter"><i class="fa-solid fa-filter"></i></button>
                    @if(request()->hasAny(['q', 'brand_id', 'car_model_id', 'variant_id', 'category_id', 'stock_status']))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table Card -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <form action="{{ route('admin.products.bulk') }}" method="POST" id="bulkForm" class="d-flex align-items-center gap-2">
                @csrf
                <select name="action" class="form-select form-select-sm" style="width: 170px;" required>
                    <option value="">Bulk Actions...</option>
                    <option value="activate">Set Active</option>
                    <option value="deactivate">Set Inactive</option>
                    <option value="delete">Delete Selected</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-secondary">Apply</button>
            </form>
            <div class="text-muted small">
                Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} items
            </div>
        </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="ps-3">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th style="width: 60px;">Image</th>
                            <th>Product Details</th>
                            <th>Vehicle Hierarchy</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="ps-3">
                                    <input type="checkbox" name="selected_ids[]" value="{{ $product->id }}" class="form-check-input select-item" form="bulkForm">
                                </td>
                                <td>
                                    <div class="rounded border bg-light p-1 text-center" style="width: 48px; height: 48px;">
                                        <img src="{{ asset($product->primary_image_url) }}" alt="{{ $product->name }}" class="img-fluid h-100 object-fit-contain" onerror="this.onerror=null;this.src='{{ asset('images/placeholder-emblem.svg') }}';">
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                        {{ $product->name }}
                                    </a>
                                    <div class="text-muted small">
                                        SKU: <code>{{ $product->sku }}</code>
                                        @if($product->is_featured)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 9px;">FEATURED</span>
                                        @endif
                                        @if($product->is_best_seller)
                                            <span class="badge bg-danger text-white ms-1" style="font-size: 9px;">BEST SELLER</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark font-sm">{{ $product->brand->name ?? 'Universal' }}</div>
                                    @if($product->carModel)
                                        <div class="small text-secondary"><i class="fa-solid fa-angle-right text-muted me-1"></i>{{ $product->carModel->name }}</div>
                                    @endif
                                    @if($product->modelVariant)
                                        <div class="small text-primary fw-semibold"><i class="fa-solid fa-code-fork text-muted me-1"></i>{{ $product->modelVariant->name }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $product->primaryCategory->name ?? 'General' }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">&#x20B9;{{ number_format($product->final_price, 2) }}</div>
                                    @if($product->sale_price && $product->sale_price < $product->regular_price)
                                        <del class="text-muted small">&#x20B9;{{ number_format($product->regular_price, 2) }}</del>
                                    @endif
                                </td>
                                <td>
                                    @if($product->stock_quantity <= 0)
                                        <span class="badge bg-danger">0 Out of Stock</span>
                                    @elseif($product->stock_quantity <= $product->min_stock_alert)
                                        <span class="badge bg-warning text-dark">{{ $product->stock_quantity }} Low Stock</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $product->stock_quantity }} in stock</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->status)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted">Draft</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.detail', $product->slug) }}" target="_blank" class="btn btn-light border" title="Preview Live">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-secondary"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-light border text-primary" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('admin.products.duplicate', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-light border text-success" title="Duplicate">
                                                <i class="fa-solid fa-copy"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline confirm-action" data-confirm="Are you sure you want to delete product '{{ $product->name }}'?" data-confirm-title="Delete Product?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light border text-danger" title="Delete">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fs-2 mb-2 d-block text-secondary"></i>
                                    No products found matching your filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('bulkForm')?.addEventListener('submit', function (e) {
        if (this.dataset.confirmed === 'true') {
            return;
        }
        const checked = document.querySelectorAll('.select-item:checked');
        if (checked.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'Please select at least one product using the checkboxes.',
                confirmButtonColor: '#d90429'
            });
            return false;
        }
        const act = this.querySelector('select[name="action"]').value;
        if (act === 'delete') {
            e.preventDefault();
            Swal.fire({
                title: 'Delete Selected Products?',
                text: `Are you sure you want to delete ${checked.length} selected product(s)? This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d90429',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete them!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn-swal-confirm',
                    cancelButton: 'btn-swal-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.dataset.confirmed = 'true';
                    this.submit();
                }
            });
            return false;
        }
    });

    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.select-item').forEach(cb => cb.checked = this.checked);
    });

    const fBrand = document.getElementById('filterBrandSelect');
    const fModel = document.getElementById('filterModelSelect');
    const fVariant = document.getElementById('filterVariantSelect');

    if (fBrand && fModel && fVariant) {
        fBrand.addEventListener('change', function() {
            const brandId = this.value;
            fModel.innerHTML = '<option value="">All Car Models</option>';
            fVariant.innerHTML = '<option value="">All Variants</option>';
            if (!brandId) return;

            fetch('/ajax/models-by-brand/' + brandId)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        data.data.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            opt.textContent = m.name;
                            fModel.appendChild(opt);
                        });
                    }
                });
        });

        fModel.addEventListener('change', function() {
            const modelId = this.value;
            fVariant.innerHTML = '<option value="">All Variants</option>';
            if (!modelId) return;

            fetch('/ajax/variants-by-model/' + modelId)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        data.data.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.id;
                            opt.textContent = v.name;
                            fVariant.appendChild(opt);
                        });
                    }
                });
        });
    }
</script>
@endpush
