@extends('layouts.admin')

@section('title', 'Car Variants - AutoLogos')
@section('page_title', 'Car Variants Management')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Car Variants</h5>
            <p class="text-muted small mb-0">Manage variants under each car model (e.g. Punch Pure, Nexon Creative, Thar AX).</p>
        </div>
        <a href="{{ route('admin.variants.create', ['brand_id' => request('brand_id'), 'car_model_id' => request('car_model_id')]) }}" class="btn btn-theme-red px-3">
            <i class="fa-solid fa-plus me-1"></i> Add New Variant
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.variants.index') }}" method="GET" class="row g-2 align-items-center" id="variantFilterForm">
                <div class="col-md-3">
                    <select name="brand_id" id="filterBrandSelect" class="form-select">
                        <option value="">All Companies / Brands</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="car_model_id" id="filterModelSelect" class="form-select">
                        <option value="">All Car Models</option>
                        @foreach($models as $m)
                            <option value="{{ $m->id }}" {{ request('car_model_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="q" class="form-control" placeholder="Search variant name..." value="{{ request('q') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                    @if(request()->hasAny(['brand_id', 'car_model_id', 'q']))
                        <a href="{{ route('admin.variants.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Variants Table Card -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 60px;">#</th>
                        <th>Variant Name</th>
                        <th>Company / Brand</th>
                        <th>Car Model</th>
                        <th>Products Linked</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $v)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $v->id }}</td>
                            <td>
                                <strong class="text-dark d-block">{{ $v->name }}</strong>
                                <small class="text-muted">Slug: <code>{{ $v->slug }}</code></small>
                            </td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger fw-semibold">
                                    {{ $v->carModel?->brand?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $v->carModel?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.index', ['variant_id' => $v->id]) }}" class="badge bg-light text-dark border text-decoration-none">
                                    {{ $v->products_count }} products
                                </a>
                            </td>
                            <td>
                                @if($v->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.variants.edit', $v->id) }}" class="btn btn-light border text-primary" title="Edit Variant">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.variants.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete variant {{ $v->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Delete Variant">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-car-tunnel display-6 d-block mb-2 text-secondary"></i>
                                No variants found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($variants->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $variants->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandSelect = document.getElementById('filterBrandSelect');
    const modelSelect = document.getElementById('filterModelSelect');

    if (brandSelect && modelSelect) {
        brandSelect.addEventListener('change', function() {
            const brandId = this.value;
            modelSelect.innerHTML = '<option value="">Loading models...</option>';
            if (!brandId) {
                modelSelect.innerHTML = '<option value="">All Car Models</option>';
                return;
            }

            fetch('/ajax/models-by-brand/' + brandId)
                .then(res => res.json())
                .then(data => {
                    modelSelect.innerHTML = '<option value="">All Car Models</option>';
                    if (data.success && data.data) {
                        data.data.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.id;
                            opt.textContent = m.name;
                            modelSelect.appendChild(opt);
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching models:', err);
                    modelSelect.innerHTML = '<option value="">All Car Models</option>';
                });
        });
    }
});
</script>
@endpush
@endsection
