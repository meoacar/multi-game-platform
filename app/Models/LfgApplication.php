<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * LFG Application Model
 * İlanlara yapılan başvurular
 * 
 * İlişkiler:
 * - belongsTo: LfgPost
 * - belongsTo: User (başvuran)
 */
class LfgApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'lfg_post_id',
        'user_id',
        'message',
        'status',
    ];

    /**
     * İlan
     */
    public function lfgPost()
    {
        return $this->belongsTo(LfgPost::class);
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
     * Başvuruyu kabul et
     */
    public function accept(): void
    {
        $this->update(['status' => 'accepted']);
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
