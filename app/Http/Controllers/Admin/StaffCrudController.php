<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffCrudController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'directPermissions']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        $staffUsers = $query->latest('id')->paginate(20)->withQueryString();
        $roles = Role::orderBy('id')->get();

        return view('admin.staff.index', compact('staffUsers', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('id')->get();
        $permissionsGrouped = Permission::all()->groupBy('group');

        return view('admin.staff.create', compact('roles', 'permissionsGrouped'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|max:100',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'direct_permissions' => 'nullable|array',
            'direct_permissions.*' => 'exists:permissions,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        if ($request->has('direct_permissions')) {
            $user->directPermissions()->sync($request->direct_permissions);
        }

        return redirect()->route('admin.staff.index')->with('success', "Team member '{$user->name}' added successfully.");
    }

    public function edit(User $staff)
    {
        $roles = Role::orderBy('id')->get();
        $permissionsGrouped = Permission::all()->groupBy('group');
        $userDirectPermissionIds = $staff->directPermissions->pluck('id')->toArray();
        $rolePermissionIds = $staff->role ? $staff->role->permissions->pluck('id')->toArray() : [];

        return view('admin.staff.edit', compact('staff', 'roles', 'permissionsGrouped', 'userDirectPermissionIds', 'rolePermissionIds'));
    }

    public function update(Request $request, User $staff)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => "required|email|max:150|unique:users,email,{$staff->id}",
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|max:100',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'direct_permissions' => 'nullable|array',
            'direct_permissions.*' => 'exists:permissions,id',
        ]);

        // Safety protection for self-editing
        if ($staff->id === Auth::id()) {
            if (!$request->boolean('is_active', true)) {
                return redirect()->back()->with('error', 'You cannot deactivate your own account.')->withInput();
            }
            if ($staff->isSuperAdmin()) {
                $requestedRole = Role::find($request->role_id);
                if ($requestedRole && !$requestedRole->isSuperAdmin()) {
                    return redirect()->back()->with('error', 'You cannot demote yourself from Super Administrator.')->withInput();
                }
            }
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $staff->update($updateData);

        $staff->directPermissions()->sync($request->input('direct_permissions', []));

        return redirect()->route('admin.staff.index')->with('success', "Team member '{$staff->name}' updated successfully.");
    }

    public function toggleStatus(Request $request, User $staff)
    {
        if ($staff->id === Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot change your own account status.'], 400);
            }
            return redirect()->back()->with('error', 'You cannot change your own account status.');
        }

        $staff->update(['is_active' => !$staff->is_active]);
        $statusText = $staff->is_active ? 'activated' : 'deactivated';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $staff->is_active,
                'message' => "Team member '{$staff->name}' has been {$statusText}."
            ]);
        }

        return redirect()->back()->with('success', "Team member '{$staff->name}' has been {$statusText}.");
    }

    public function destroy(User $staff)
    {
        if ($staff->id === Auth::id()) {
            return redirect()->route('admin.staff.index')->with('error', 'You cannot delete your own account.');
        }

        if ($staff->isSuperAdmin()) {
            $superAdminCount = User::whereHas('role', fn($q) => $q->where('slug', 'super_admin'))->count();
            if ($superAdminCount <= 1) {
                return redirect()->route('admin.staff.index')->with('error', 'Cannot delete the only Super Administrator account.');
            }
        }

        $userName = $staff->name;
        $staff->directPermissions()->detach();
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', "Team member '{$userName}' has been deleted.");
    }
}
