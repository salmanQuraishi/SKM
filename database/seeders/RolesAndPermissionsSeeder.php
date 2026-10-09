<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Catalog & Inventory
            ['name' => 'Manage Products', 'slug' => 'manage_products', 'group' => 'Catalog & Inventory'],
            ['name' => 'Manage Categories', 'slug' => 'manage_categories', 'group' => 'Catalog & Inventory'],
            ['name' => 'Manage Brands & Vehicles', 'slug' => 'manage_brands', 'group' => 'Catalog & Inventory'],

            // Sales & Logistics
            ['name' => 'Manage Orders & Dispatch', 'slug' => 'manage_orders', 'group' => 'Sales & Logistics'],
            ['name' => 'Manage Customers', 'slug' => 'manage_customers', 'group' => 'Sales & Logistics'],
            ['name' => 'Manage Coupons & Discounts', 'slug' => 'manage_coupons', 'group' => 'Sales & Logistics'],

            // Marketing & Content
            ['name' => 'Manage Hero & Banners', 'slug' => 'manage_banners', 'group' => 'Marketing & Content'],
            ['name' => 'Manage Reviews', 'slug' => 'manage_reviews', 'group' => 'Marketing & Content'],
            ['name' => 'Manage Sales Reports', 'slug' => 'manage_reports', 'group' => 'Marketing & Content'],
            ['name' => 'Manage CMS Pages', 'slug' => 'manage_pages', 'group' => 'Marketing & Content'],
            ['name' => 'Manage Contact Inquiries', 'slug' => 'manage_inquiries', 'group' => 'Marketing & Content'],

            // System Administration
            ['name' => 'Manage Store Settings', 'slug' => 'manage_settings', 'group' => 'System Administration'],
            ['name' => 'Manage Roles & Permissions', 'slug' => 'manage_roles', 'group' => 'System Administration'],
            ['name' => 'Manage Staff & Team', 'slug' => 'manage_staff', 'group' => 'System Administration'],
        ];

        $allPermissionIds = [];
        $permissionMap = [];
        foreach ($permissions as $p) {
            $created = Permission::updateOrCreate(
                ['slug' => $p['slug']],
                ['name' => $p['name'], 'group' => $p['group']]
            );
            $allPermissionIds[] = $created->id;
            $permissionMap[$p['slug']] = $created->id;
        }

        // 1. Super Admin Role (All Permissions)
        $superAdminRole = Role::updateOrCreate(
            ['slug' => 'super_admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full control over all store features, settings, team members, and security configurations.',
                'is_admin' => true,
                'is_system' => true,
            ]
        );
        $superAdminRole->permissions()->sync($allPermissionIds);

        // 2. Admin Role (Store & Operations Control)
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Operational control over catalog, inventory, orders, marketing, and reports.',
                'is_admin' => true,
                'is_system' => true,
            ]
        );
        $adminPermissions = collect($allPermissionIds)->filter(function ($id) use ($permissionMap) {
            // Exclude manage_roles from standard admin by default
            return $id !== ($permissionMap['manage_roles'] ?? null);
        })->values()->all();
        $adminRole->permissions()->sync($adminPermissions);

        // 3. Manager Role (Store & Catalog Manager)
        $managerRole = Role::updateOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Store Manager',
                'description' => 'Can manage catalog products, vehicle models, customer orders, reviews, and inquiries.',
                'is_admin' => true,
                'is_system' => false,
            ]
        );
        $managerPermSlugs = [
            'manage_products', 'manage_categories', 'manage_brands',
            'manage_orders', 'manage_customers', 'manage_coupons',
            'manage_reviews', 'manage_reports', 'manage_inquiries',
        ];
        $managerPermIds = array_values(array_intersect_key($permissionMap, array_flip($managerPermSlugs)));
        $managerRole->permissions()->sync($managerPermIds);

        // 4. Staff / Operations Role (Orders & Customer Support)
        $staffRole = Role::updateOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Support & Operations Staff',
                'description' => 'Can view and update order status, generate invoices/slips, and handle inquiries.',
                'is_admin' => true,
                'is_system' => false,
            ]
        );
        $staffPermSlugs = ['manage_orders', 'manage_reviews', 'manage_inquiries'];
        $staffPermIds = array_values(array_intersect_key($permissionMap, array_flip($staffPermSlugs)));
        $staffRole->permissions()->sync($staffPermIds);

        // 5. Customer Role (Retail Users)
        $customerRole = Role::updateOrCreate(
            ['slug' => 'customer'],
            [
                'name' => 'Retail Customer',
                'description' => 'Regular retail shopper with account order history and address management.',
                'is_admin' => false,
                'is_system' => true,
            ]
        );
        $customerRole->permissions()->sync([]);

        // Seed Super Admin User (admin@autologos.in / Admin@123456)
        User::firstOrCreate(
            ['email' => 'admin@autologos.in'],
            [
                'name' => 'Store Administrator',
                'phone' => '9876543210',
                'password' => Hash::make('Admin@123456'),
                'role_id' => $superAdminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed Demo Staff User (staff@autologos.in / Staff@123456)
        User::firstOrCreate(
            ['email' => 'staff@autologos.in'],
            [
                'name' => 'Support Staff Demo',
                'phone' => '9876543211',
                'password' => Hash::make('Staff@123456'),
                'role_id' => $staffRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed Demo Customer (customer@autologos.in / Customer@123456)
        $demoUser = User::firstOrCreate(
            ['email' => 'customer@autologos.in'],
            [
                'name' => 'Rohit Sharma',
                'phone' => '9812345678',
                'password' => Hash::make('Customer@123456'),
                'role_id' => $customerRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed demo address for customer
        $demoUser->addresses()->firstOrCreate(
            ['mobile' => '9812345678'],
            [
                'name' => 'Rohit Sharma',
                'email' => 'customer@autologos.in',
                'address_line_1' => 'Flat 402, Greenfield Apartments',
                'address_line_2' => 'Sector 14, MG Road',
                'landmark' => 'Opposite City Center Mall',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'pincode' => '122001',
                'country' => 'India',
                'is_default' => true,
            ]
        );
    }
}
