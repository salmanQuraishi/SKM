@extends('layouts.admin')

@section('title', 'Customers - AutoLogos')
@section('page_title', 'Customer Management')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Registered Customers</h5>
            <p class="text-muted small mb-0">View customer profiles, total orders count, lifetime spend and account statuses.</p>
        </div>
    </div>

    <!-- Search Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name, email or mobile number...">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-dark w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</button>
                    @if(request('q'))
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Customer</th>
                        <th>Email & Phone</th>
                        <th>Registered Date</th>
                        <th>Orders Count</th>
                        <th>Total Spend</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-danger-subtle text-danger fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                        {{ strtoupper(substr($c->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.customers.show', $c->id) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $c->name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $c->email }}</div>
                                <div class="small text-muted">{{ $c->phone ?: 'No phone added' }}</div>
                            </td>
                            <td class="text-muted small">
                                {{ $c->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $c->orders_count }} orders</span>
                            </td>
                            <td class="fw-bold text-dark">
                                &#x20B9;{{ number_format($c->total_spent ?? 0, 2) }}
                            </td>
                            <td>
                                @if($c->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Blocked</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.customers.show', $c->id) }}" class="btn btn-light border text-primary" title="View Customer History">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.toggle-status', $c->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-light border {{ $c->is_active ? 'text-warning' : 'text-success' }}" title="{{ $c->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fa-solid {{ $c->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.customers.destroy', $c->id) }}" method="POST" class="d-inline confirm-action" data-confirm="Permanently delete customer account for '{{ $c->name }}'?" data-confirm-title="Delete Customer?">
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
                            <td colspan="7" class="text-center py-5 text-muted">No customers registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
