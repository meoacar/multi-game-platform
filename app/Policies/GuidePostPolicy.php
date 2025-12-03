<?php

namespace App\Policies;

use App\Models\GuidePost;
use App\Models\User;
use App\Traits\HasGameContext;
use Illuminate\Auth\Access\Response;

/**
 * Guide Post Policy
 * Rehber yazıları için yetkilendirme kuralları
 * 
 * Game Context: Rehber yazıları oyuna özel kaynaklardır.
 * Kullanıcılar sadece mevcut oyun bağlamındaki rehberlere erişebilir.
 */
class GuidePostPolicy
{
    use HasGameContext;
    /**
     * Herkes rehberleri görüntüleyebilir
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Herkes yayınlanmış rehberleri görüntüleyebilir
     */
    public function view(?User $user, GuidePost $guidePost): bool
    {
        // Admin kullanıcılar tüm oyunlardaki rehberleri görebilir
        if ($user && $this->canBypassGameContext($user)) {
            if ($guidePost->is_published) {
                return true;
            }
            return $user->id === $guidePost->user_id || $user->is_admin;
        }

        // Game context kontrolü
        $this->checkGameContext($guidePost);
        
        if ($guidePost->is_published) {
            return true;
        }

        return $user && ($user->id === $guidePost->user_id || $user->is_admin);
    }

    /**
     * Giriş yapmış kullanıcılar rehber oluşturabilir
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Sadece rehber sahibi veya admin güncelleyebilir
     */
    public function update(User $user, GuidePost $guidePost): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $guidePost->user_id || $user->is_admin;
        }

        // Game context kontrolü
        $this->checkGameContext($guidePost);
        
        return $user->id === $guidePost->user_id || $user->is_admin;
    }

    /**
     * Sadece rehber sahibi veya admin silebilir
     */
    public function delete(User $user, GuidePost $guidePost): bool
    {
        // Admin kulanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $guidePost->user_id || $user->is_admin;
        }

        // Game context kontrolü
        $this->checkGameContext($guidePost);
        
        return $user->id === $guidePost->user_id || $user->is_admin;
    }

    /**
     * Sadece admin geri yükleyebilir
     */
    public function restore(User $user, GuidePost $guidePost): bool
    {
        return $user->is_admin;
    }

    /**
     * Sadece admin kalıcı olarak silebilir
     */
    public function forceDelete(User $user, GuidePost $guidePost): bool
    {
        return $user->is_admin;
    }
}
