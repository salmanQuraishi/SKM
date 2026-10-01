@extends('layouts.admin')

@section('title', 'CMS Pages - AutoLogos')
@section('page_title', 'Content Pages (CMS)')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Static & Policy Pages</h5>
            <p class="text-muted small mb-0">Manage About Us, Privacy Policy, Terms & Conditions, Shipping Policy, and Custom Pages.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-theme-red px-3">
            <i class="fa-solid fa-plus me-1"></i> Add CMS Page
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Page Title</th>
                        <th>Slug / URL</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $pg)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark">{{ $pg->title }}</strong>
                            </td>
                            <td>
                                <code>/page/{{ $pg->slug }}</code>
                            </td>
                            <td>
                                @if($pg->is_published)
                                    <span class="badge bg-success-subtle text-success">Published</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">Draft</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $pg->updated_at->format('d M Y, h:i A') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('page.show', $pg->slug) }}" target="_blank" class="btn btn-light border text-secondary" title="Preview Live">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $pg->id) }}" class="btn btn-light border text-primary" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $pg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete page {{ $pg->title }}?')">
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
                            <td colspan="5" class="text-center py-5 text-muted">No CMS pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pages->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $pages->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
