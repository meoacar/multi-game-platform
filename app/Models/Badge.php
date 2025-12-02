<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'rarity',
        'is_hidden',
        'is_active',
        'sort_order',
        'xp_required',
    ];

    protected $casts = [
        'xp_required' => 'integer',
        'is_hidden' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('unlocked_at', 'progress', 'progress_max')
            ->withTimestamps();
    }

    /**
     * Scope: Sadece aktif rozetler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Kategoriye göre filtrele
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Nadirliğe göre filtrele
     */
    public function scopeByRarity($query, string $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    /**
     * Scope: Gizli olmayan rozetler
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }
}
