<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Device Policy
 * Cihaz işlemleri için yetkilendirme kuralları
 */
class DevicePolicy
{
    /**
     * Tüm cihazları görüntüleme yetkisi
     * Herkes görebilir (paylaşım için)
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Cihaz detayını görüntüleme yetkisi
     * Herkes görebilir
     */
    public function view(?User $user, Device $device): bool
    {
        return true;
    }

    /**
     * Yeni cihaz oluşturma yetkisi
     * Sadece kayıtlı ve yasaklanmamış kullanıcılar
     */
    public function create(User $user): bool
    {
        return !$user->isBanned();
    }

    /**
     * Cihaz güncelleme yetkisi
     * Sadece cihaz sahibi veya admin
     */
    public function update(User $user, Device $device): bool
    {
        return $user->id === $device->user_id || $user->isAdmin();
    }

    /**
     * Cihaz silme yetkisi
     * Sadece cihaz sahibi veya admin
     */
    public function delete(User $user, Device $device): bool
    {
        return $user->id === $device->user_id || $user->isAdmin();
    }

    /**
     * Silinen cihazı geri yükleme yetkisi
     * Sadece admin
     */
    public function restore(User $user, Device $device): bool
    {
        return $user->isAdmin();
    }

    /**
     * Cihazı kalıcı olarak silme yetkisi
     * Sadece admin
     */
    public function forceDelete(User $user, Device $device): bool
    {
        return $user->isAdmin();
    }
}
