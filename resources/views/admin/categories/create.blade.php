@extends('layouts.admin')

@section('title', 'Add Category - AutoLogos')
@section('page_title', 'Create New Category')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Add Category</h5>
            <p class="text-muted small mb-0">Create a new category for products.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 bg-white" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g., Chrome Monograms" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">None (Top Level Root Category)</option>
                            @foreach($parentCategories as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">FontAwesome Icon Class</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', 'fa-solid fa-shield-halved') }}" placeholder="e.g., fa-solid fa-car">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Category Image / Banner</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" checked>
                            <label class="form-check-label fw-semibold" for="statusSwitch">Active Status</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="featSwitch" value="1">
                            <label class="form-check-label fw-semibold" for="featSwitch">Featured Category</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-theme-red px-4 py-2 fw-bold">
                    <i class="fa-solid fa-check me-2"></i> Save Category
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
