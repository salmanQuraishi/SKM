@extends('layouts.admin')

@section('title', 'Categories - AutoLogos')
@section('page_title', 'Category Management')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Product Categories</h5>
            <p class="text-muted small mb-0">Organize emblems, monograms, letters and accessories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-theme-red px-3">
            <i class="fa-solid fa-plus me-1"></i> Add New Category
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 70px;">Icon</th>
                        <th>Category Name</th>
                        <th>Parent Category</th>
                        <th>Products Count</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td class="ps-4">
                                <div class="rounded bg-light border d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                    @if($cat->image)
                                        <img src="{{ asset($cat->image) }}" alt="{{ $cat->name }}" class="img-fluid p-1">
                                    @else
                                        <i class="{{ $cat->icon ?: 'fa-solid fa-folder' }} text-danger"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $cat->name }}</strong>
                                <small class="text-muted">Slug: <code>{{ $cat->slug }}</code></small>
                            </td>
                            <td>
                                @if($cat->parent)
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $cat->parent->name }}</span>
                                @else
                                    <span class="text-muted small">None (Root)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $cat->products_count }} products</span>
                            </td>
                            <td>{{ $cat->sort_order }}</td>
                            <td>
                                @if($cat->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-light border text-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="d-inline confirm-action" data-confirm="Are you sure you want to delete category '{{ $cat->name }}'?" data-confirm-title="Delete Category?">
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
                            <td colspan="7" class="text-center py-5 text-muted">No categories created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
