@extends('layouts.admin')

@section('title', 'Edit Role: ' . $role->name . ' - AutoLogos Admin')
@section('page_title', 'Edit Role & Permissions')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.roles.index') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Roles List
            </a>
            <h4 class="fw-bold text-dark mt-1">
                Edit Role: <span class="text-danger">{{ $role->name }}</span>
            </h4>
        </div>
        <div>
            @if($role->isSuperAdmin())
                <span class="badge bg-danger fs-6"><i class="fa-solid fa-crown me-1"></i> Super Admin Role</span>
            @elseif($role->isSystem())
                <span class="badge bg-primary fs-6"><i class="fa-solid fa-lock me-1"></i> System Role</span>
            @endif
        </div>
    </div>

    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: Role Details -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 sticky-top" style="top: 80px;">
                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                        <i class="fa-solid fa-shield text-danger me-2"></i> Role Information
                    </h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Unique Slug Identifier</label>
                        <input type="text" name="slug" class="form-control font-monospace" value="{{ old('slug', $role->slug) }}" {{ $role->isSystem() ? 'readonly disabled' : '' }}>
                        @if($role->isSystem())
                            <small class="text-muted"><i class="fa-solid fa-lock me-1"></i> System role slug cannot be modified.</small>
                        @endif
                        @error('slug') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Description</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $role->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_admin" value="1" id="isAdminCheck" {{ old('is_admin', $role->is_admin) ? 'checked' : '' }} {{ $role->isSuperAdmin() ? 'disabled' : '' }}>
                            <label class="form-check-label fw-semibold text-dark small" for="isAdminCheck">
                                Grant Administrative Access
                            </label>
                        </div>
                        <small class="text-muted">Allows login to the admin control center.</small>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-xs">
                        <i class="fa-solid fa-save me-1"></i> Save Changes & Permissions
                    </button>
                </div>
            </div>

            <!-- Right Column: Permissions Grouped -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="fa-solid fa-key text-warning me-2"></i> Role Permissions Assignment
                            </h6>
                            <p class="text-muted small mb-0">
                                @if($role->isSuperAdmin())
                                    <span class="text-danger fw-bold"><i class="fa-solid fa-infinity me-1"></i> Super Admin automatically possesses all system permissions by design.</span>
                                @else
                                    Select or deselect permissions for all users assigned to this role.
                                @endif
                            </p>
                        </div>
                        @if(!$role->isSuperAdmin())
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllPermissions(true)">
                                    <i class="fa-solid fa-check-double me-1"></i> Select All
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllPermissions(false)">
                                    <i class="fa-solid fa-xmark me-1"></i> Deselect All
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="row g-4">
                        @foreach($permissionsGrouped as $groupName => $perms)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 bg-light h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="fw-bold text-dark font-sm">
                                            <i class="fa-solid fa-folder-tree text-primary me-1"></i> {{ $groupName }}
                                        </span>
                                        @if(!$role->isSuperAdmin())
                                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 font-sm" onclick="toggleGroup('{{ Str::slug($groupName) }}')">
                                                Toggle
                                            </button>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column gap-2 group-{{ Str::slug($groupName) }}">
                                        @foreach($perms as $p)
                                            <div class="form-check border-bottom pb-2 mb-1">
                                                <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="perm_{{ $p->id }}" {{ $role->isSuperAdmin() || in_array($p->id, old('permissions', $assignedPermissionIds)) ? 'checked' : '' }} {{ $role->isSuperAdmin() ? 'disabled' : '' }}>
                                                <label class="form-check-label font-sm text-dark cursor-pointer d-block" for="perm_{{ $p->id }}">
                                                    <span class="fw-semibold">{{ $p->name }}</span>
                                                    @if($p->description)
                                                        <span class="text-muted d-block small" style="font-size: 11.5px;">{{ $p->description }}</span>
                                                    @endif
                                                    <code class="text-secondary" style="font-size: 10px;">{{ $p->slug }}</code>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleAllPermissions(check) {
        document.querySelectorAll('.perm-checkbox:not(:disabled)').forEach(cb => cb.checked = check);
    }

    function toggleGroup(groupSlug) {
        const groupCbs = document.querySelectorAll(`.group-${groupSlug} .perm-checkbox:not(:disabled)`);
        const allChecked = Array.from(groupCbs).every(cb => cb.checked);
        groupCbs.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endpush
@endsection
