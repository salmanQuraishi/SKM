@extends('layouts.admin')

@section('title', 'Add CMS Page - AutoLogos')
@section('page_title', 'Create Content Page')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Create CMS Page</h5>
            <p class="text-muted small mb-0">Write articles, terms, policy documents or custom content.</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
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

    <div class="card border-0 shadow-sm rounded-3 bg-white" style="max-width: 900px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.pages.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Page Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g., Warranty & Quality Guarantee" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">Page Content (HTML / Text) <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="12" placeholder="Write page content here..." required>{{ old('content') }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">SEO Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark">SEO Meta Description</label>
                        <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description') }}">
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_published" id="pubSwitch" value="1" checked>
                    <label class="form-check-label fw-semibold" for="pubSwitch">Published Live</label>
                </div>

                <button type="submit" class="btn btn-theme-red px-4 py-2 fw-bold">
                    <i class="fa-solid fa-check me-2"></i> Save Page
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
