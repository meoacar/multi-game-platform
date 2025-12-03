<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\LfgApplicationReceived;
use App\Notifications\ClanApplicationReceived;
use App\Notifications\ApplicationAccepted;
use App\Notifications\ApplicationRejected;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\DB;

/**
 * Notification Service
 * Bildirim gönderme işlemleri
 * 
 * Cross-game özellik: Bildirimler nullable game_id ile saklanır.
 * Oyuna özel bildirimler game_id alır, platform geneli bildirimler null kalır.
 */
class NotificationService
{
    /**
     * LFG başvurusu bildirimi gönder
     * 
     * Oyuna özel bildirim - game_id session'dan alınır
     *
     * @param User $postOwner
     * @param User $applicant
     * @param string $postTitle
     * @param int $postId
     * @return void
     */
    public function sendLfgApplicationNotification(User $postOwner, User $applicant, string $postTitle, int $postId): void
    {
        $notification = $postOwner->notify(new LfgApplicationReceived($applicant, $postTitle, $postId));
        
        // Oyuna özel bildirim - game_id ekle
        $this->attachGameIdToNotification($postOwner, $notification);
    }

    /**
     * Klan başvurusu bildirimi gönder
     * 
     * Oyuna özel bildirim - game_id session'dan alınır
     *
     * @param User $clanLeader
     * @param User $applicant
     * @param string $clanName
     * @param string $clanSlug
     * @return void
     */
    public function sendClanApplicationNotification(User $clanLeader, User $applicant, string $clanName, string $clanSlug): void
    {
        $notification = $clanLeader->notify(new ClanApplicationReceived($applicant, $clanName, $clanSlug));
        
        // Oyuna özel bildirim - game_id ekle
        $this->attachGameIdToNotification($clanLeader, $notification);
    }

    /**
     * Başvuru kabul bildirimi gönder
     * 
     * Oyuna özel bildirim - game_id session'dan alınır
     *
     * @param User $applicant
     * @param string $title
     * @param string $type (lfg veya clan)
     * @param int $targetId
     * @return void
     */
    public function sendApplicationAcceptedNotification(User $applicant, string $title, string $type, int $targetId): void
    {
        $notification = $applicant->notify(new ApplicationAccepted($title, $type, $targetId));
        
        // Oyuna özel bildirim - game_id ekle
        $this->attachGameIdToNotification($applicant, $notification);
    }

    /**
     * Başvuru red bildirimi gönder
     * 
     * Oyuna özel bildirim - game_id session'dan alınır
     *
     * @param User $applicant
     * @param string $title
     * @param string $type (lfg veya clan)
     * @return void
     */
    public function sendApplicationRejectedNotification(User $applicant, string $title, string $type): void
    {
        $notification = $applicant->notify(new ApplicationRejected($title, $type));
        
        // Oyuna özel bildirim - game_id ekle
        $this->attachGameIdToNotification($applicant, $notification);
    }

    /**
     * Hoş geldin mesajı gönder
     * 
     * Platform geneli bildirim - game_id null kalır (cross-game)
     *
     * @param User $user
     * @return void
     */
    public function sendWelcomeNotification(User $user): void
    {
        // Platform geneli bildirim - game_id eklenmez (null kalır)
        $user->notify(new WelcomeNotification());
    }

    /**
     * Profil tamamlama hatırlatması gönder
     * 
     * Platform geneli bildirim - game_id null kalır (cross-game)
     *
     * @param User $user
     * @return void
     */
    public function sendProfileCompletionReminder(User $user): void
    {
        // Profil tamamlanmamışsa bildirim gönder
        if (!$user->profile || !$user->profile->is_profile_completed) {
            // Bu özellik ileride eklenebilir
        }
    }
    
    /**
     * Bildirimlere game_id ekle
     * 
     * Oyuna özel bildirimlere session'daki game_id'yi ekler.
     * Bu sayede unified notification center'da oyuna göre filtreleme yapılabilir.
     *
     * @param User $user
     * @param mixed $notification
     * @return void
     */
    protected function attachGameIdToNotification(User $user, $notification): void
    {
        $gameId = session('game_id');
        
        if ($gameId) {
            // Son eklenen bildirimi bul ve game_id'yi güncelle
            DB::table('notifications')
                ->where('notifiable_type', get_class($user))
                ->where('notifiable_id', $user->id)
                ->whereNull('game_id')
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->update(['game_id' => $gameId]);
        }
    }
    
    /**
     * Oyuna özel bildirim gönder
     * 
     * Generic metod - herhangi bir bildirim için game_id ile gönderim
     *
     * @param User $user
     * @param mixed $notification
     * @param int|null $gameId
     * @return void
     */
    public function sendGameSpecificNotification(User $user, $notification, ?int $gameId = null): void
    {
        $user->notify($notification);
        
        // Game ID belirtilmişse onu kullan, yoksa session'dan al
        $gameId = $gameId ?? session('game_id');
        
        if ($gameId) {
            $this->attachGameIdToNotification($user, $notification);
        }
    }
    
    /**
     * Platform geneli bildirim gönder
     * 
     * Cross-game bildirim - game_id null kalır
     *
     * @param User $user
     * @param mixed $notification
     * @return void
     */
    public function sendCrossGameNotification(User $user, $notification): void
    {
        // game_id eklenmez - null kalır (cross-game)
        $user->notify($notification);
    }
}
