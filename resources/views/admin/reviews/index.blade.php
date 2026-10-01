@extends('layouts.admin')

@section('title', 'Reviews Moderation - AutoLogos')
@section('page_title', 'Customer Product Reviews')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Product Reviews & Ratings</h5>
            <p class="text-muted small mb-0">Approve or reject customer feedback and verify rating stars.</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-danger' : 'btn-outline-secondary' }}">All Reviews</a>
                <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-danger' : 'btn-outline-secondary' }}">Pending Approval</a>
                <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="btn btn-sm {{ request('status') === 'approved' ? 'btn-danger' : 'btn-outline-secondary' }}">Approved</a>
                <a href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-outline-secondary' }}">Rejected</a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Product</th>
                        <th>Reviewer</th>
                        <th>Rating</th>
                        <th>Review Comment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('product.detail', $rev->product->slug ?? '#') }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                    {{ $rev->product->name ?? 'Deleted Product' }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $rev->customer_name }}</div>
                                @if($rev->is_verified_buyer)
                                    <span class="badge bg-success-subtle text-success" style="font-size: 10px;"><i class="fa-solid fa-circle-check me-1"></i> Verified Buyer</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-warning small">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $rev->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                @if($rev->title)
                                    <strong class="text-dark d-block small">{{ $rev->title }}</strong>
                                @endif
                                <p class="text-muted small mb-0">{{ $rev->comment }}</p>
                            </td>
                            <td class="text-muted small">{{ $rev->created_at->format('d M Y') }}</td>
                            <td>
                                @if($rev->status === 'approved')
                                    <span class="badge bg-success-subtle text-success">Approved</span>
                                @elseif($rev->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Pending</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    @if($rev->status !== 'approved')
                                        <form action="{{ route('admin.reviews.status', $rev->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-light border text-success" title="Approve Review">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($rev->status !== 'rejected')
                                        <form action="{{ route('admin.reviews.status', $rev->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-light border text-warning" title="Reject Review">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete review?')">
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
                            <td colspan="7" class="text-center py-5 text-muted">No reviews found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
