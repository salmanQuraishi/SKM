@extends('layouts.admin')

@section('title', 'Hero Banners - AutoLogos')
@section('page_title', 'Hero Banners & Promotional Slides')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Store Banners</h5>
            <p class="text-muted small mb-0">Manage homepage hero slides, badge text, car visual renders and CTA buttons.</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-theme-red px-3">
            <i class="fa-solid fa-plus me-1"></i> Add New Banner
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 80px;">Visual</th>
                        <th>Banner Title & Subtitle</th>
                        <th>Badge Text</th>
                        <th>CTA Action</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $b)
                        <tr>
                            <td class="ps-4">
                                <div class="rounded bg-dark p-1 text-center" style="width: 60px; height: 45px;">
                                    @if($b->car_image_path)
                                        <img src="{{ asset($b->car_image_path) }}" alt="{{ $b->title }}" class="img-fluid h-100 object-fit-contain">
                                    @elseif($b->image_path)
                                        <img src="{{ asset($b->image_path) }}" alt="{{ $b->title }}" class="img-fluid h-100 object-fit-contain">
                                    @else
                                        <i class="fa-solid fa-image text-white-50 pt-2"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $b->title }}</strong>
                                <small class="text-muted">{{ $b->subtitle }}</small>
                            </td>
                            <td>
                                @if($b->badge_text)
                                    <span class="badge bg-danger-subtle text-danger">{{ $b->badge_text }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ $b->button_url }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                    {{ $b->button_text }} <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 10px;"></i>
                                </a>
                            </td>
                            <td><span class="badge bg-light text-dark border text-uppercase">{{ $b->position }}</span></td>
                            <td>
                                @if($b->status)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.banners.edit', $b->id) }}" class="btn btn-light border text-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $b->id) }}" method="POST" class="d-inline confirm-action" data-confirm="Are you sure you want to delete banner '{{ $b->title }}'? This action cannot be undone." data-confirm-title="Delete Banner?">
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
                            <td colspan="7" class="text-center py-5 text-muted">No hero banners created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($banners->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $banners->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
