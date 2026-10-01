@extends('layouts.admin')

@section('title', 'Edit Category - ' . $category->name)
@section('page_title', 'Edit Category: ' . $category->name)

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Edit Category</h5>
            <p class="text-muted small mb-0">Update category details and organization.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">None (Top Level Root Category)</option>
                            @foreach($parentCategories as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id', $category->parent_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">FontAwesome Icon Class</label>
                        <input type="text" name="icon" class="form-control" value="{{ old('icon', $category->icon) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Category Image / Banner</label>
                    @if($category->image)
                        <div class="mb-2">
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-height: 80px;">
                        </div>
                    @endif
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-dark">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $category->sort_order) }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" {{ old('status', $category->status) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="statusSwitch">Active Status</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="featSwitch" value="1" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="featSwitch">Featured Category</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-theme-red px-4 py-2 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
