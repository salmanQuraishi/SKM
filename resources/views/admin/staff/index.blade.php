@extends('layouts.admin')

@section('title', 'Staff & Team Management - SKM Saller Admin')
@section('page_title', 'Staff & Team Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Button -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-users-gear text-danger me-2"></i> Users & Staff Management
            </h4>
            <p class="text-muted small mb-0">Manage administrative staff and system team members, assign operational roles (Super Admin, Staff), and assign granular permissions.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm shadow-xs d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Manage Roles
            </a>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-danger btn-sm shadow-xs d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Add New User / Staff
            </a>
        </div>
    </div>

    @if(!empty($isCustomerRoleSelected))
        <!-- Customer Role Notice Banner -->
        <div class="alert alert-info border-0 shadow-sm rounded-3 d-flex flex-wrap align-items-center justify-content-between mb-4 p-3 gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white text-info d-flex align-items-center justify-content-center shadow-xs" style="width: 36px; height: 36px;">
                    <i class="fa-solid fa-circle-info fs-5"></i>
                </div>
                <div>
                    <strong class="d-block text-dark">Viewing Store Customers Filter</strong>
                    <span class="small text-muted">You are viewing users belonging to the Customer role. For registered customer order tracking, addresses, and lifetime spend, manage them under Customer Management.</span>
                </div>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-primary px-3 fw-semibold text-nowrap">
                <i class="fa-solid fa-users me-1"></i> Open Customer Management
            </a>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.staff.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Search staff by name, email, or phone..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="role" class="form-select">
                        <option value="">All Staff Roles</option>
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
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="table-layout: auto;">
                <thead class="bg-light text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px; border-bottom: 2px solid #e9ecef;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 25%; min-width: 220px;">Member Details</th>
                        <th class="py-3" style="width: 25%; min-width: 220px;">Contact Information</th>
                        <th class="py-3" style="width: 15%; min-width: 140px;">Assigned Role</th>
                        <th class="py-3" style="width: 18%; min-width: 160px;">Direct Permissions</th>
                        <th class="py-3 text-center" style="width: 8%; min-width: 90px;">Status</th>
                        <th class="py-3 text-end pe-4" style="width: 9%; min-width: 110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffUsers as $member)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0 {{ $member->isSuperAdmin() ? 'bg-danger' : 'bg-dark' }}" style="width: 40px; height: 40px; font-size: 15px;">
                                        {{ strtoupper(substr($member->name ?: 'U', 0, 1)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark d-flex align-items-center gap-2 text-break">
                                            <span>{{ $member->name ?: 'Unnamed Member' }}</span>
                                            @if($member->id === Auth::id())
                                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 9px;">YOU</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small" style="font-size: 11px;">
                                            Joined {{ $member->created_at ? $member->created_at->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="text-break">
                                    <i class="fa-solid fa-envelope text-muted me-1 small"></i> <span class="text-dark">{{ $member->email }}</span>
                                </div>
                                @if($member->phone)
                                    <div class="text-muted small mt-1">
                                        <i class="fa-solid fa-phone text-muted me-1" style="font-size: 10px;"></i> {{ $member->phone }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($member->role)
                                    <span class="badge {{ $member->isSuperAdmin() ? 'bg-danger text-white' : ($member->role->slug === 'staff' ? 'bg-primary text-white' : (in_array($member->role->slug, ['customer', 'user']) ? 'bg-secondary text-white' : 'bg-success text-white')) }}">
                                        @if($member->isSuperAdmin()) <i class="fa-solid fa-crown me-1"></i> @endif
                                        @if($member->role->slug === 'staff') <i class="fa-solid fa-user-shield me-1"></i> @endif
                                        @if(in_array($member->role->slug, ['customer', 'user'])) <i class="fa-solid fa-user me-1"></i> @endif
                                        {{ $member->role->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white"><i class="fa-solid fa-user me-1"></i> No Role</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($member->isSuperAdmin())
                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                        <i class="fa-solid fa-infinity me-1"></i> All Permissions
                                    </span>
                                @elseif($member->directPermissions->count() > 0)
                                    <span class="badge bg-warning-subtle text-dark border border-warning">
                                        <i class="fa-solid fa-plus me-1"></i> {{ $member->directPermissions->count() }} Custom Permission(s)
                                    </span>
                                @elseif($member->role && $member->role->permissions->count() > 0)
                                    <span class="text-muted small">Inherited ({{ $member->role->permissions->count() }})</span>
                                @else
                                    <span class="text-muted small fst-italic">None (Customer / No Admin)</span>
                                @endif
                            </td>
                            <td class="text-center py-3">
                                @if($member->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-ban me-1"></i> Suspended</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
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
                                No staff members found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffUsers->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">
                    Showing {{ $staffUsers->firstItem() }} to {{ $staffUsers->lastItem() }} of {{ $staffUsers->total() }} results
                </div>
                <div>
                    {{ $staffUsers->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
