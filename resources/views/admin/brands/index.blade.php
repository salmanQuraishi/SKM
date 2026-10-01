@extends('layouts.admin')

@section('title', 'Car Brands - AutoLogos')
@section('page_title', 'Car Brands Management')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Automobile Brands</h5>
            <p class="text-muted small mb-0">Manage vehicle brands, logos, models and catalog counts.</p>
        </div>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-theme-red px-3">
            <i class="fa-solid fa-plus me-1"></i> Add New Brand
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 70px;">Logo</th>
                        <th>Brand Name</th>
                        <th>Models Count</th>
                        <th>Products Count</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                        <tr>
                            <td class="ps-4">
                                <div class="rounded bg-light border p-1 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                    @if($brand->logo)
                                        <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" class="img-fluid object-fit-contain h-100">
                                    @else
                                        <i class="fa-solid fa-copyright text-danger"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $brand->name }}</strong>
                                <small class="text-muted">Slug: <code>{{ $brand->slug }}</code></small>
                            </td>
                            <td>
                                <a href="{{ route('admin.models.index', ['brand_id' => $brand->id]) }}" class="badge bg-light text-dark border text-decoration-none">
                                    {{ $brand->car_models_count }} models
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.index', ['brand_id' => $brand->id]) }}" class="badge bg-light text-dark border text-decoration-none">
                                    {{ $brand->products_count }} products
                                </a>
                            </td>
                            <td>
                                @if($brand->is_featured)
                                    <span class="badge bg-warning-subtle text-warning-emphasis"><i class="fa-solid fa-star me-1"></i> Featured</span>
                                @else
                                    <span class="text-muted small">Standard</span>
                                @endif
                            </td>
                            <td>
                                @if($brand->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-light border text-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete brand {{ $brand->name }}?')">
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
                            <td colspan="7" class="text-center py-5 text-muted">No brands registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($brands->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $brands->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
