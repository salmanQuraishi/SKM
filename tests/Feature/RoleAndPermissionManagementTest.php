<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RoleAndPermissionManagementTest extends TestCase
{
    protected function getSuperAdmin(): User
    {
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super_admin'],
            ['name' => 'Super Admin', 'is_admin' => true, 'is_system' => true]
        );

        $user = User::where('role_id', $superAdminRole->id)->where('is_active', true)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Super Admin Test',
                'email' => 'superadmintest_' . Str::random(6) . '@example.com',
                'password' => Hash::make('secret123'),
                'role_id' => $superAdminRole->id,
                'is_active' => true,
            ]);
        }

        return $user;
    }

    protected function createStaffUser(array $permissions = [], ?Role $role = null): User
    {
        if (!$role) {
            $role = Role::firstOrCreate(
                ['slug' => 'staff'],
                ['name' => 'Staff', 'is_admin' => true, 'is_system' => false]
            );
        }

        $user = User::create([
            'name' => 'Staff Test ' . Str::random(5),
            'email' => 'staff_' . Str::random(8) . '@example.com',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        if (!empty($permissions)) {
            $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id')->toArray();
            $user->directPermissions()->sync($permissionIds);
        }

        return $user;
    }

    /**
     * Test 1: Super Admin can view Roles list page
     */
    public function test_super_admin_can_view_roles_list(): void
    {
        $admin = $this->getSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Role Management System', false);
        $response->assertSee('Create New Role', false);
    }

    /**
     * Test 2: Super Admin can create a custom role with permissions
     */
    public function test_super_admin_can_create_custom_role(): void
    {
        $admin = $this->getSuperAdmin();
        $permissions = Permission::limit(3)->pluck('id')->toArray();
        $slug = 'inventory-clerk-' . Str::lower(Str::random(4));

        $response = $this->actingAs($admin)->post(route('admin.roles.store'), [
            'name' => 'Inventory Clerk',
            'slug' => $slug,
            'description' => 'Handles stock updates only',
            'is_admin' => 1,
            'permissions' => $permissions,
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'Inventory Clerk',
            'slug' => Str::slug($slug),
            'is_admin' => 1,
        ]);

        $role = Role::where('slug', Str::slug($slug))->first();
        $this->assertNotNull($role);
        $this->assertCount(count($permissions), $role->permissions);
    }

    /**
     * Test 3: Super Admin can update role and its permissions
     */
    public function test_super_admin_can_update_role_and_sync_permissions(): void
    {
        $admin = $this->getSuperAdmin();
        $role = Role::create([
            'name' => 'Junior Manager ' . Str::random(4),
            'slug' => 'junior_mgr_' . Str::lower(Str::random(4)),
            'description' => 'Test role',
            'is_admin' => 1,
            'is_system' => false,
        ]);

        $allPermissions = Permission::pluck('id')->toArray();
        $assignPermissions = array_slice($allPermissions, 0, 2);

        $response = $this->actingAs($admin)->put(route('admin.roles.update', $role), [
            'name' => 'Senior Manager Updated',
            'slug' => $role->slug,
            'description' => 'Updated description',
            'is_admin' => 1,
            'permissions' => $assignPermissions,
        ]);

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertEquals('Senior Manager Updated', $role->name);
        $this->assertEquals(2, $role->permissions()->count());
    }

    /**
     * Test 4: Cannot delete a system role
     */
    public function test_cannot_delete_system_role(): void
    {
        $admin = $this->getSuperAdmin();
        $superAdminRole = Role::where('slug', 'super_admin')->first();

        $response = $this->actingAs($admin)->delete(route('admin.roles.destroy', $superAdminRole));

        $response->assertRedirect(route('admin.roles.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['slug' => 'super_admin']);
    }

    /**
     * Test 5: Super Admin can view staff list page
     */
    public function test_super_admin_can_view_staff_list(): void
    {
        $admin = $this->getSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSee('Team & Staff Members', false);
        $response->assertSee('Add New Member', false);
    }

    /**
     * Test 6: Super Admin can create a staff member with role and direct permissions
     */
    public function test_super_admin_can_create_staff_with_direct_permissions(): void
    {
        $admin = $this->getSuperAdmin();
        $staffRole = Role::where('slug', 'staff')->first() ?? Role::firstOrCreate(['slug' => 'staff'], ['name' => 'Staff', 'is_admin' => true]);
        $permission = Permission::firstOrCreate(['slug' => 'manage_orders'], ['name' => 'Manage Orders', 'group' => 'Sales']);

        $email = 'newstaff_' . Str::random(6) . '@example.com';

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'name' => 'Staff With Permissions',
            'email' => $email,
            'phone' => '9876543210',
            'role_id' => $staffRole->id,
            'password' => 'secret1234',
            'is_active' => 1,
            'direct_permissions' => [$permission->id],
        ]);

        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['email' => $email]);
        $user = User::where('email', $email)->first();
        $this->assertTrue($user->directPermissions()->where('permissions.id', $permission->id)->exists());
        $this->assertTrue($user->hasPermission('manage_orders'));
    }

    /**
     * Test 7: Super Admin can toggle staff active status via AJAX
     */
    public function test_super_admin_can_toggle_staff_status(): void
    {
        $admin = $this->getSuperAdmin();
        $staff = $this->createStaffUser();
        $this->assertTrue($staff->is_active);

        $response = $this->actingAs($admin)->postJson(route('admin.staff.toggle_status', $staff));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_active' => false]);
        $staff->refresh();
        $this->assertFalse($staff->is_active);
    }

    /**
     * Test 8: Staff user cannot deactivate or delete themselves
     */
    public function test_staff_cannot_toggle_or_delete_themselves(): void
    {
        $admin = $this->getSuperAdmin();

        // Self toggle
        $response = $this->actingAs($admin)->postJson(route('admin.staff.toggle_status', $admin));
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);

        // Self delete
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.staff.destroy', $admin));
        $deleteResponse->assertRedirect(route('admin.staff.index'));
        $deleteResponse->assertSessionHas('error');
    }

    /**
     * Test 9: Permission middleware protects routes (unauthorized vs authorized)
     */
    public function test_permission_middleware_blocks_unauthorized_staff(): void
    {
        // Create staff user without manage_roles permission
        $roleWithoutRolesPerm = Role::create([
            'name' => 'Restricted Role ' . Str::random(4),
            'slug' => 'restricted_' . Str::lower(Str::random(4)),
            'is_admin' => true,
        ]);
        $restrictedStaff = $this->createStaffUser([], $roleWithoutRolesPerm);

        // Attempt to access roles page (requires manage_roles permission)
        $response = $this->actingAs($restrictedStaff)->get(route('admin.roles.index'));
        $response->assertStatus(403);

        // Now grant direct permission 'manage_roles'
        $manageRolesPerm = Permission::firstOrCreate(['slug' => 'manage_roles'], ['name' => 'Manage Roles', 'group' => 'System']);
        $restrictedStaff->directPermissions()->attach($manageRolesPerm->id);
        $restrictedStaff->refresh();

        // Now staff should be allowed
        $allowedResponse = $this->actingAs($restrictedStaff)->get(route('admin.roles.index'));
        $allowedResponse->assertStatus(200);
    }
}
