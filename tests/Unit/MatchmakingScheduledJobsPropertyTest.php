<?php

namespace Tests\Unit;

use App\Jobs\FindMatchesJob;
use App\Jobs\CleanupExpiredQueuesJob;
use App\Jobs\CleanupExpiredMatchesJob;
use App\Models\User;
use App\Models\Game;
use App\Models\MatchmakingQueue;
use App\Models\MatchmakingMatch;
use App\Services\MatchmakingService;
use App\Services\MatchmakingAlgorithm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Matchmaking Scheduled Jobs Property-Based Tests
 * 
 * **Feature: matchmaking, Property 2: Eşleşme arama periyodu**
 * 
 * Requirements: 1.2
 */
class MatchmakingScheduledJobsPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected MatchmakingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MatchmakingService(new MatchmakingAlgorithm());
    }

    /**
     * Property 2: Eşleşme arama periyodu
     * 
     * For any kuyrukta bekleyen kullanıcı, sistem her 10 saniyede bir eşleşme aramalıdır
     * 
     * **Validates: Requirements 1.2**
     * 
     * @test
     */
    public function find_matches_job_searches_for_matches_periodically()
    {
        // Tek bir game oluştur
        $game = Game::factory()->create();
        
        // 10 farklı senaryo test et
        for ($iteration = 0; $iteration < 10; $iteration++) {
            // Her iterasyonda farklı sayıda kullanıcı oluştur (2-8 arası)
            $userCount = rand(2, 8);
            $users = [];
            
            for ($i = 0; $i < $userCount; $i++) {
                $user = User::factory()->create();
                $user->profile()->create([
                    'bio' => 'Test bio',
                    'rank' => 'Gold',
                    'play_style' => 'Balanced',
                ]);
                $users[] = $user;
                
                // Kuyruğa ekle
                $this->service->joinQueue($user, [
                    'game_id' => $game->id,
                    'mode' => 'Squad',
                    'min_rank' => 'Silver',
                    'max_rank' => 'Platinum',
                    'city' => 'Istanbul',
                    'microphone_required' => false,
                    'play_style' => 'Balanced',
                ]);
            }

            // Başlangıçta kuyrukta bekleyen kullanıcı sayısı
            $initialQueueCount = MatchmakingQueue::active()->count();
            $this->assertEquals($userCount, $initialQueueCount);

            // FindMatchesJob'ı çalıştır (scheduled job simülasyonu)
            $job = new FindMatchesJob();
            $job->handle($this->service);

            // Property: Job çalıştıktan sonra eşleşme arama yapılmalı
            // Eğer yeterli kullanıcı varsa (4+), eşleşme oluşturulmalı
            if ($userCount >= 4) {
                $matches = MatchmakingMatch::where('game_id', $game->id)->count();
                $this->assertGreaterThanOrEqual(1, $matches, 
                    "Iteration {$iteration}: {$userCount} kullanıcı ile en az 1 eşleşme oluşturulmalı");
            }

            // Property: search_attempts her job çalıştırmasında artmalı
            $queues = MatchmakingQueue::whereIn('user_id', collect($users)->pluck('id'))->get();
            foreach ($queues as $queue) {
                if ($queue->status === 'searching') {
                    $this->assertGreaterThanOrEqual(1, $queue->search_attempts,
                        "Iteration {$iteration}: search_attempts artırılmalı");
                }
            }

            // Temizlik: Bu iterasyonun verilerini sil
            MatchmakingQueue::whereIn('user_id', collect($users)->pluck('id'))->delete();
            MatchmakingMatch::where('game_id', $game->id)->delete();
            foreach ($users as $user) {
                $user->profile()->delete();
                $user->delete();
            }
        }
    }




}
