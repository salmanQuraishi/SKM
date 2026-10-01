@extends('layouts.admin')

@section('title', 'Car Models - AutoLogos')
@section('page_title', 'Car Models Catalog')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Car Models</h5>
            <p class="text-muted small mb-0">Manage vehicle models (i20, Creta, Swift, Nexon, Fortuner, etc.).</p>
        </div>
        <a href="{{ route('admin.models.create') }}" class="btn btn-theme-red px-3">
            <i class="fa-solid fa-plus me-1"></i> Add Car Model
        </a>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.models.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="brand_id" class="form-select">
                        <option value="">All Brands</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-dark w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                    @if(request('brand_id'))
                        <a href="{{ route('admin.models.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 70px;">Badge</th>
                        <th>Model Name</th>
                        <th>Brand</th>
                        <th>Variants Count</th>
                        <th>Products Linked</th>
                        <th>Popular</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($models as $m)
                        <tr>
                            <td class="ps-4">
                                <div class="rounded bg-light border p-1 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    @if($m->badge_image)
                                        <img src="{{ asset($m->badge_image) }}" alt="{{ $m->name }}" class="img-fluid object-fit-contain h-100">
                                    @else
                                        <i class="fa-solid fa-car-side text-secondary"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $m->name }}</strong>
                                <small class="text-muted">Slug: <code>{{ $m->slug }}</code></small>
                            </td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger fw-semibold">{{ $m->brand->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.variants.index', ['brand_id' => $m->brand_id, 'car_model_id' => $m->id]) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none">
                                    <i class="fa-solid fa-code-fork me-1"></i> {{ $m->variants_count }} variants
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.index', ['car_model_id' => $m->id]) }}" class="badge bg-light text-dark border text-decoration-none">
                                    {{ $m->products_count }} products
                                </a>
                            </td>
                            <td>
                                @if($m->is_popular)
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Popular</span>
                                @else
                                    <span class="text-muted small">Standard</span>
                                @endif
                            </td>
                            <td>
                                @if($m->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.models.edit', $m->id) }}" class="btn btn-light border text-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.models.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete model {{ $m->name }}?')">
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
                            <td colspan="7" class="text-center py-5 text-muted">No car models found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($models->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $models->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
