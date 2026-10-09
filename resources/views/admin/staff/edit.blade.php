@extends('layouts.admin')

@section('title', 'Edit Team Member: ' . $staff->name . ' - AutoLogos Admin')
@section('page_title', 'Edit Team Member')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.staff.index') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Team Members
            </a>
            <h4 class="fw-bold text-dark mt-1">
                Edit Member: <span class="text-danger">{{ $staff->name }}</span>
            </h4>
        </div>
        <div>
            @if($staff->isSuperAdmin())
                <span class="badge bg-danger fs-6"><i class="fa-solid fa-crown me-1"></i> Super Administrator</span>
            @endif
        </div>
    </div>

    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: Member Credentials -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                        <i class="fa-solid fa-user-shield text-danger me-2"></i> Account Information
                    </h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $staff->name) }}" required>
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}" required>
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Mobile / Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->phone) }}" placeholder="e.g. 9876543210">
                        @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Reset Password (Optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                        <small class="text-muted">Enter only if you wish to reset their login password.</small>
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Assigned Operational Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required {{ $staff->id === Auth::id() && $staff->isSuperAdmin() ? 'disabled' : '' }}>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" {{ old('role_id', $staff->role_id) == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }} ({{ $r->slug }})
                                </option>
                            @endforeach
                        </select>
                        @if($staff->id === Auth::id() && $staff->isSuperAdmin())
                            <input type="hidden" name="role_id" value="{{ $staff->role_id }}">
                            <small class="text-danger"><i class="fa-solid fa-lock me-1"></i> You cannot change your own Super Admin role.</small>
                        @else
                            <small class="text-muted">Inherits all permissions assigned to this role.</small>
                        @endif
                        @error('role_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveCheck" {{ old('is_active', $staff->is_active) ? 'checked' : '' }} {{ $staff->id === Auth::id() ? 'disabled' : '' }}>
                            <label class="form-check-label fw-semibold text-dark small" for="isActiveCheck">
                                Account Active
                            </label>
                        </div>
                        @if($staff->id === Auth::id())
                            <input type="hidden" name="is_active" value="1">
                            <small class="text-muted">You cannot deactivate your own account.</small>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-xs">
                        <i class="fa-solid fa-save me-1"></i> Save Member Changes
                    </button>
                </div>
            </div>

            <!-- Right Column: Additional Direct Permissions -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="fa-solid fa-key text-warning me-2"></i> Custom Direct Permissions
                            </h6>
                            <p class="text-muted small mb-0">
                                @if($staff->isSuperAdmin())
                                    <span class="text-danger fw-bold"><i class="fa-solid fa-crown me-1"></i> Super Admin has unrestricted access to all permissions.</span>
                                @else
                                    Checked permissions are directly granted to this user in addition to their role permissions.
                                @endif
                            </p>
                        </div>
                        @if(!$staff->isSuperAdmin())
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllPermissions(true)">Select All</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllPermissions(false)">Deselect All</button>
                            </div>
                        @endif
                    </div>

                    <div class="row g-3">
                        @foreach($permissionsGrouped as $groupName => $perms)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 bg-light h-100">
                                    <div class="fw-bold text-dark font-sm mb-2 pb-1 border-bottom">
                                        <i class="fa-solid fa-folder-tree text-primary me-1"></i> {{ $groupName }}
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($perms as $p)
                                            @php
                                                $fromRole = in_array($p->id, $rolePermissionIds);
                                                $isDirect = in_array($p->id, old('direct_permissions', $userDirectPermissionIds));
                                            @endphp
                                            <div class="form-check">
                                                <input class="form-check-input perm-checkbox" type="checkbox" name="direct_permissions[]" value="{{ $p->id }}" id="perm_{{ $p->id }}" {{ $isDirect || $staff->isSuperAdmin() ? 'checked' : '' }} {{ $staff->isSuperAdmin() ? 'disabled' : '' }}>
                                                <label class="form-check-label font-sm text-dark cursor-pointer d-flex align-items-center justify-content-between" for="perm_{{ $p->id }}">
                                                    <span>{{ $p->name }}</span>
                                                    @if($fromRole && !$staff->isSuperAdmin())
                                                        <span class="badge bg-light text-muted border" style="font-size: 9px;" title="Already granted via Role">Via Role</span>
                                                    @endif
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
</script>
@endpush
@endsection
