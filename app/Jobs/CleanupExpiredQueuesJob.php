<?php

namespace App\Jobs;

use App\Services\MatchmakingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * CleanupExpiredQueuesJob
 * 
 * Her dakika çalışarak süresi dolmuş kuyrukları temizler
 * 
 * Requirements: 1.4
 */
class CleanupExpiredQueuesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Job çalıştırma denemeleri
     */
    public $tries = 3;

    /**
     * Job timeout süresi (saniye)
     */
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('matchmaking');
    }

    /**
     * Execute the job.
     */
    public function handle(MatchmakingService $matchmakingService): void
    {
        try {
            $cleanedCount = $matchmakingService->cleanupExpiredQueues();

            Log::info('CleanupExpiredQueuesJob completed', [
                'cleaned_count' => $cleanedCount,
                'timestamp' => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            Log::error('CleanupExpiredQueuesJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Job başarısız olduğunda
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupExpiredQueuesJob permanently failed', [
            'error' => $exception->getMessage(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
