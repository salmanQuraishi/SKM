@extends('layouts.admin')

@section('title', 'Staff & Team Management - AutoLogos Admin')
@section('page_title', 'Staff & Team Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Button -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-user-shield text-danger me-2"></i> Team & Staff Members
            </h4>
            <p class="text-muted small mb-0">Manage internal administrative users, assign their operational roles, and grant custom permissions.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm shadow-xs d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Manage Roles
            </a>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-danger btn-sm shadow-xs d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Add New Member
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.staff.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Search by name, email, or phone..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ request('role') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-danger btn-sm px-3 fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['q', 'role']))
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Staff Users Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 font-sm">
                <thead class="bg-light text-muted text-uppercase small" style="font-size: 11px; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Member Details</th>
                        <th class="py-3">Contact Information</th>
                        <th class="py-3">Assigned Role</th>
                        <th class="py-3">Direct Permissions</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffUsers as $member)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs {{ $member->isSuperAdmin() ? 'bg-danger' : 'bg-dark' }}" style="width: 40px; height: 40px; font-size: 15px;">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                            {{ $member->name }}
                                            @if($member->id === Auth::id())
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 9px;">YOU</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small" style="font-size: 11px;">
                                            Joined {{ $member->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-envelope text-muted me-1 small"></i> <span class="text-dark">{{ $member->email }}</span></div>
                                @if($member->phone)
                                    <div class="text-muted small"><i class="fa-solid fa-phone text-muted me-1" style="font-size: 10px;"></i> {{ $member->phone }}</div>
                                @endif
                            </td>
                            <td>
                                @if($member->role)
                                    <span class="badge {{ $member->isSuperAdmin() ? 'bg-danger' : 'bg-primary-subtle text-primary border border-primary' }}">
                                        @if($member->isSuperAdmin()) <i class="fa-solid fa-crown me-1"></i> @endif
                                        {{ $member->role->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white">No Role</span>
                                @endif
                            </td>
                            <td>
                                @if($member->isSuperAdmin())
                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                        <i class="fa-solid fa-infinity me-1"></i> All Permissions
                                    </span>
                                @elseif($member->directPermissions->count() > 0)
                                    <span class="badge bg-warning-subtle text-dark border border-warning">
                                        <i class="fa-solid fa-plus me-1"></i> {{ $member->directPermissions->count() }} Custom Permission(s)
                                    </span>
                                @else
                                    <span class="text-muted small">Inherited from Role ({{ $member->role ? $member->role->permissions->count() : 0 }})</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($member->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-ban me-1"></i> Suspended</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.staff.edit', $member->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Member & Permissions">
                                        <i class="fa-solid fa-user-pen"></i>
                                    </a>

                                    @if($member->id !== Auth::id())
                                        <form action="{{ route('admin.staff.toggle_status', $member->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $member->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $member->is_active ? 'Deactivate Member' : 'Activate Member' }}">
                                                <i class="fa-solid {{ $member->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Member">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-user-slash fs-1 text-secondary mb-3 d-block"></i>
                                No team members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffUsers->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                {{ $staffUsers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
