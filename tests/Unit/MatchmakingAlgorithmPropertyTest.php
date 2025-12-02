<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\MatchmakingQueue;
use App\Models\User;
use App\Services\MatchmakingAlgorithm;
use Eris\Generator;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * MatchmakingAlgorithm Property-Based Tests
 * 
 * Feature: matchmaking
 * Properties:
 * - Property 4: Uyumluluk skoru 0-100 arasında
 * - Property 5: Minimum eşik 60
 * - Property 6: Rank uyumluluğu
 * 
 * Validates: Requirements 3.1, 3.2, 3.5
 */
class MatchmakingAlgorithmPropertyTest extends TestCase
{
    use RefreshDatabase, TestTrait;

    protected MatchmakingAlgorithm $algorithm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->algorithm = new MatchmakingAlgorithm();
    }
    
    /**
     * Test için oyun oluştur veya mevcut oyunu getir
     */
    private function getTestGame(): Game
    {
        return Game::firstOrCreate(
            ['slug' => 'pubg-mobile'],
            ['name' => 'PUBG Mobile', 'is_active' => true]
        );
    }

    /**
     * Feature: matchmaking, Property 4: Uyumluluk skoru 0-100 arasında
     * 
     * Property: For any iki oyuncu, uyumluluk skoru 0-100 arasında olmalıdır.
     * 
     * Validates: Requirements 3.1
     * 
     * @test
     */
    public function compatibility_score_is_between_0_and_100()
    {
        $this->forAll(
            Generator\elements(['Squad', 'Duo', 'Solo']),
            Generator\elements(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror']),
            Generator\elements(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror']),
            Generator\oneOf(Generator\constant(null), Generator\elements(['Aggressive', 'Balanced', 'Defensive', 'Sniper'])),
            Generator\oneOf(Generator\constant(null), Generator\elements(['Aggressive', 'Balanced', 'Defensive', 'Sniper'])),
            Generator\oneOf(Generator\constant(null), Generator\elements(['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya'])),
            Generator\oneOf(Generator\constant(null), Generator\elements(['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya'])),
            Generator\bool(),
            Generator\bool(),
            Generator\choose(0, 10),
            Generator\choose(0, 10)
        )
        ->then(function ($mode, $rank1, $rank2, $playStyle1, $playStyle2, $city1, $city2, $mic1, $mic2, $attempts1, $attempts2) {
            $game = $this->getTestGame();
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $queue1 = MatchmakingQueue::create([
                'user_id' => $user1->id,
                'game_id' => $game->id,
                'mode' => $mode,
                'min_rank' => $rank1,
                'max_rank' => $rank1,
                'city' => $city1,
                'microphone_required' => $mic1,
                'play_style' => $playStyle1,
                'status' => 'searching',
                'expires_at' => now()->addMinutes(5),
                'search_attempts' => $attempts1,
            ]);

            $queue2 = MatchmakingQueue::create([
                'user_id' => $user2->id,
                'game_id' => $game->id,
                'mode' => $mode,
                'min_rank' => $rank2,
                'max_rank' => $rank2,
                'city' => $city2,
                'microphone_required' => $mic2,
                'play_style' => $playStyle2,
                'status' => 'searching',
                'expires_at' => now()->addMinutes(5),
                'search_attempts' => $attempts2,
            ]);

            $score = $this->algorithm->calculateTotalScore($queue1, $queue2);

            $this->assertGreaterThanOrEqual(0, $score, "Compatibility score cannot be negative. Got: {$score}");
            $this->assertLessThanOrEqual(100, $score, "Compatibility score cannot exceed 100. Got: {$score}");
        });
    }

    /**
     * Feature: matchmaking, Property 5: Minimum eşik 60
     * 
     * @test
     */
    public function minimum_compatibility_threshold_is_60()
    {
        $minScore = $this->algorithm->getMinCompatibilityScore();
        $this->assertEquals(60, $minScore, "Minimum compatibility threshold must be 60. Got: {$minScore}");
    }

    /**
     * Feature: matchmaking, Property 6: Rank uyumluluğu
     * 
     * @test
     */
    public function rank_compatibility_respects_rank_range()
    {
        $this->forAll(
            Generator\elements(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace']),
            Generator\choose(0, 3)
        )
        ->then(function ($minRank, $rankDifference) {
            $game = $this->getTestGame();
            
            $rankOrder = ['Bronze' => 1, 'Silver' => 2, 'Gold' => 3, 'Platinum' => 4, 'Diamond' => 5, 'Crown' => 6, 'Ace' => 7, 'Conqueror' => 8];
            $rankNames = array_keys($rankOrder);
            $minRankIndex = $rankOrder[$minRank] - 1;
            $maxRankIndex = min($minRankIndex + $rankDifference, count($rankNames) - 1);
            $maxRank = $rankNames[$maxRankIndex];

            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            $queue1 = MatchmakingQueue::create([
                'user_id' => $user1->id,
                'game_id' => $game->id,
                'mode' => 'Squad',
                'min_rank' => $minRank,
                'max_rank' => $maxRank,
                'city' => 'İstanbul',
                'microphone_required' => true,
                'play_style' => 'Aggressive',
                'status' => 'searching',
                'expires_at' => now()->addMinutes(5),
                'search_attempts' => 0,
            ]);

            $targetRankIndex = $minRankIndex + (int)($rankDifference / 2);
            $targetRank = $rankNames[$targetRankIndex];

            $queue2 = MatchmakingQueue::create([
                'user_id' => $user2->id,
                'game_id' => $game->id,
                'mode' => 'Squad',
                'min_rank' => $targetRank,
                'max_rank' => $targetRank,
                'city' => 'İstanbul',
                'microphone_required' => true,
                'play_style' => 'Aggressive',
                'status' => 'searching',
                'expires_at' => now()->addMinutes(5),
                'search_attempts' => 0,
            ]);

            $score = $this->algorithm->calculateTotalScore($queue1, $queue2);
            $this->assertGreaterThan(0, $score, "Score should be > 0 when rank is within range");
        });
    }
}
