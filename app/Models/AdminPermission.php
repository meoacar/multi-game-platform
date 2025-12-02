<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * AdminPermission Model
 * 
 * Admin panelinde kullanılan granular yetkileri temsil eder.
 * Örnek yetkiler: users.view, users.edit, content.delete, vb.
 * 
 * İlişkiler:
 * - roles: AdminRole (many-to-many)
 */
class AdminPermission extends Model
{
    /**
     * Tablo adı
     */
    protected $table = 'admin_permissions';

    /**
     * Toplu atanabilir alanlar
     */
    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
    ];

    /**
     * Tip dönüşümleri
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Bu yetkiye sahip roller
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            'role_permission',
            'permission_id',
            'role_id'
        );
    }

    /**
     * Slug'a göre yetki bul
     * 
     * @param string $slug
     * @return self|null
     */
    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->first();
    }

    /**
     * Gruba göre yetkileri getir
     * 
     * @param string $group
     * @return Collection
     */
    public static function getByGroup(string $group): Collection
    {
        return self::where('group', $group)->get();
    }

    /**
     * Tüm yetki gruplarını getir
     * 
     * @return Collection
     */
    public static function getAllGroups(): Collection
    {
        return self::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
    }

    /**
     * Yetkileri gruplara göre organize et
     * 
     * @return Collection
     */
    public static function getGrouped(): Collection
    {
        return self::all()->groupBy('group');
    }

    /**
     * Belirli bir role sahip mi kontrol et
     * 
     * @param string $roleSlug
     * @return bool
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }
}
