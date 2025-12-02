<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Menu Model
 * 
 * Menü yönetimi için ana model
 * İlişkiler:
 * - hasMany: MenuItem (menü öğeleri)
 */
class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'location',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Menüye ait öğeler
     */
    public function items()
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order');
    }

    /**
     * Tüm menü öğeleri (alt öğeler dahil)
     */
    public function allItems()
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    /**
     * Kullanılabilir menü konumları
     */
    public static function getAvailableLocations(): array
    {
        return [
            'header' => 'Header (Üst Menü)',
            'footer' => 'Footer (Alt Menü)',
            'sidebar' => 'Sidebar (Yan Menü)',
            'mobile' => 'Mobile (Mobil Menü)',
        ];
    }

    /**
     * Slug'a göre menü getir
     */
    public static function getBySlug(string $slug)
    {
        return static::where('slug', $slug)
            ->where('is_active', true)
            ->with(['items' => function ($query) {
                $query->where('is_active', true)
                      ->with(['children' => function ($q) {
                          $q->where('is_active', true)->orderBy('order');
                      }]);
            }])
            ->first();
    }
}
