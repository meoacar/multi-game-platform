<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Profile Policy
 * Profil işlemleri için yetkilendirme kuralları
 */
class ProfilePolicy
{
    /**
     * Tüm profilleri görüntüleme yetkisi
     * Herkes görebilir
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Profil detayını görüntüleme yetkisi
     * Herkes görebilir
     */
    public function view(?User $user, Profile $profile): bool
    {
        return true;
    }

    /**
     * Yeni profil oluşturma yetkisi
     * Otomatik oluşturulduğu için gerek yok
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Profil güncelleme yetkisi
     * Sadece profil sahibi veya admin
     */
    public function update(User $user, Profile $profile): bool
    {
        return $user->id === $profile->user_id || $user->isAdmin();
    }

    /**
     * Profil silme yetkisi
     * Sadece admin (profiller kullanıcı ile birlikte silinir)
     */
    public function delete(User $user, Profile $profile): bool
    {
        return $user->isAdmin();
    }

    /**
     * Silinen profili geri yükleme yetkisi
     * Sadece admin
     */
    public function restore(User $user, Profile $profile): bool
    {
        return $user->isAdmin();
    }

    /**
     * Profili kalıcı olarak silme yetkisi
     * Sadece admin
     */
    public function forceDelete(User $user, Profile $profile): bool
    {
        return $user->isAdmin();
    }
}
