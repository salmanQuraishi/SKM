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

        return $this->permissions->contains('slug', $permissionSlug);
    }

    public function isSuperAdmin(): bool
    {
        return $this->slug === 'super_admin';
    }

    public function isSystem(): bool
    {
        return (bool) $this->is_system || in_array($this->slug, ['super_admin', 'admin', 'customer']);
    }
}
