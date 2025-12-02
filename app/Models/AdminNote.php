<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Admin Note Model
 * Adminlerin kullanıcılar hakkında tuttuğu notlar
 */
class AdminNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'note',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Not hakkındaki kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Notu yazan admin
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Not tipine göre renk döndür
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'info' => 'blue',
            'warning' => 'yellow',
            'important' => 'red',
            default => 'gray',
        };
    }

    /**
     * Not tipine göre ikon döndür
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'important' => '🚨',
            default => '📝',
        };
    }
}
