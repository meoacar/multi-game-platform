<?php

namespace App\Policies;

use App\Models\Clan;
use App\Models\User;
use App\Traits\HasGameContext;
use Illuminate\Auth\Access\Response;

/**
 * Clan Policy
 * Klan işlemleri için yetkilendirme kuralları
 * 
 * Game Context: Klanlar oyuna özel kaynaklardır.
 * Kullanıcılar sadece mevcut oyun bağlamındaki klanlara erişebilir.
 */
class ClanPolicy
{
    use HasGameContext;
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
     * Herkes görebilir (sadece kendi oyun bağlamındaki klanları)
     */
    public function view(?User $user, Clan $clan): bool
    {
        // Admin kullanıcılar tüm oyunlardaki klanları görebilir
        if ($user && $this->canBypassGameContext($user)) {
            return true;
        }

        // Game context kontrolü
        $this->checkGameContext($clan);
        
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
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $clan->user_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($clan);
        
        return $user->id === $clan->user_id || $user->isAdmin();
    }

    /**
     * Klan silme yetkisi
     * Sadece klan lideri veya admin
     */
    public function delete(User $user, Clan $clan): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $clan->user_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($clan);
        
        return $user->id === $clan->user_id || $user->isAdmin();
    }

    /**
     * Klana başvuru yapma yetkisi
     * Kayıtlı, yasaklanmamış, üye olmayan ve klan dolu değilse
     */
    public function apply(User $user, Clan $clan): bool
    {
        // Game context kontrolü
        $this->checkGameContext($clan);
        
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
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $clan->user_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($clan);
        
        return $user->id === $clan->user_id || $user->isAdmin();
    }

    /**
     * Klan üyelerini yönetme yetkisi
     * Sadece klan lideri veya admin
     */
    public function manageMembers(User $user, Clan $clan): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $clan->user_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($clan);
        
        return $user->id === $clan->user_id || $user->isAdmin();
    }

    /**
     * Klan onaylama yetkisi (verified)
     * Sadece admin
     */
    public function verify(User $user, Clan $clan): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($clan);
        
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
