<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MenuItem Model
 * 
 * Menü öğeleri için model
 * Hiyerarşik yapı destekler (parent-child ilişkisi)
 * 
 * İlişkiler:
 * - belongsTo: Menu (ana menü)
 * - belongsTo: MenuItem (parent öğe)
 * - hasMany: MenuItem (child öğeler)
 * - morphTo: target (Page, Category, vb.)
 */
class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'route',
        'type',
        'target_id',
        'target_type',
        'icon',
        'css_class',
        'target',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Menü öğesinin ait olduğu menü
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Üst menü öğesi (parent)
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Alt menü öğeleri (children)
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Hedef model (polymorphic ilişki)
     * Örnek: Page, Category, vb.
     */
    public function target()
    {
        return $this->morphTo();
    }

    /**
     * Menü öğesi türleri
     */
    public static function getAvailableTypes(): array
    {
        return [
            'custom' => 'Özel Link',
            'page' => 'Sayfa',
            'category' => 'Kategori',
            'url' => 'Harici URL',
            'route' => 'Route',
        ];
    }

    /**
     * Menü öğesinin URL'ini al
     */
    public function getUrlAttribute($value)
    {
        // Eğer custom URL varsa onu döndür
        if ($value) {
            return $value;
        }

        // Route varsa route URL'ini döndür
        if ($this->route) {
            try {
                return route($this->route);
            } catch (\Exception $e) {
                return '#';
            }
        }

        // Target varsa onun URL'ini döndür
        if ($this->target) {
            if ($this->target instanceof Page) {
                return route('pages.show', $this->target->slug);
            }
            // Diğer target türleri için URL'ler eklenebilir
        }

        return '#';
    }

    /**
     * Menü öğesinin derinliğini hesapla (kaç seviye içeride)
     */
    public function getDepth(): int
    {
        $depth = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }
        
        return $depth;
    }

    /**
     * Menü öğesinin tüm üst öğelerini al (breadcrumb için)
     */
    public function getAncestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }
        
        return $ancestors;
    }
}
