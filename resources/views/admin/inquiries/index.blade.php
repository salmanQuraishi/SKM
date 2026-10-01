@extends('layouts.admin')

@section('title', 'Contact Inquiries - AutoLogos')
@section('page_title', 'Contact Messages & Support Inquiries')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Customer Support Inquiries</h5>
            <p class="text-muted small mb-0">Messages submitted through the contact page form or phone inquiries.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Sender Name</th>
                        <th>Contact Details</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inq)
                        <tr class="{{ !$inq->is_read ? 'table-warning bg-opacity-25 fw-semibold' : '' }}">
                            <td class="ps-4">
                                <strong class="text-dark">{{ $inq->name }}</strong>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $inq->email }}</div>
                                <div class="small text-muted">{{ $inq->phone ?: 'No phone' }}</div>
                            </td>
                            <td>{{ $inq->subject ?: 'General Inquiry' }}</td>
                            <td>
                                <p class="small text-muted mb-0" style="max-width: 320px;">{{ $inq->message }}</p>
                            </td>
                            <td class="small text-muted">{{ $inq->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                @if($inq->is_read)
                                    <span class="badge bg-light text-muted border">Read</span>
                                @else
                                    <span class="badge bg-danger">New Unread</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    @if(!$inq->is_read)
                                        <form action="{{ route('admin.inquiries.read', $inq->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-light border text-success" title="Mark as Read">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($inq->phone)
                                        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $inq->phone) }}" target="_blank" class="btn btn-light border text-success" title="Reply on WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $inq->email }}" class="btn btn-light border text-primary" title="Reply via Email">
                                        <i class="fa-solid fa-reply"></i>
                                    </a>
                                    <form action="{{ route('admin.inquiries.destroy', $inq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete message?')">
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
                            <td colspan="7" class="text-center py-5 text-muted">No customer inquiries submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inquiries->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $inquiries->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
