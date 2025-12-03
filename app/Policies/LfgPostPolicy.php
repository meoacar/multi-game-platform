<?php

namespace App\Policies;

use App\Models\LfgPost;
use App\Models\User;
use App\Traits\HasGameContext;
use Illuminate\Auth\Access\Response;

/**
 * LFG Post Policy
 * LFG ilanları için yetkilendirme kuralları
 * 
 * Game Context: LFG ilanları oyuna özel kaynaklardır.
 * Kullanıcılar sadece mevcut oyun bağlamındaki ilanlara erişebilir.
 */
class LfgPostPolicy
{
    use HasGameContext;
    /**
     * Tüm ilanları görüntüleme yetkisi
     * Herkes görebilir
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * İlan detayını görüntüleme yetkisi
     * Herkes görebilir (sadece kendi oyun bağlamındaki ilanları)
     */
    public function view(?User $user, LfgPost $lfgPost): bool
    {
        // Admin kullanıcılar tüm oyunlardaki ilanları görebilir
        if ($user && $this->canBypassGameContext($user)) {
            return true;
        }

        // Game context kontrolü
        $this->checkGameContext($lfgPost);
        
        return true;
    }

    /**
     * Yeni ilan oluşturma yetkisi
     * Sadece kayıtlı ve yasaklanmamış kullanıcılar
     */
    public function create(User $user): bool
    {
        return !$user->isBanned();
    }

    /**
     * İlan güncelleme yetkisi
     * Sadece ilan sahibi veya admin
     */
    public function update(User $user, LfgPost $lfgPost): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $lfgPost->user_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($lfgPost);
        
        return $user->id === $lfgPost->user_id || $user->isAdmin();
    }

    /**
     * İlan silme yetkisi
     * Sadece ilan sahibi veya admin
     */
    public function delete(User $user, LfgPost $lfgPost): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $lfgPost->user_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($lfgPost);
        
        return $user->id === $lfgPost->user_id || $user->isAdmin();
    }

    /**
     * İlana başvuru yapma yetkisi
     * Kayıtlı, yasaklanmamış ve ilan sahibi olmayan kullanıcılar
     */
    public function apply(User $user, LfgPost $lfgPost): bool
    {
        // Game context kontrolü
        $this->checkGameContext($lfgPost);
        
        return !$user->isBanned() 
            && $user->id !== $lfgPost->user_id 
            && !$lfgPost->isClosed();
    }

    /**
     * İlan başvurularını görüntüleme yetkisi
     * Sadece ilan sahibi veya admin
     */
    public function viewApplications(User $user, LfgPost $lfgPost): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $lfgPost->user_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($lfgPost);
        
        return $user->id === $lfgPost->user_id || $user->isAdmin();
    }

    /**
     * Silinen ilanı geri yükleme yetkisi
     * Sadece admin
     */
    public function restore(User $user, LfgPost $lfgPost): bool
    {
        return $user->isAdmin();
    }

    /**
     * İlanı kalıcı olarak silme yetkisi
     * Sadece admin
     */
    public function forceDelete(User $user, LfgPost $lfgPost): bool
    {
        return $user->isAdmin();
    }
}
