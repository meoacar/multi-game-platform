<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Game;
use App\Models\MatchmakingQueue;
use App\Models\MatchmakingMatch;
use App\Models\MatchmakingHistory;
use App\Services\MatchmakingService;
use App\Services\MatchmakingAlgorithm;
use App\Services\XpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * MatchmakingService Unit Tests
 * 
 * Requirements: 1.1, 1.2, 1.4, 4.2
 * 
 * Test Senaryoları:
 * - joinQueue() - Kuyruğa ekleme
 * - findMatches() - Eşleşme arama
 * - acceptMatch() - Eşleşmeyi kabul etme
 * - cleanupExpiredQueues() - Süresi dolmuş kuyrukları temizleme
 */
class MatchmakingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MatchmakingService $service;
    protected MatchmakingAlgorithm $algorithm;
    protected XpService $xpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->algorithm = new MatchmakingAlgorithm();
        $this->xpService = new XpService();
        $this->service = new MatchmakingService($this->algorithm, $this->xpService);
    }

    // ==========================================
    // joinQueue() Testleri
    // ==========================================

    /** @test */
    public function join_queue_creates_queue_with_searching_status()
    {
        // Kullanıcı ve oyun oluştur
        $user = User::factory()->create();
        $user->profile()->create([
            'bio' => 'Test bio',
            'rank' => 'Gold',
        ]);
        
        $game = Game::factory()->create();

        $preferences = [
            'game_id' => $game->id,
            'mode' => 'Squad',
            'min_rank' => 'Gold',
            'max_rank' => 'Platinum',
            'city' => 'Istanbul',
            'microphone_required' => true,
            'play_style' => 'Aggressive',
        ];

        // Kuyruğa ekle
        $queue = $this->service->joinQueue($user, $preferences);

        // Kontroller
        $this->assertInstanceOf(MatchmakingQueue::class, $queue);
        $this->assertEquals('searching', $queue->status);
        $this->assertEquals($user->id, $queue->user_id);
        $this->assertEquals($game->id, $queue->game_id);
        $this->assertEquals('Squad', $queue->mode);
        $this->assertEquals('Gold', $queue->min_rank);
        $this->assertEquals('Platinum', $queue->max_rank);
        $this->assertEquals('Istanbul', $queue->city);
        $this->assertTrue($queue->microphone_required);
        $this->assertEquals('Aggressive', $queue->play_style);
        $this->assertNotNull($queue->expires_at);
    }

    /** @test */
    public function join_queue_throws_exception_if_user_already_in_queue()
    {
        $user = User::factory()->create();
        $user->profile()->create(['bio' => 'Test']);
        $game = Game::factory()->create();

        $preferences = [
            'game_id' => $game->id,
            'mode' => 'Squad',
        ];

        // İlk kuyruğa ekleme
        $this->service->joinQueue($user, $preferences);

        // İkinci kuyruğa ekleme denemesi
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Zaten kuyrukta bekliyorsunuz');
        
        $this->service->joinQueue($user, $preferences);
    }

    /** @test */
    public function join_queue_throws_exception_if_user_has_no_profile()
    {
        $user = User::factory()->create();
        // Profil oluşturma
        
        $game = Game::factory()->create();

        $preferences = [
            'game_id' => $game->id,
            'mode' => 'Squad',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Lütfen profilinizi tamamlayın');
        
        $this->service->joinQueue($user, $preferences);
    }

    /** @test */
    public function join_queue_sets_expiration_time_to_5_minutes()
    {
        $user = User::factory()->create();
        $user->profile()->create(['bio' => 'Test']);
        $game = Game::factory()->create();

        $preferences = [
            'game_id' => $game->id,
            'mode' => 'Duo',
        ];

        $queue = $this->service->joinQueue($user, $preferences);

        // Expires_at yaklaşık 5 dakika sonra olmalı (4.5 - 5.5 dakika arası)
        $expectedExpiration = now()->addMinutes(5);
        $diff = abs($queue->expires_at->diffInSeconds($expectedExpiration));
        
        $this->assertLessThan(60, $diff); // 1 dakikadan az fark olmalı
    }

    // ==========================================
    // leaveQueue() Testleri
    // ==========================================

    /** @test */
    public function leave_queue_cancels_active_queue()
    {
        $user = User::factory()->create();
        $user->profile()->create(['bio' => 'Test']);
        $game = Game::factory()->create();

        // Kuyruğa ekle
        $queue = $this->service->joinQueue($user, [
            'game_id' => $game->id,
            'mode' => 'Squad',
        ]);

        // Kuyruktan çık
        $result = $this->service->leaveQueue($user);

        $this->assertTrue($result);
        
        $queue->refresh();
        $this->assertEquals('cancelled', $queue->status);
    }

    /** @test */
    public function leave_queue_returns_false_if_no_active_queue()
    {
        $user = User::factory()->create();

        $result = $this->service->leaveQueue($user);

        $this->assertFalse($result);
    }

    // ==========================================
    // findMatches() Testleri
    // ==========================================

    /** @test */
    public function find_matches_creates_match_for_compatible_players()
    {
        $game = Game::factory()->create();
        
        // 4 uyumlu oyuncu oluştur
        $users = User::factory()->count(4)->create();
        foreach ($users as $user) {
            $user->profile()->create(['bio' => 'Test', 'rank' => 'Gold']);
            
            MatchmakingQueue::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'mode' => 'Squad',
                'min_rank' => 'Gold',
                'max_rank' => 'Platinum',
                'city' => 'Istanbul',
                'microphone_required' => true,
                'play_style' => 'Aggressive',
                'status' => 'searching',
                'expires_at' => now()->addMinutes(5),
                'search_attempts' => 0,
            ]);
        }

        // Eşleşme ara
        $matchesCreated = $this->service->findMatches();

        // Kontroller
        $this->assertEquals(1, $matchesCreated);
        
        // Eşleşme oluşturulmuş mu?
        $match = MatchmakingMatch::first();
        $this->assertNotNull($match);
        $this->assertEquals('Squad', $match->mode);
        $this->assertEquals(4, count($match->user_ids));
        $this->assertEquals('pending', $match->status);
        
        // Kuyruklar matched durumuna geçmiş mi?
        $matchedQueues = MatchmakingQueue::where('status', 'matched')->count();
        $this->assertEquals(4, $matchedQueues);
    }

    /** @test */
    public function find_matches_returns_zero_if_no_active_queues()
    {
        $matchesCreated = $this->service->findMatches();

        $this->assertEquals(0, $matchesCreated);
    }

    /** @test */
    public function find_matches_increments_search_attempts()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();
        $user->profile()->create(['bio' => 'Test']);
        
        $queue = MatchmakingQueue::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'mode' => 'Squad',
            'status' => 'searching',
            'expires_at' => now()->addMinutes(5),
            'search_attempts' => 0,
        ]);

        // Eşleşme ara (yeterli oyuncu yok, eşleşme oluşmaz)
        $this->service->findMatches();

        $queue->refresh();
        $this->assertEquals(1, $queue->search_attempts);
    }


    // ==========================================
    // acceptMatch() Testleri
    // ==========================================

    /** @test */
    public function accept_match_marks_user_as_accepted()
    {
        $game = Game::factory()->create();
        $users = User::factory()->count(2)->create();
        
        $userIds = $users->pluck('id')->toArray();

        // Eşleşme oluştur
        $match = MatchmakingMatch::create([
            'game_id' => $game->id,
            'mode' => 'Duo',
            'user_ids' => $userIds,
            'compatibility_score' => 80,
            'status' => 'pending',
            'acceptance_status' => [],
            'expires_at' => now()->addSeconds(30),
        ]);

        // İlk kullanıcı kabul eder
        $result = $this->service->acceptMatch($match, $users[0]);

        $this->assertTrue($result);
        
        $match->refresh();
        $this->assertEquals('accepted', $match->getUserAcceptanceStatus($users[0]->id));
    }

    /** @test */
    public function accept_match_completes_when_all_users_accept()
    {
        $game = Game::factory()->create();
        $users = User::factory()->count(2)->create();
        
        $createdAt = now()->subMinutes(2);
        
        foreach ($users as $user) {
            $user->profile()->create(['bio' => 'Test']);
            
            $queue = MatchmakingQueue::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'mode' => 'Duo',
                'status' => 'matched',
                'expires_at' => now()->addMinutes(5),
                'search_attempts' => 0,
            ]);
            
            // created_at'i manuel olarak ayarla
            $queue->created_at = $createdAt;
            $queue->save();
        }
        
        $userIds = $users->pluck('id')->toArray();

        $match = MatchmakingMatch::create([
            'game_id' => $game->id,
            'mode' => 'Duo',
            'user_ids' => $userIds,
            'compatibility_score' => 80,
            'status' => 'pending',
            'acceptance_status' => [],
            'expires_at' => now()->addSeconds(30),
        ]);

        // Her iki kullanıcı da kabul eder
        $this->service->acceptMatch($match, $users[0]);
        $this->service->acceptMatch($match, $users[1]);

        $match->refresh();
        
        // Eşleşme tamamlanmış olmalı
        $this->assertEquals('accepted', $match->status);
        
        // Geçmiş kayıtları oluşturulmuş olmalı
        $historyCount = MatchmakingHistory::whereIn('user_id', $userIds)->count();
        $this->assertEquals(2, $historyCount);
    }

    /** @test */
    public function accept_match_throws_exception_if_user_not_in_match()
    {
        $game = Game::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = MatchmakingMatch::create([
            'game_id' => $game->id,
            'mode' => 'Duo',
            'user_ids' => [$user1->id],
            'compatibility_score' => 80,
            'status' => 'pending',
            'acceptance_status' => [],
            'expires_at' => now()->addSeconds(30),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bu eşleşmede yer almıyorsunuz');
        
        $this->service->acceptMatch($match, $user2);
    }

    /** @test */
    public function accept_match_throws_exception_if_match_expired()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();

        $match = MatchmakingMatch::create([
            'game_id' => $game->id,
            'mode' => 'Solo',
            'user_ids' => [$user->id],
            'compatibility_score' => 80,
            'status' => 'pending',
            'acceptance_status' => [],
            'expires_at' => now()->subSeconds(10), // Süresi dolmuş
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bu eşleşme artık aktif değil');
        
        $this->service->acceptMatch($match, $user);
    }

    // ==========================================
    // rejectMatch() Testleri
    // ==========================================

    /** @test */
    public function reject_match_cancels_match()
    {
        $game = Game::factory()->create();
        $users = User::factory()->count(2)->create();
        
        foreach ($users as $user) {
            MatchmakingQueue::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'mode' => 'Duo',
                'status' => 'matched',
                'expires_at' => now()->addMinutes(5),
            ]);
        }
        
        $userIds = $users->pluck('id')->toArray();

        $match = MatchmakingMatch::create([
            'game_id' => $game->id,
            'mode' => 'Duo',
            'user_ids' => $userIds,
            'compatibility_score' => 80,
            'status' => 'pending',
            'acceptance_status' => [],
            'expires_at' => now()->addSeconds(30),
        ]);

        // Kullanıcı reddeder
        $result = $this->service->rejectMatch($match, $users[0]);

        $this->assertTrue($result);
        
        $match->refresh();
        $this->assertEquals('rejected', $match->status);
        
        // Kuyruklar geri searching durumuna dönmüş olmalı
        $searchingQueues = MatchmakingQueue::where('status', 'searching')->count();
        $this->assertEquals(2, $searchingQueues);
    }

    // ==========================================
    // cleanupExpiredQueues() Testleri
    // ==========================================

    /** @test */
    public function cleanup_expired_queues_marks_expired_queues()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();

        // Süresi dolmuş kuyruk oluştur
        $expiredQueue = MatchmakingQueue::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'mode' => 'Squad',
            'status' => 'searching',
            'expires_at' => now()->subMinutes(1), // 1 dakika önce dolmuş
        ]);

        // Temizle
        $count = $this->service->cleanupExpiredQueues();

        $this->assertEquals(1, $count);
        
        $expiredQueue->refresh();
        $this->assertEquals('expired', $expiredQueue->status);
        
        // Geçmiş kaydı oluşturulmuş olmalı
        $history = MatchmakingHistory::where('user_id', $user->id)->first();
        $this->assertNotNull($history);
        $this->assertEquals('timeout', $history->result);
    }

    /** @test */
    public function cleanup_expired_queues_ignores_active_queues()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();

        // Aktif kuyruk oluştur
        MatchmakingQueue::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'mode' => 'Squad',
            'status' => 'searching',
            'expires_at' => now()->addMinutes(5), // Henüz dolmamış
        ]);

        // Temizle
        $count = $this->service->cleanupExpiredQueues();

        $this->assertEquals(0, $count);
        
        // Kuyruk hala searching durumunda olmalı
        $queue = MatchmakingQueue::first();
        $this->assertEquals('searching', $queue->status);
    }

    /** @test */
    public function cleanup_expired_queues_returns_zero_if_no_expired_queues()
    {
        $count = $this->service->cleanupExpiredQueues();

        $this->assertEquals(0, $count);
    }

    // ==========================================
    // cleanupExpiredMatches() Testleri
    // ==========================================

    /** @test */
    public function cleanup_expired_matches_cancels_expired_matches()
    {
        $game = Game::factory()->create();
        $users = User::factory()->count(2)->create();
        
        $createdAt = now()->subMinutes(2);
        
        foreach ($users as $user) {
            $queue = MatchmakingQueue::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'mode' => 'Duo',
                'status' => 'matched',
                'expires_at' => now()->addMinutes(5),
                'search_attempts' => 0,
            ]);
            
            // created_at'i manuel olarak ayarla
            $queue->created_at = $createdAt;
            $queue->save();
        }
        
        $userIds = $users->pluck('id')->toArray();

        // Süresi dolmuş eşleşme oluştur
        $expiredMatch = MatchmakingMatch::create([
            'game_id' => $game->id,
            'mode' => 'Duo',
            'user_ids' => $userIds,
            'compatibility_score' => 80,
            'status' => 'pending',
            'acceptance_status' => [],
            'expires_at' => now()->subSeconds(10), // 10 saniye önce dolmuş
        ]);

        // Temizle
        $count = $this->service->cleanupExpiredMatches();

        $this->assertEquals(1, $count);
        
        $expiredMatch->refresh();
        $this->assertEquals('timeout', $expiredMatch->status);
        
        // Kuyruklar geri searching durumuna dönmüş olmalı
        $searchingQueues = MatchmakingQueue::where('status', 'searching')->count();
        $this->assertEquals(2, $searchingQueues);
    }

    // ==========================================
    // Yardımcı Metodlar Testleri
    // ==========================================

    /** @test */
    public function get_user_active_queue_returns_active_queue()
    {
        $user = User::factory()->create();
        $user->profile()->create(['bio' => 'Test']);
        $game = Game::factory()->create();

        $queue = $this->service->joinQueue($user, [
            'game_id' => $game->id,
            'mode' => 'Squad',
        ]);

        $activeQueue = $this->service->getUserActiveQueue($user);

        $this->assertNotNull($activeQueue);
        $this->assertEquals($queue->id, $activeQueue->id);
    }

    /** @test */
    public function get_user_success_rate_calculates_correctly()
    {
        $user = User::factory()->create();

        // 3 başarılı, 1 başarısız eşleşme
        MatchmakingHistory::create([
            'user_id' => $user->id,
            'result' => 'completed',
            'wait_time_seconds' => 60,
            'compatibility_score' => 80,
        ]);
        
        MatchmakingHistory::create([
            'user_id' => $user->id,
            'result' => 'completed',
            'wait_time_seconds' => 120,
            'compatibility_score' => 85,
        ]);
        
        MatchmakingHistory::create([
            'user_id' => $user->id,
            'result' => 'completed',
            'wait_time_seconds' => 90,
            'compatibility_score' => 75,
        ]);
        
        MatchmakingHistory::create([
            'user_id' => $user->id,
            'result' => 'timeout',
            'wait_time_seconds' => 300,
            'compatibility_score' => 0,
        ]);

        $successRate = $this->service->getUserSuccessRate($user);

        // 3/4 = 75%
        $this->assertEquals(75.0, $successRate);
    }

    /** @test */
    public function get_user_average_wait_time_calculates_correctly()
    {
        $user = User::factory()->create();

        // 3 başarılı eşleşme
        MatchmakingHistory::create([
            'user_id' => $user->id,
            'result' => 'completed',
            'wait_time_seconds' => 60,
            'compatibility_score' => 80,
        ]);
        
        MatchmakingHistory::create([
            'user_id' => $user->id,
            'result' => 'completed',
            'wait_time_seconds' => 120,
            'compatibility_score' => 85,
        ]);
        
        MatchmakingHistory::create([
            'user_id' => $user->id,
            'result' => 'completed',
            'wait_time_seconds' => 90,
            'compatibility_score' => 75,
        ]);

        $avgWaitTime = $this->service->getUserAverageWaitTime($user);

        // (60 + 120 + 90) / 3 = 90
        $this->assertEquals(90.0, $avgWaitTime);
    }
}
