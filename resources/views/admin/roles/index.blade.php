@extends('layouts.admin')

@section('title', 'Roles & Permissions - AutoLogos Admin')
@section('page_title', 'Roles & Permissions Management')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with Action Button -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-shield-halved text-danger me-2"></i> Role Management System
            </h4>
            <p class="text-muted small mb-0">Create and configure user roles and specify granular access permissions across the system.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm shadow-xs d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-users"></i> Staff & Team
            </a>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-danger btn-sm shadow-xs d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i> Create New Role
            </a>
        </div>
    </div>

    <!-- Roles Grid / Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-list-check text-primary me-2"></i> All System & Custom Roles ({{ $roles->count() }})
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 font-sm">
                <thead class="bg-light text-muted text-uppercase small" style="font-size: 11px; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3">Role Name</th>
                        <th class="py-3">Slug Identifier</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Assigned Staff</th>
                        <th class="py-3">Permissions</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $role->isSuperAdmin() ? 'bg-danger text-white' : ($role->isSystem() ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary') }}" style="width: 36px; height: 36px;">
                                        <i class="fa-solid {{ $role->isSuperAdmin() ? 'fa-crown' : ($role->slug === 'customer' ? 'fa-user' : 'fa-id-badge') }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $role->name }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $role->description ?: 'No description provided.' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded text-danger fw-semibold">{{ $role->slug }}</code>
                            </td>
                            <td>
                                @if($role->isSuperAdmin())
                                    <span class="badge bg-danger text-white"><i class="fa-solid fa-crown me-1"></i> Super Admin</span>
                                @elseif($role->isSystem())
                                    <span class="badge bg-primary-subtle text-primary border border-primary"><i class="fa-solid fa-lock me-1"></i> System Role</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-puzzle-piece me-1"></i> Custom Role</span>
                                @endif

                                @if(!$role->is_admin && $role->slug === 'customer')
                                    <span class="badge bg-secondary-subtle text-secondary ms-1">Customer Area</span>
                                @else
                                    <span class="badge bg-info-subtle text-info ms-1">Admin Access</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.staff.index', ['role' => $role->id]) }}" class="badge bg-light text-dark border text-decoration-none px-2 py-1">
                                    <i class="fa-solid fa-users me-1 text-primary"></i> {{ $role->users_count }} member(s)
                                </a>
                            </td>
                            <td>
                                @if($role->isSuperAdmin())
                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                        <i class="fa-solid fa-infinity me-1"></i> Full Access (All Permissions)
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-key me-1 text-warning"></i> {{ $role->permissions_count }} permission(s)
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Role & Permissions">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    @if(!$role->isSystem() && $role->users_count === 0)
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete role {{ $role->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Role">
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
                                <i class="fa-solid fa-shield-halved fs-1 text-secondary mb-3 d-block"></i>
                                No roles found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
