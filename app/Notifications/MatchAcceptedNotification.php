<?php

namespace App\Notifications;

use App\Models\MatchmakingMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Eşleşme Kabul Edildi Bildirimi
 * 
 * Tüm oyuncular eşleşmeyi kabul ettiğinde gönderilir.
 * Oyuncular artık birbirleriyle iletişime geçebilir.
 */
class MatchAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public MatchmakingMatch $match
    ) {}

    /**
     * Bildirim kanalları
     * Database ve push notification kullanılır
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Veritabanı için bildirim verisi
     */
    public function toArray(object $notifiable): array
    {
        $userIds = $this->match->user_ids;
        $playerCount = count($userIds);
        
        return [
            'type' => 'match_accepted',
            'title' => 'Eşleşme Tamamlandı!',
            'message' => "Tüm oyuncular kabul etti. Artık takım arkadaşlarınızla iletişime geçebilirsiniz.",
            'match_id' => $this->match->id,
            'mode' => $this->match->mode,
            'player_count' => $playerCount,
            'compatibility_score' => $this->match->compatibility_score,
            'user_ids' => $userIds,
            'url' => route('matchmaking.history'),
            'icon' => 'check-circle',
            'success' => true,
        ];
    }
}
