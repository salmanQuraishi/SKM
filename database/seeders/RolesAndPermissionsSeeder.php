<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // 1. Dashboard & Analytics
            ['name' => 'View Sales Dashboard', 'slug' => 'view_dashboard', 'group' => 'Dashboard & Analytics', 'description' => 'View primary sales metrics, revenue, and daily orders'],
            ['name' => 'View Catalog Analytics', 'slug' => 'view_catalog_analytics', 'group' => 'Dashboard & Analytics', 'description' => 'View brand/catalog traffic and fitment search analytics'],

            // 2. Products & Inventory
            ['name' => 'View Products', 'slug' => 'view_products', 'group' => 'Products & Catalog', 'description' => 'View product listing, specs, inventory, and details'],
            ['name' => 'Create Products', 'slug' => 'create_products', 'group' => 'Products & Catalog', 'description' => 'Add new products to store catalog'],
            ['name' => 'Edit Products', 'slug' => 'edit_products', 'group' => 'Products & Catalog', 'description' => 'Modify product pricing, description, stock, and images'],
            ['name' => 'Delete Products', 'slug' => 'delete_products', 'group' => 'Products & Catalog', 'description' => 'Remove products from the catalog'],
            ['name' => 'Duplicate Products', 'slug' => 'duplicate_products', 'group' => 'Products & Catalog', 'description' => 'Clone an existing product with its details'],
            ['name' => 'Bulk Product Update', 'slug' => 'bulk_products', 'group' => 'Products & Catalog', 'description' => 'Bulk activate/deactivate and update stock'],

            // 3. Categories Management
            ['name' => 'View Categories', 'slug' => 'view_categories', 'group' => 'Categories', 'description' => 'View category list and sub-categories'],
            ['name' => 'Create Categories', 'slug' => 'create_categories', 'group' => 'Categories', 'description' => 'Add new category with banner & thumbnail'],
            ['name' => 'Edit Categories', 'slug' => 'edit_categories', 'group' => 'Categories', 'description' => 'Modify category name, description, and status'],
            ['name' => 'Delete Categories', 'slug' => 'delete_categories', 'group' => 'Categories', 'description' => 'Delete categories from store'],

            // 4. Brands & Vehicle Hierarchy
            ['name' => 'View Brands & Vehicles', 'slug' => 'view_brands', 'group' => 'Brands & Vehicles', 'description' => 'View vehicle manufacturers, car models and variants'],
            ['name' => 'Create Brands & Vehicles', 'slug' => 'create_brands', 'group' => 'Brands & Vehicles', 'description' => 'Add vehicle brand, car model or variant'],
            ['name' => 'Edit Brands & Vehicles', 'slug' => 'edit_brands', 'group' => 'Brands & Vehicles', 'description' => 'Modify brand logos, model years and variant engine trims'],
            ['name' => 'Delete Brands & Vehicles', 'slug' => 'delete_brands', 'group' => 'Brands & Vehicles', 'description' => 'Delete brands, car models or variants'],

            // 5. Orders & Logistics
            ['name' => 'View Orders', 'slug' => 'view_orders', 'group' => 'Orders & Logistics', 'description' => 'View customer orders, billing and shipping addresses'],
            ['name' => 'Update Order Status', 'slug' => 'update_order_status', 'group' => 'Orders & Logistics', 'description' => 'Update status (Pending, Shipped, Delivered, Cancelled)'],
            ['name' => 'Download Invoices & Slips', 'slug' => 'download_invoices', 'group' => 'Orders & Logistics', 'description' => 'Download GST invoice, packing slips and customer receipts'],
            ['name' => 'Send WhatsApp Alerts', 'slug' => 'send_order_whatsapp', 'group' => 'Orders & Logistics', 'description' => 'Trigger WhatsApp order status updates to customer'],

            // 6. Users & Customers
            ['name' => 'View Users & Customers', 'slug' => 'view_users', 'group' => 'Users & Staff', 'description' => 'View registered customers and team members list'],
            ['name' => 'Create Users & Staff', 'slug' => 'create_users', 'group' => 'Users & Staff', 'description' => 'Create new user or staff accounts'],
            ['name' => 'Edit Users & Staff', 'slug' => 'edit_users', 'group' => 'Users & Staff', 'description' => 'Edit user profile, assign roles and change passwords'],
            ['name' => 'Toggle User Status', 'slug' => 'toggle_user_status', 'group' => 'Users & Staff', 'description' => 'Activate or suspend user and customer logins'],
            ['name' => 'Delete Users & Customers', 'slug' => 'delete_users', 'group' => 'Users & Staff', 'description' => 'Delete user/customer records from system'],

            // 7. Roles & Permissions
            ['name' => 'View Roles', 'slug' => 'view_roles', 'group' => 'Roles & Permissions', 'description' => 'View list of system and custom roles'],
            ['name' => 'Create Roles', 'slug' => 'create_roles', 'group' => 'Roles & Permissions', 'description' => 'Create custom roles with custom permission sets'],
            ['name' => 'Edit Roles', 'slug' => 'edit_roles', 'group' => 'Roles & Permissions', 'description' => 'Edit roles and assign/remove permissions'],
            ['name' => 'Delete Roles', 'slug' => 'delete_roles', 'group' => 'Roles & Permissions', 'description' => 'Delete custom roles (system roles protected)'],
            ['name' => 'Assign Direct Permissions', 'slug' => 'assign_user_permissions', 'group' => 'Roles & Permissions', 'description' => 'Super Admin can assign custom permissions directly to individual users'],

            // 8. Coupons & Discounts
            ['name' => 'View Coupons', 'slug' => 'view_coupons', 'group' => 'Coupons & Discounts', 'description' => 'View discount coupon codes and usage analytics'],
            ['name' => 'Create Coupons', 'slug' => 'create_coupons', 'group' => 'Coupons & Discounts', 'description' => 'Create percentage or flat discount coupons'],
            ['name' => 'Edit Coupons', 'slug' => 'edit_coupons', 'group' => 'Coupons & Discounts', 'description' => 'Modify coupon discounts, expiry dates and minimum spends'],
            ['name' => 'Delete Coupons', 'slug' => 'delete_coupons', 'group' => 'Coupons & Discounts', 'description' => 'Remove discount coupons'],

            // 9. Marketing & Banners
            ['name' => 'View Banners', 'slug' => 'view_banners', 'group' => 'Marketing & Banners', 'description' => 'View homepage hero sliders and promotional banners'],
            ['name' => 'Create Banners', 'slug' => 'create_banners', 'group' => 'Marketing & Banners', 'description' => 'Upload new homepage hero banners and sliders'],
            ['name' => 'Edit Banners', 'slug' => 'edit_banners', 'group' => 'Marketing & Banners', 'description' => 'Update banner artwork, captions, links and display sort'],
            ['name' => 'Delete Banners', 'slug' => 'delete_banners', 'group' => 'Marketing & Banners', 'description' => 'Delete hero sliders and banners'],

            // 10. Customer Reviews
            ['name' => 'View Reviews', 'slug' => 'view_reviews', 'group' => 'Customer Reviews', 'description' => 'View product ratings and customer feedback'],
            ['name' => 'Moderate Reviews', 'slug' => 'moderate_reviews', 'group' => 'Customer Reviews', 'description' => 'Approve or reject customer submitted reviews'],
            ['name' => 'Delete Reviews', 'slug' => 'delete_reviews', 'group' => 'Customer Reviews', 'description' => 'Delete inappropriate product reviews'],

            // 11. Sales & Financial Reports
            ['name' => 'View Reports', 'slug' => 'view_reports', 'group' => 'Sales Reports', 'description' => 'View sales charts, order counts, and revenue reports'],
            ['name' => 'Export Reports', 'slug' => 'export_reports', 'group' => 'Sales Reports', 'description' => 'Download sales reports in CSV format'],

            // 12. CMS & Static Pages
            ['name' => 'View CMS Pages', 'slug' => 'view_pages', 'group' => 'CMS Pages', 'description' => 'View static website policy and info pages'],
            ['name' => 'Create CMS Pages', 'slug' => 'create_pages', 'group' => 'CMS Pages', 'description' => 'Create new informational pages'],
            ['name' => 'Edit CMS Pages', 'slug' => 'edit_pages', 'group' => 'CMS Pages', 'description' => 'Edit static page HTML, title and SEO descriptions'],
            ['name' => 'Delete CMS Pages', 'slug' => 'delete_pages', 'group' => 'CMS Pages', 'description' => 'Remove static pages'],

            // 13. Contact Inquiries
            ['name' => 'View Inquiries', 'slug' => 'view_inquiries', 'group' => 'Contact Inquiries', 'description' => 'View messages received through contact form'],
            ['name' => 'Process Inquiries', 'slug' => 'process_inquiries', 'group' => 'Contact Inquiries', 'description' => 'Mark inquiries as read or resolved'],
            ['name' => 'Delete Inquiries', 'slug' => 'delete_inquiries', 'group' => 'Contact Inquiries', 'description' => 'Delete contact form inquiries'],

            // 14. Store Settings & Cache
            ['name' => 'View Settings', 'slug' => 'view_settings', 'group' => 'Settings & System', 'description' => 'View store contact profile, logo and Razorpay key settings'],
            ['name' => 'Edit Settings', 'slug' => 'edit_settings', 'group' => 'Settings & System', 'description' => 'Update company address, branding, and Razorpay credentials'],
            ['name' => 'Clear Cache', 'slug' => 'clear_cache', 'group' => 'Settings & System', 'description' => 'Clear system cache, route cache and application cookies'],

            // Backward Compatibility Module Shortcuts
            ['name' => 'Full Products Module Access', 'slug' => 'manage_products', 'group' => 'Module Full Access', 'description' => 'Complete control over products module'],
            ['name' => 'Full Categories Module Access', 'slug' => 'manage_categories', 'group' => 'Module Full Access', 'description' => 'Complete control over categories module'],
            ['name' => 'Full Brands & Vehicles Access', 'slug' => 'manage_brands', 'group' => 'Module Full Access', 'description' => 'Complete control over vehicles and brands'],
            ['name' => 'Full Orders Module Access', 'slug' => 'manage_orders', 'group' => 'Module Full Access', 'description' => 'Complete control over orders and shipments'],
            ['name' => 'Full Customers Module Access', 'slug' => 'manage_customers', 'group' => 'Module Full Access', 'description' => 'Complete control over customer accounts'],
            ['name' => 'Full Staff & Team Access', 'slug' => 'manage_staff', 'group' => 'Module Full Access', 'description' => 'Complete control over staff members'],
            ['name' => 'Full Roles Module Access', 'slug' => 'manage_roles', 'group' => 'Module Full Access', 'description' => 'Complete control over roles management'],
            ['name' => 'Full Coupons Module Access', 'slug' => 'manage_coupons', 'group' => 'Module Full Access', 'description' => 'Complete control over discount coupons'],
            ['name' => 'Full Banners Module Access', 'slug' => 'manage_banners', 'group' => 'Module Full Access', 'description' => 'Complete control over marketing banners'],
            ['name' => 'Full Reviews Module Access', 'slug' => 'manage_reviews', 'group' => 'Module Full Access', 'description' => 'Complete control over review moderation'],
            ['name' => 'Full Reports Module Access', 'slug' => 'manage_reports', 'group' => 'Module Full Access', 'description' => 'Complete control over sales reports'],
            ['name' => 'Full CMS Pages Module Access', 'slug' => 'manage_pages', 'group' => 'Module Full Access', 'description' => 'Complete control over CMS pages'],
            ['name' => 'Full Inquiries Module Access', 'slug' => 'manage_inquiries', 'group' => 'Module Full Access', 'description' => 'Complete control over contact inquiries'],
            ['name' => 'Full Settings Module Access', 'slug' => 'manage_settings', 'group' => 'Module Full Access', 'description' => 'Complete control over system settings'],
        ];

        $allPermissionIds = [];
        $permissionMap = [];
        foreach ($permissions as $p) {
            $created = Permission::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'name' => $p['name'],
                    'group' => $p['group'],
                    'description' => $p['description'],
                ]
            );
            $allPermissionIds[] = $created->id;
            $permissionMap[$p['slug']] = $created->id;
        }

        // ==========================================
        // 1. Super Admin Role (All Points Master Access)
        // ==========================================
        $superAdminRole = Role::updateOrCreate(
            ['slug' => 'super_admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Master administrator with full unrestricted access to all modules, settings, staff and points.',
                'is_admin' => true,
                'is_system' => true,
            ]
        );
        $superAdminRole->permissions()->sync($allPermissionIds);

        // ==========================================
        // 2. Staff Role (Assigned Operational Points)
        // ==========================================
        $staffRole = Role::updateOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'description' => 'Operations and support staff member with permissions assigned by Super Admin.',
                'is_admin' => true,
                'is_system' => true,
            ]
        );
        $staffSlugs = [
            'view_dashboard',
            'view_products', 'create_products', 'edit_products', 'duplicate_products',
            'view_categories',
            'view_brands',
            'view_orders', 'update_order_status', 'download_invoices', 'send_order_whatsapp',
            'view_users',
            'view_inquiries', 'process_inquiries',
            'view_reviews',
            // Also include module fallbacks
            'manage_orders', 'manage_products', 'manage_inquiries',
        ];
        $staffPermIds = array_values(array_intersect_key($permissionMap, array_flip($staffSlugs)));
        $staffRole->permissions()->sync($staffPermIds);

        // ==========================================
        // 3. User Role (Standard Web / Customer Role)
        // ==========================================
        $userRole = Role::updateOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'User',
                'description' => 'Standard customer / web user account with shopping, order tracking and profile access.',
                'is_admin' => false,
                'is_system' => true,
            ]
        );
        $userRole->permissions()->sync([]);

        // Ensure legacy 'customer' role is mapped or updated to 'user'
        $oldCustomerRole = Role::where('slug', 'customer')->first();
        if ($oldCustomerRole) {
            // Reassign any users pointing to old customer role to the new user role
            User::where('role_id', $oldCustomerRole->id)->update(['role_id' => $userRole->id]);
            // If customer is a distinct record, remove it
            if ($oldCustomerRole->id !== $userRole->id) {
                DB::table('role_permission')->where('role_id', $oldCustomerRole->id)->delete();
                $oldCustomerRole->delete();
            }
        }

        // Clean up obsolete test or temporary roles (leave only clean roles)
        $cleanRoleIds = [$superAdminRole->id, $staffRole->id, $userRole->id];
        $obsoleteRoles = Role::whereNotIn('id', $cleanRoleIds)
            ->whereIn('slug', ['admin', 'manager', 'order_manager', 'product_manager'])
            ->orWhere('slug', 'like', 'inventory%')
            ->orWhere('slug', 'like', 'junior%')
            ->orWhere('slug', 'like', 'restricted%')
            ->get();

        foreach ($obsoleteRoles as $oldRole) {
            User::where('role_id', $oldRole->id)->update(['role_id' => $staffRole->id]);
            DB::table('role_permission')->where('role_id', $oldRole->id)->delete();
            $oldRole->delete();
        }

        // Assign users with null role_id to default 'User' role
        User::whereNull('role_id')->update(['role_id' => $userRole->id]);

        // Seed or update Super Admin User
        User::updateOrCreate(
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

        // Seed or update Demo Staff User
        User::updateOrCreate(
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

        // Seed or update Demo Customer / User
        $demoUser = User::updateOrCreate(
            ['email' => 'customer@autologos.in'],
            [
                'name' => 'Rohit Sharma',
                'phone' => '9812345678',
                'password' => Hash::make('Customer@123456'),
                'role_id' => $userRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed demo address for user
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
