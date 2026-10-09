<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_admin',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission($permissionSlug): bool
    {
        if ($this->slug === 'super_admin') {
            return true;
        }

        $slugs = $this->permissions->pluck('slug')->toArray();
        if (in_array($permissionSlug, $slugs, true)) {
            return true;
        }

        $moduleMap = [
            'manage_products' => ['view_products', 'create_products', 'edit_products', 'delete_products', 'duplicate_products', 'bulk_products'],
            'manage_categories' => ['view_categories', 'create_categories', 'edit_categories', 'delete_categories'],
            'manage_brands' => ['view_brands', 'create_brands', 'edit_brands', 'delete_brands'],
            'manage_orders' => ['view_orders', 'update_order_status', 'download_invoices', 'send_order_whatsapp'],
            'manage_customers' => ['view_users', 'create_users', 'edit_users', 'toggle_user_status', 'delete_users'],
            'manage_staff' => ['view_users', 'create_users', 'edit_users', 'toggle_user_status', 'delete_users'],
            'manage_roles' => ['view_roles', 'create_roles', 'edit_roles', 'delete_roles', 'assign_user_permissions'],
            'manage_coupons' => ['view_coupons', 'create_coupons', 'edit_coupons', 'delete_coupons'],
            'manage_banners' => ['view_banners', 'create_banners', 'edit_banners', 'delete_banners'],
            'manage_reviews' => ['view_reviews', 'moderate_reviews', 'delete_reviews'],
            'manage_reports' => ['view_reports', 'export_reports'],
            'manage_pages' => ['view_pages', 'create_pages', 'edit_pages', 'delete_pages'],
            'manage_inquiries' => ['view_inquiries', 'process_inquiries', 'delete_inquiries'],
            'manage_settings' => ['view_settings', 'edit_settings', 'clear_cache'],
        ];

        foreach ($moduleMap as $parent => $children) {
            if (in_array($permissionSlug, $children, true) && in_array($parent, $slugs, true)) {
                return true;
            }
        }

        if (isset($moduleMap[$permissionSlug])) {
            foreach ($moduleMap[$permissionSlug] as $child) {
                if (in_array($child, $slugs, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isSuperAdmin(): bool
    {
        return $this->slug === 'super_admin';
    }

    public function isSystem(): bool
    {
        return (bool) $this->is_system || in_array($this->slug, ['super_admin', 'staff', 'user', 'customer']);
    }
}
