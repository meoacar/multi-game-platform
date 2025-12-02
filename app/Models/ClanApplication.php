<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Clan Application Model
 * Klanlara yapılan başvurular
 * 
 * İlişkiler:
 * - belongsTo: Clan
 * - belongsTo: User (başvuran)
 */
class ClanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clan_id',
        'message',
        'status',
    ];

    /**
     * Klan
     */
    public function clan()
    {
        return $this->belongsTo(Clan::class);
    }

    /**
     * Başvuran kullanıcı
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Bekleyen başvurular
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Kabul edilen başvurular
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope: Reddedilen başvurular
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Başvuruyu kabul et ve kullanıcıyı klana ekle
     */
    public function accept(): void
    {
        $this->update(['status' => 'accepted']);
        
        // Kullanıcıyı klana ekle
        $this->clan->members()->attach($this->user_id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);
        
        // Üye sayısını güncelle
        $this->clan->updateMemberCount();
    }

    /**
     * Başvuruyu reddet
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Başvuru beklemede mi?
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Başvuru kabul edildi mi?
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Başvuru reddedildi mi?
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
