@extends('layouts.admin')

@section('title', 'Add Team Member - AutoLogos Admin')
@section('page_title', 'Add Team Member')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('admin.staff.index') }}" class="text-decoration-none text-muted small">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Team Members
        </a>
        <h4 class="fw-bold text-dark mt-1">Add New Team / Staff Member</h4>
    </div>

    <form action="{{ route('admin.staff.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Member Credentials -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                        <i class="fa-solid fa-user-shield text-danger me-2"></i> Account Information
                    </h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Amit Kumar" required>
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="e.g. amit@autologos.in" required>
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Mobile / Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. 9876543210">
                        @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Assigned Operational Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }} ({{ $r->slug }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">The user will inherit all permissions assigned to this role.</small>
                        @error('role_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveCheck" checked>
                            <label class="form-check-label fw-semibold text-dark small" for="isActiveCheck">
                                Account Active (Can log in immediately)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-xs">
                        <i class="fa-solid fa-user-plus me-1"></i> Create Member
                    </button>
                </div>
            </div>

            <!-- Right Column: Additional Direct Permissions -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="fa-solid fa-key text-warning me-2"></i> Custom Direct Permissions (Optional)
                            </h6>
                            <p class="text-muted small mb-0">Grant additional individual permissions specifically to this user beyond their assigned role.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllPermissions(true)">Select All</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllPermissions(false)">Deselect All</button>
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach($permissionsGrouped as $groupName => $perms)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 bg-light h-100">
                                    <div class="d-flex justify-content-between align-items-center fw-bold text-dark font-sm mb-2 pb-1 border-bottom">
                                        <span><i class="fa-solid fa-folder-tree text-primary me-1"></i> {{ $groupName }}</span>
                                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 font-sm" onclick="toggleGroup('{{ Str::slug($groupName) }}')">
                                            Toggle
                                        </button>
                                    </div>
                                    <div class="d-flex flex-column gap-2 group-{{ Str::slug($groupName) }}">
                                        @foreach($perms as $p)
                                            <div class="form-check border-bottom pb-1 mb-1">
                                                <input class="form-check-input perm-checkbox" type="checkbox" name="direct_permissions[]" value="{{ $p->id }}" id="perm_{{ $p->id }}" {{ in_array($p->id, old('direct_permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label font-sm text-dark cursor-pointer d-block" for="perm_{{ $p->id }}">
                                                    <span class="fw-semibold">{{ $p->name }}</span>
                                                    @if($p->description)
                                                        <span class="text-muted d-block small" style="font-size: 11px;">{{ $p->description }}</span>
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
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = check);
    }

    function toggleGroup(groupSlug) {
        const groupCbs = document.querySelectorAll(`.group-${groupSlug} .perm-checkbox`);
        const allChecked = Array.from(groupCbs).every(cb => cb.checked);
        groupCbs.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endpush
@endsection
