<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\XpEvent;
use App\Services\XpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessXpEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Job çalıştırma denemeleri
     */
    public $tries = 3;

    /**
     * Job timeout süresi (saniye)
     */
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $eventType,
        public int $points,
        public ?array $meta = null
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(XpService $xpService): void
    {
        // XP event oluştur
        $xpEvent = XpEvent::create([
            'user_id' => $this->user->id,
            'type' => $this->eventType,
            'points' => $this->points,
            'meta' => $this->meta,
        ]);

        // Kullanıcı toplam XP'sini güncelle
        $this->user->increment('xp_total', $this->points);

        // Badge kontrolü
        $xpService->checkAndUnlockBadges($this->user);

        // XP eşik kontrolü (level up)
        $oldLevel = $xpService->calculateLevel($this->user->xp_total - $this->points);
        $newLevel = $xpService->calculateLevel($this->user->xp_total);

        // Level atladıysa bildirim gönder
        if ($newLevel > $oldLevel) {
            SendNotificationJob::dispatch(
                $this->user,
                'level_up',
                [
                    'old_level' => $oldLevel,
                    'new_level' => $newLevel,
                    'message' => "Tebrikler! {$newLevel}. seviyeye ulaştınız!",
                ]
            );
        }
    }

    /**
     * Job başarısız olduğunda
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('XP işleme başarısız: ' . $exception->getMessage(), [
            'user_id' => $this->user->id,
            'event_type' => $this->eventType,
            'points' => $this->points,
        ]);
    }
}
