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
        if (!$this->role) {
            return false;
        }

        if ($this->role->slug === 'customer') {
            return false;
        }

        return (bool) $this->role->is_admin
            || in_array($this->role->slug, ['super_admin', 'admin', 'manager', 'staff', 'order_manager', 'product_manager', 'sales_manager', 'support_staff']);
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

        // Check loaded relations first for optimal performance
        if ($this->relationLoaded('directPermissions')) {
            if ($this->directPermissions->contains('slug', $permissionSlug)) {
                return true;
            }
        } elseif ($this->directPermissions()->where('slug', $permissionSlug)->exists()) {
            return true;
        }

        if ($this->role && $this->role->hasPermission($permissionSlug)) {
            return true;
        }

        return false;
    }
}
