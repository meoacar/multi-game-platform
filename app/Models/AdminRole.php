<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Admin Role Model
 * Admin rolleri ve yetkileri
 */
class AdminRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Bu role sahip kullanıcılar
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Bu role ait yetkiler
     */
    public function permissions()
    {
        return $this->belongsToMany(
            AdminPermission::class,
            'role_permission',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    /**
     * Role yetki ekle
     */
    public function givePermission($permission): void
    {
        if (is_string($permission)) {
            $permission = AdminPermission::where('slug', $permission)->firstOrFail();
        }

        $permissionId = $permission instanceof AdminPermission ? $permission->id : $permission;

        if (!$this->permissions()->where('permission_id', $permissionId)->exists()) {
            $this->permissions()->attach($permissionId);
        }
    }

    /**
     * Rolden yetki kaldır
     */
    public function revokePermission($permission): void
    {
        if (is_string($permission)) {
            $permission = AdminPermission::where('slug', $permission)->firstOrFail();
        }

        $permissionId = $permission instanceof AdminPermission ? $permission->id : $permission;
        $this->permissions()->detach($permissionId);
    }

    /**
     * Rolün yetkilerini senkronize et
     */
    public function syncPermissions(array $permissions): void
    {
        $permissionIds = collect($permissions)->map(function ($permission) {
            if (is_string($permission)) {
                return AdminPermission::where('slug', $permission)->firstOrFail()->id;
            }
            return $permission instanceof AdminPermission ? $permission->id : $permission;
        })->toArray();

        $this->permissions()->sync($permissionIds);
    }

    /**
     * Rolün belirli yetkiye sahip olup olmadığını kontrol et
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }
}
