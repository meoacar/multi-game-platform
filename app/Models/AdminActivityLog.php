<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'admin_id',
        'action',
        'target_type',
        'target_id',
        'meta',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public static function log(string $action, ?string $targetType = null, ?int $targetId = null, ?array $meta = null): void
    {
        static::create([
            'admin_id' => auth()->id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'meta' => $meta,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get human-readable action text
     */
    public function getActionText(): string
    {
        $actions = [
            'ban_user' => 'bir kullanıcıyı banladı',
            'unban_user' => 'bir kullanıcının banını kaldırdı',
            'delete_user' => 'bir kullanıcıyı sildi',
            'toggle_admin' => 'bir kullanıcının admin yetkisini değiştirdi',
            'update_profile' => 'bir profili güncelledi',
            'verify_profile' => 'bir profili doğruladı',
            'delete_profile' => 'bir profili sildi',
            'update_device' => 'bir cihaz kaydını güncelledi',
            'delete_device' => 'bir cihaz kaydını sildi',
            'update_lfg_post' => 'bir ilanı güncelledi',
            'close_lfg_post' => 'bir ilanı kapattı',
            'toggle_featured_lfg' => 'bir ilanı öne çıkardı/kaldırdı',
            'delete_lfg_post' => 'bir ilanı sildi',
            'bulk_close_lfg_posts' => 'toplu ilan kapattı',
            'bulk_delete_lfg_posts' => 'toplu ilan sildi',
            'update_clan' => 'bir klanı güncelledi',
            'verify_clan' => 'bir klanı doğruladı',
            'delete_clan' => 'bir klanı sildi',
            'update_guide' => 'bir rehberi güncelledi',
            'toggle_published_guide' => 'bir rehberi yayınladı/kaldırdı',
            'toggle_featured_guide' => 'bir rehberi öne çıkardı/kaldırdı',
            'delete_guide' => 'bir rehberi sildi',
            'bulk_publish_guides' => 'toplu rehber yayınladı',
            'bulk_unpublish_guides' => 'toplu rehber yayından kaldırdı',
            'bulk_delete_guides' => 'toplu rehber sildi',
            'update_community_post' => 'bir gönderiyi güncelledi',
            'toggle_featured_community' => 'bir gönderiyi öne çıkardı/kaldırdı',
            'delete_community_post' => 'bir gönderiyi sildi',
            'resolve_report' => 'bir raporu çözümledi',
            'reject_report' => 'bir raporu reddetti',
            'create_badge' => 'yeni rozet oluşturdu',
            'update_badge' => 'bir rozeti güncelledi',
            'delete_badge' => 'bir rozeti sildi',
            'adjust_user_xp' => 'bir kullanıcının XP\'sini ayarladı',
            'create_game' => 'yeni oyun ekledi',
            'update_game' => 'bir oyunu güncelledi',
            'delete_game' => 'bir oyunu sildi',
            'update_settings' => 'site ayarlarını güncelledi',
            'create_page' => 'yeni sayfa oluşturdu',
            'update_page' => 'bir sayfayı güncelledi',
            'delete_page' => 'bir sayfayı sildi',
            'toggle_publish_page' => 'bir sayfayı yayınladı/kaldırdı',
        ];

        return $actions[$this->action] ?? $this->action;
    }
}
