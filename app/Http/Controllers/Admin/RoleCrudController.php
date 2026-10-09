<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleCrudController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions'])->withCount(['users', 'permissions'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionsGrouped = Permission::all()->groupBy('group');
        return view('admin.roles.create', compact('permissionsGrouped'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:roles,slug',
            'description' => 'nullable|string|max:255',
            'is_admin' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Role::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}_{$counter}";
            $counter++;
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_admin' => $request->boolean('is_admin', true),
            'is_system' => false,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', "Role '{$role->name}' created successfully with permissions.");
    }

    public function edit(Role $role)
    {
        $permissionsGrouped = Permission::all()->groupBy('group');
        $assignedPermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissionsGrouped', 'assignedPermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_admin' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        if (!$role->isSystem()) {
            $rules['slug'] = 'nullable|string|max:100|unique:roles,slug,' . $role->id;
        }

        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if (!$role->isSystem()) {
            $updateData['is_admin'] = $request->boolean('is_admin', true);
            if ($request->filled('slug')) {
                $updateData['slug'] = Str::slug($request->slug);
            }
        }

        $role->update($updateData);

        // Super Admin keeps all permissions unconditionally
        if ($role->isSuperAdmin()) {
            $allPermIds = Permission::pluck('id')->toArray();
            $role->permissions()->sync($allPermIds);
        } else {
            $role->permissions()->sync($request->input('permissions', []));
        }

        return redirect()->route('admin.roles.index')->with('success', "Role '{$role->name}' updated successfully.");
    }

    public function destroy(Role $role)
    {
        if ($role->isSystem()) {
            return redirect()->route('admin.roles.index')->with('error', "System role '{$role->name}' is protected and cannot be deleted.");
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', "Cannot delete role '{$role->name}' because {$role->users()->count()} user(s) are assigned to it. Reassign users first.");
        }

        $roleName = $role->name;
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', "Role '{$roleName}' has been removed successfully.");
    }
}
