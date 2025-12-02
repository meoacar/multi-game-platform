<?php

namespace App\Policies;

use App\Models\Clan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Clan Policy
 * Klan işlemleri için yetkilendirme kuralları
 */
class ClanPolicy
{
    /**
     * Tüm klanları görüntüleme yetkisi
     * Herkes görebilir
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Klan detayını görüntüleme yetkisi
     * Herkes görebilir
     */
    public function view(?User $user, Clan $clan): bool
    {
        return true;
    }

    /**
     * Yeni klan oluşturma yetkisi
     * Sadece kayıtlı ve yasaklanmamış kullanıcılar
     */
    public function create(User $user): bool
    {
        return !$user->isBanned();
    }

    /**
     * Klan güncelleme yetkisi
     * Sadece klan lideri veya admin
     */
    public function update(User $user, Clan $clan): bool
    {
        return $user->id === $clan->user_id || $user->isAdmin();
    }

    /**
     * Klan silme yetkisi
     * Sadece klan lideri veya admin
     */
    public function delete(User $user, Clan $clan): bool
    {
        return $user->id === $clan->user_id || $user->isAdmin();
    }

    /**
     * Klana başvuru yapma yetkisi
     * Kayıtlı, yasaklanmamış, üye olmayan ve klan dolu değilse
     */
    public function apply(User $user, Clan $clan): bool
    {
        return !$user->isBanned() 
            && !$clan->hasMember($user) 
            && !$clan->isFull();
    }

    /**
     * Klan başvurularını görüntüleme yetkisi
     * Sadece klan lideri veya admin
     */
    public function viewApplications(User $user, Clan $clan): bool
    {
        return $user->id === $clan->user_id || $user->isAdmin();
    }

    /**
     * Klan üyelerini yönetme yetkisi
     * Sadece klan lideri veya admin
     */
    public function manageMembers(User $user, Clan $clan): bool
    {
        return $user->id === $clan->user_id || $user->isAdmin();
    }

    /**
     * Klan onaylama yetkisi (verified)
     * Sadece admin
     */
    public function verify(User $user, Clan $clan): bool
    {
        return $user->isAdmin();
    }

    /**
     * Silinen klanı geri yükleme yetkisi
     * Sadece admin
     */
    public function restore(User $user, Clan $clan): bool
    {
        return $user->isAdmin();
    }

    /**
     * Klanı kalıcı olarak silme yetkisi
     * Sadece admin
     */
    public function forceDelete(User $user, Clan $clan): bool
    {
        return $user->isAdmin();
    }
}
