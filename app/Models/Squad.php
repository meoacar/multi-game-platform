<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Squad extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'leader_id',
        'max_members',
        'game_mode',
        'rank_requirement',
        'region',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // İlişkiler
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'squad_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    // Slug oluştur
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($squad) {
            if (empty($squad->slug)) {
                $squad->slug = Str::slug($squad->name) . '-' . Str::random(6);
            }
        });
    }

    // Takım dolu mu?
    public function isFull()
    {
        return $this->members()->count() >= $this->max_members;
    }

    // Kullanıcı üye mi?
    public function hasMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }
}
