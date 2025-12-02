<?php

namespace App\Policies;

use App\Models\LfgPost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * LFG Post Policy
 * LFG ilanları için yetkilendirme kuralları
 */
class LfgPostPolicy
{
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
     * Herkes görebilir
     */
    public function view(?User $user, LfgPost $lfgPost): bool
    {
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
        return $user->id === $lfgPost->user_id || $user->isAdmin();
    }

    /**
     * İlan silme yetkisi
     * Sadece ilan sahibi veya admin
     */
    public function delete(User $user, LfgPost $lfgPost): bool
    {
        return $user->id === $lfgPost->user_id || $user->isAdmin();
    }

    /**
     * İlana başvuru yapma yetkisi
     * Kayıtlı, yasaklanmamış ve ilan sahibi olmayan kullanıcılar
     */
    public function apply(User $user, LfgPost $lfgPost): bool
    {
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
