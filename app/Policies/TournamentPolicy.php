<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;
use App\Traits\HasGameContext;
use Illuminate\Auth\Access\Response;

/**
 * Tournament Policy
 * Turnuva işlemleri için yetkilendirme kuralları
 * 
 * Game Context: Turnuvalar oyuna özel kaynaklardır.
 * Kullanıcılar sadece mevcut oyun bağlamındaki turnuvalara erişebilir.
 */
class TournamentPolicy
{
    use HasGameContext;

    /**
     * Tüm turnuvaları görüntüleme yetkisi
     * Herkes görebilir
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Turnuva detayını görüntüleme yetkisi
     * Herkes görebilir (sadece kendi oyun bağlamındaki turnuvaları)
     */
    public function view(?User $user, Tournament $tournament): bool
    {
        // Admin kullanıcılar tüm oyunlardaki turnuvaları görebilir
        if ($user && $this->canBypassGameContext($user)) {
            return true;
        }

        // Game context kontrolü
        $this->checkGameContext($tournament);
        
        return true;
    }

    /**
     * Yeni turnuva oluşturma yetkisi
     * Sadece kayıtlı ve yasaklanmamış kullanıcılar
     */
    public function create(User $user): bool
    {
        return !$user->isBanned();
    }

    /**
     * Turnuva güncelleme yetkisi
     * Sadece turnuva organizatörü veya admin
     */
    public function update(User $user, Tournament $tournament): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $tournament->organizer_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($tournament);
        
        return $user->id === $tournament->organizer_id || $user->isAdmin();
    }

    /**
     * Turnuva silme yetkisi
     * Sadece turnuva organizatörü veya admin
     */
    public function delete(User $user, Tournament $tournament): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $tournament->organizer_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($tournament);
        
        return $user->id === $tournament->organizer_id || $user->isAdmin();
    }

    /**
     * Turnuvaya katılma yetkisi
     * Kayıtlı, yasaklanmamış kullanıcılar
     */
    public function join(User $user, Tournament $tournament): bool
    {
        // Game context kontrolü
        $this->checkGameContext($tournament);
        
        return !$user->isBanned() && $tournament->canJoin();
    }

    /**
     * Turnuva takımlarını yönetme yetkisi
     * Sadece turnuva organizatörü veya admin
     */
    public function manageTeams(User $user, Tournament $tournament): bool
    {
        // Admin kullanıcılar game context kontrolünden muaf
        if ($this->canBypassGameContext($user)) {
            return $user->id === $tournament->organizer_id || $user->isAdmin();
        }

        // Game context kontrolü
        $this->checkGameContext($tournament);
        
        return $user->id === $tournament->organizer_id || $user->isAdmin();
    }

    /**
     * Silinen turnuvayı geri yükleme yetkisi
     * Sadece admin
     */
    public function restore(User $user, Tournament $tournament): bool
    {
        return $user->isAdmin();
    }

    /**
     * Turnuvayı kalıcı olarak silme yetkisi
     * Sadece admin
     */
    public function forceDelete(User $user, Tournament $tournament): bool
    {
        return $user->isAdmin();
    }
}
