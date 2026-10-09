<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
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
     * Test 1: The standard roles in the system are Super Admin, Staff, and User
     */
    public function test_standard_roles_exist_and_clean(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $roles = Role::pluck('name', 'slug')->toArray();
        $this->assertArrayHasKey('super_admin', $roles);
        $this->assertArrayHasKey('staff', $roles);
        $this->assertArrayHasKey('user', $roles);
        $this->assertEquals('Super Admin', $roles['super_admin']);
        $this->assertEquals('Staff', $roles['staff']);
        $this->assertEquals('User', $roles['user']);
    }

    /**
     * Test 2: Granular permissions exist for all points (50+ points across modules)
     */
    public function test_granular_permissions_exist_for_all_points(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $criticalPointSlugs = [
            'view_dashboard', 'view_catalog_analytics',
            'view_products', 'create_products', 'edit_products', 'delete_products', 'duplicate_products', 'bulk_products',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_brands', 'create_brands', 'edit_brands', 'delete_brands',
            'view_orders', 'update_order_status', 'download_invoices', 'send_order_whatsapp',
            'view_users', 'create_users', 'edit_users', 'toggle_user_status', 'delete_users',
            'view_roles', 'create_roles', 'edit_roles', 'delete_roles', 'assign_user_permissions',
            'view_coupons', 'create_coupons', 'edit_coupons', 'delete_coupons',
            'view_banners', 'create_banners', 'edit_banners', 'delete_banners',
            'view_reviews', 'moderate_reviews', 'delete_reviews',
            'view_reports', 'export_reports',
            'view_pages', 'create_pages', 'edit_pages', 'delete_pages',
            'view_inquiries', 'process_inquiries', 'delete_inquiries',
            'view_settings', 'edit_settings', 'clear_cache',
        ];

        foreach ($criticalPointSlugs as $slug) {
            $this->assertDatabaseHas('permissions', ['slug' => $slug]);
        }
    }

    /**
     * Test 3: Super Admin can view Roles list page with Super Admin, Staff, and User
     */
    public function test_super_admin_can_view_roles_list(): void
    {
        $admin = $this->getSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.roles.index'));

        $response->assertStatus(200);
        $response->assertSee('Role Management System', false);
        $response->assertSee('Super Admin', false);
        $response->assertSee('Staff', false);
        $response->assertSee('User', false);
        $response->assertSee('Create New Role', false);
    }

    /**
     * Test 4: Super Admin can create a custom role with permissions
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
     * Test 5: Super Admin can update role and its permissions
     */
    public function test_super_admin_can_update_role_and_sync_permissions(): void
    {
        $admin = $this->getSuperAdmin();
        $role = Role::create([
            'name' => 'Junior Manager ' . Str::random(4),
            'slug' => 'junior-mgr-' . Str::lower(Str::random(4)),
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
     * Test 6: Cannot delete system roles (Super Admin, Staff, User)
     */
    public function test_cannot_delete_system_roles(): void
    {
        $admin = $this->getSuperAdmin();
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        $userRole = Role::where('slug', 'user')->first();

        $response1 = $this->actingAs($admin)->delete(route('admin.roles.destroy', $superAdminRole));
        $response1->assertRedirect(route('admin.roles.index'));
        $response1->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['slug' => 'super_admin']);

        if ($userRole) {
            $response2 = $this->actingAs($admin)->delete(route('admin.roles.destroy', $userRole));
            $response2->assertRedirect(route('admin.roles.index'));
            $response2->assertSessionHas('error');
            $this->assertDatabaseHas('roles', ['slug' => 'user']);
        }
    }

    /**
     * Test 7: Super Admin can view users & staff list page
     */
    public function test_super_admin_can_view_users_and_staff_list(): void
    {
        $admin = $this->getSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSee('Users & Staff Management', false);
        $response->assertSee('Add New User / Staff', false);
    }

    /**
     * Test 8: Super Admin can create a user/staff member with direct permissions for any points
     */
    public function test_super_admin_can_create_user_with_direct_permissions(): void
    {
        $admin = $this->getSuperAdmin();
        $staffRole = Role::where('slug', 'staff')->first() ?? Role::firstOrCreate(['slug' => 'staff'], ['name' => 'Staff', 'is_admin' => true]);
        $permission = Permission::firstOrCreate(['slug' => 'update_order_status'], ['name' => 'Update Order Status', 'group' => 'Orders & Logistics']);

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
        $this->assertTrue($user->hasPermission('update_order_status'));
    }

    /**
     * Test 9: Super Admin can toggle staff active status via AJAX
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
     * Test 10: Staff user cannot deactivate or delete themselves
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
     * Test 11: Granular permission checks block unauthorized staff but allow authorized staff
     */
    public function test_granular_permission_middleware_blocks_unauthorized_staff(): void
    {
        // Create staff user without manage_roles or view_roles permission
        $roleWithoutRolesPerm = Role::create([
            'name' => 'Restricted Role ' . Str::random(4),
            'slug' => 'restricted-' . Str::lower(Str::random(4)),
            'is_admin' => true,
        ]);
        $restrictedStaff = $this->createStaffUser([], $roleWithoutRolesPerm);

        // Attempt to access roles page (requires manage_roles permission)
        $response = $this->actingAs($restrictedStaff)->get(route('admin.roles.index'));
        $response->assertStatus(403);

        // Now grant direct granular permission 'view_roles' or 'manage_roles'
        $manageRolesPerm = Permission::firstOrCreate(['slug' => 'manage_roles'], ['name' => 'Manage Roles', 'group' => 'Roles & Permissions']);
        $restrictedStaff->directPermissions()->attach($manageRolesPerm->id);
        $restrictedStaff->refresh();

        // Now staff should be allowed
        $allowedResponse = $this->actingAs($restrictedStaff)->get(route('admin.roles.index'));
        $allowedResponse->assertStatus(200);
    }
}
