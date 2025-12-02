<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Game;
use App\Models\MatchmakingQueue;
use App\Services\MatchmakingService;
use App\Services\MatchmakingAlgorithm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Matchmaking Queue Property-Based Tests
 * 
 * **Feature: matchmaking, Property 1: Kuyruk ekleme status**
 * **Feature: matchmaking, Property 3: Zaman aşımı**
 * 
 * Requirements: 1.1, 1.4
 */
class MatchmakingQueuePropertyTest extends TestCase
{
    use RefreshDatabase;

    protected MatchmakingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MatchmakingService(new MatchmakingAlgorithm());
    }

    /**
     * Property 1: Kuyruk ekleme status
     * 
     * For any kullanıcı, kuyruğa eklendiğinde status "searching" olmalıdır
     * 
     * **Validates: Requirements 1.1**
     * 
     * @test
     */
    public function queue_status_is_searching_when_user_joins()
    {
        // Tek bir game oluştur (slug unique olduğu için)
        $game = Game::factory()->create();
        
        // 10 farklı kullanıcı ve tercih kombinasyonu test et
        for ($i = 0; $i < 10; $i++) {
            $user = User::factory()->create();
            $user->profile()->create(['bio' => 'Test bio']);

            $modes = ['Squad', 'Duo', 'Solo'];
            $ranks = ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond'];
            $cities = ['Istanbul', 'Ankara', 'Izmir', null];
            $playStyles = ['Aggressive', 'Defensive', 'Balanced', null];

            $preferences = [
                'game_id' => $game->id,
                'mode' => $modes[array_rand($modes)],
                'min_rank' => $ranks[array_rand($ranks)],
                'max_rank' => $ranks[array_rand($ranks)],
                'city' => $cities[array_rand($cities)],
                'microphone_required' => (bool) rand(0, 1),
                'play_style' => $playStyles[array_rand($playStyles)],
            ];

            $queue = $this->service->joinQueue($user, $preferences);

            // Property: Status her zaman "searching" olmalı
            $this->assertEquals('searching', $queue->status);
            $this->assertNotNull($queue->expires_at);
            $this->assertTrue($queue->expires_at->isFuture());
        }
    }

    /**
     * Property 3: Zaman aşımı
     * 
     * For any kuyruk, 5 dakika sonra otomatik iptal edilmelidir
     * 
     * **Validates: Requirements 1.4**
     * 
     * @test
     */
    public function queue_expires_after_5_minutes()
    {
        // Tek bir game oluştur
        $game = Game::factory()->create();
        
        // 5 farklı kuyruk oluştur
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();

            // Süresi dolmuş kuyruk oluştur
            $queue = MatchmakingQueue::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'mode' => 'Squad',
                'status' => 'searching',
                'expires_at' => now()->subMinutes(rand(1, 10)), // 1-10 dakika önce dolmuş
                'search_attempts' => 0,
            ]);

            // Property: Süresi dolmuş kuyruklar expired olarak işaretlenmeli
            $this->assertTrue($queue->isExpired());
            $this->assertFalse($queue->isActive());
        }

        // Cleanup çalıştır
        $count = $this->service->cleanupExpiredQueues();

        // Property: Tüm süresi dolmuş kuyruklar temizlenmeli
        $this->assertEquals(5, $count);

        // Property: Temizlenen kuyrukların status'u "expired" olmalı
        $expiredQueues = MatchmakingQueue::where('status', 'expired')->count();
        $this->assertEquals(5, $expiredQueues);
    }
}
