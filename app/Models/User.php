<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'avatar',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    public function allPermissions()
    {
        $rolePermissions = $this->role ? $this->role->permissions : collect();
        $direct = $this->directPermissions;
        return $rolePermissions->merge($direct)->unique('id');
    }

    public function isAdmin(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // If user has direct administrative permissions granted
        if ($this->directPermissions()->count() > 0) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        if (in_array($this->role->slug, ['user', 'customer'])) {
            return false;
        }

        return (bool) $this->role->is_admin
            || in_array($this->role->slug, ['super_admin', 'staff', 'admin', 'manager']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->slug === 'super_admin';
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Direct user permissions
        if ($this->relationLoaded('directPermissions')) {
            $directSlugs = $this->directPermissions->pluck('slug')->toArray();
        } else {
            $directSlugs = $this->directPermissions()->pluck('slug')->toArray();
        }

        // Role permissions
        $roleSlugs = [];
        if ($this->role) {
            if ($this->role->isSuperAdmin()) {
                return true;
            }
            if ($this->role->relationLoaded('permissions')) {
                $roleSlugs = $this->role->permissions->pluck('slug')->toArray();
            } else {
                $roleSlugs = $this->role->permissions()->pluck('slug')->toArray();
            }
        }

        $allSlugs = array_unique(array_merge($directSlugs, $roleSlugs));

        if (in_array($permissionSlug, $allSlugs, true)) {
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
            if (in_array($permissionSlug, $children, true) && in_array($parent, $allSlugs, true)) {
                return true;
            }
        }

        if (isset($moduleMap[$permissionSlug])) {
            foreach ($moduleMap[$permissionSlug] as $child) {
                if (in_array($child, $allSlugs, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
