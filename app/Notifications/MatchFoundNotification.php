<?php

namespace App\Notifications;

use App\Models\MatchmakingMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Eşleşme Bulundu Bildirimi
 * 
 * Kullanıcı için uygun bir eşleşme bulunduğunda gönderilir.
 * Kullanıcının 30 saniye içinde kabul/red kararı vermesi gerekir.
 */
class MatchFoundNotification extends Notification
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
            'type' => 'match_found',
            'title' => 'Eşleşme Bulundu!',
            'message' => "{$playerCount} oyunculu bir eşleşme bulundu. 30 saniye içinde kabul edin.",
            'match_id' => $this->match->id,
            'mode' => $this->match->mode,
            'player_count' => $playerCount,
            'compatibility_score' => $this->match->compatibility_score,
            'expires_at' => $this->match->expires_at->toIso8601String(),
            'url' => route('matchmaking.index'),
            'icon' => 'users',
            'action_required' => true,
        ];
    }
}
