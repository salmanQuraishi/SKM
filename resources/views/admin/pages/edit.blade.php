@extends('layouts.admin')

@section('title', 'Edit Page - ' . $page->title)
@section('page_title', 'Edit Content Page: ' . $page->title)

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Edit CMS Page</h5>
            <p class="text-muted small mb-0">Update content, layout and SEO meta tags.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-outline-dark">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Preview Live
            </a>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white" style="max-width: 900px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Page Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Page Content (HTML / Text) <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="12" required>{{ old('content', $page->content) }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">SEO Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">SEO Meta Description</label>
                        <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $page->meta_description) }}">
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_published" id="pubSwitch" value="1" {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="pubSwitch">Published Live</label>
                </div>

                <button type="submit" class="btn btn-theme-red px-4 py-2 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
