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
            ['name' => 'Manage Products', 'slug' => 'manage_products', 'group' => 'products'],
            ['name' => 'Manage Categories', 'slug' => 'manage_categories', 'group' => 'categories'],
            ['name' => 'Manage Brands', 'slug' => 'manage_brands', 'group' => 'brands'],
            ['name' => 'Manage Orders', 'slug' => 'manage_orders', 'group' => 'orders'],
            ['name' => 'Manage Customers', 'slug' => 'manage_customers', 'group' => 'customers'],
            ['name' => 'Manage Coupons', 'slug' => 'manage_coupons', 'group' => 'coupons'],
            ['name' => 'Manage Banners', 'slug' => 'manage_banners', 'group' => 'banners'],
            ['name' => 'Manage Reviews', 'slug' => 'manage_reviews', 'group' => 'reviews'],
            ['name' => 'Manage Reports', 'slug' => 'manage_reports', 'group' => 'reports'],
            ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'group' => 'settings'],
        ];

        $permissionIds = [];
        foreach ($permissions as $p) {
            $created = Permission::firstOrCreate(['slug' => $p['slug']], $p);
            $permissionIds[] = $created->id;
        }

        // Roles
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super_admin'],
            ['name' => 'Super Admin', 'description' => 'Full control over all features and configurations']
        );
        $superAdminRole->permissions()->sync($permissionIds);

        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Administrative control over store and catalog']
        );
        $adminRole->permissions()->sync($permissionIds);

        $orderManagerRole = Role::firstOrCreate(
            ['slug' => 'order_manager'],
            ['name' => 'Order Manager', 'description' => 'Can view and update orders and shipments']
        );

        $productManagerRole = Role::firstOrCreate(
            ['slug' => 'product_manager'],
            ['name' => 'Product Manager', 'description' => 'Can manage products, categories and inventory']
        );

        $customerRole = Role::firstOrCreate(
            ['slug' => 'customer'],
            ['name' => 'Customer', 'description' => 'Standard retail customer account']
        );

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
                'address_type' => 'Home',
                'is_default' => true,
            ]
        );
    }
}
