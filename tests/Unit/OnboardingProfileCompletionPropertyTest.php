<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\OnboardingService;
use Eris\Generator;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * OnboardingService Property-Based Tests - Profile Completion
 * 
 * Feature: user-onboarding, Property 25: Profile completion hesaplama
 * Validates: Requirements 9.5
 * 
 * Bu test, profil tamamlanma yüzdesinin doldurulmuş alan sayısına göre
 * doğru hesaplandığını property-based testing ile doğrular.
 */
class OnboardingProfileCompletionPropertyTest extends TestCase
{
    use RefreshDatabase, TestTrait;

    protected OnboardingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OnboardingService();
    }

    /**
     * Feature: user-onboarding, Property 25: Profile completion hesaplama
     * 
     * Property: For any kullanıcı, profile completion yüzdesi doldurulmuş 
     * alan sayısına göre doğru hesaplanmalıdır.
     * 
     * Validates: Requirements 9.5
     * 
     * @test
     */
    public function profile_completion_is_calculated_correctly_based_on_filled_fields()
    {
        $this->forAll(
            // PUBG ID - opsiyonel string (max 50 karakter)
            Generator\oneOf(
                Generator\constant(null),
                Generator\bind(
                    Generator\string(),
                    function($str) {
                        return Generator\constant(substr($str, 0, 50));
                    }
                )
            ),
            // Player level - opsiyonel 1-100 arası
            Generator\oneOf(
                Generator\constant(null),
                Generator\choose(1, 100)
            ),
            // Player tier - opsiyonel enum
            Generator\oneOf(
                Generator\constant(null),
                Generator\elements(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror'])
            ),
            // Main server - opsiyonel enum
            Generator\oneOf(
                Generator\constant(null),
                Generator\elements(['Europe', 'Asia', 'America'])
            ),
            // Favorite mode - opsiyonel enum
            Generator\oneOf(
                Generator\constant(null),
                Generator\elements(['TPP', 'FPP'])
            ),
            // Favorite type - opsiyonel enum
            Generator\oneOf(
                Generator\constant(null),
                Generator\elements(['Solo', 'Duo', 'Squad'])
            ),
            // Active hours - opsiyonel array
            Generator\oneOf(
                Generator\constant(null),
                Generator\constant([]),
                Generator\seq(Generator\elements(['morning', 'afternoon', 'evening', 'night']))
            ),
            // Interests - opsiyonel array
            Generator\oneOf(
                Generator\constant(null),
                Generator\constant([]),
                Generator\seq(Generator\elements(['lfg', 'clan', 'tournament', 'social']))
            ),
            // Push enabled - boolean (her zaman sayılır)
            Generator\bool()
        )
        ->then(function (
            $pubgId,
            $playerLevel,
            $playerTier,
            $mainServer,
            $favoriteMode,
            $favoriteType,
            $activeHours,
            $interests,
            $pushEnabled
        ) {
            // Kullanıcı oluştur
            $user = User::factory()->create([
                'pubg_id' => $pubgId,
                'player_level' => $playerLevel,
                'player_tier' => $playerTier,
                'main_server' => $mainServer,
                'favorite_mode' => $favoriteMode,
                'favorite_type' => $favoriteType,
                'active_hours' => $activeHours,
                'interests' => $interests,
                'push_enabled' => $pushEnabled,
            ]);

            // Profil tamamlanma yüzdesini hesapla
            $completion = $this->service->calculateProfileCompletion($user);

            // Manuel olarak doldurulmuş alan sayısını hesapla
            $filledCount = 0;
            $totalFields = 9; // Toplam kontrol edilen alan sayısı

            // pubg_id
            if (!empty($pubgId)) {
                $filledCount++;
            }

            // player_level
            if (!empty($playerLevel)) {
                $filledCount++;
            }

            // player_tier
            if (!empty($playerTier)) {
                $filledCount++;
            }

            // main_server
            if (!empty($mainServer)) {
                $filledCount++;
            }

            // favorite_mode
            if (!empty($favoriteMode)) {
                $filledCount++;
            }

            // favorite_type
            if (!empty($favoriteType)) {
                $filledCount++;
            }

            // active_hours (array - boş değilse sayılır)
            if (!empty($activeHours) && is_array($activeHours) && count($activeHours) > 0) {
                $filledCount++;
            }

            // interests (array - boş değilse sayılır)
            if (!empty($interests) && is_array($interests) && count($interests) > 0) {
                $filledCount++;
            }

            // push_enabled (her zaman sayılır - true veya false)
            $filledCount++;

            // Beklenen yüzdeyi hesapla
            $expectedCompletion = (int) round(($filledCount / $totalFields) * 100);

            // Property kontrolü: Hesaplanan değer beklenen değere eşit olmalı
            $this->assertEquals(
                $expectedCompletion,
                $completion,
                "Profile completion mismatch. Expected: {$expectedCompletion}%, Got: {$completion}%. " .
                "Filled fields: {$filledCount}/{$totalFields}"
            );

            // Ek kontroller: Yüzde 0-100 arasında olmalı
            $this->assertGreaterThanOrEqual(0, $completion, 'Profile completion cannot be negative');
            $this->assertLessThanOrEqual(100, $completion, 'Profile completion cannot exceed 100%');
        });
    }

    /**
     * Property: Hiç alan doldurulmamışsa (push_enabled hariç), 
     * profile completion minimum değerde olmalıdır.
     * 
     * @test
     */
    public function profile_completion_is_minimum_when_no_fields_filled()
    {
        $this->forAll(
            Generator\bool() // Sadece push_enabled değişir
        )
        ->then(function ($pushEnabled) {
            // Hiç alan doldurulmamış kullanıcı
            $user = User::factory()->create([
                'pubg_id' => null,
                'player_level' => null,
                'player_tier' => null,
                'main_server' => null,
                'favorite_mode' => null,
                'favorite_type' => null,
                'active_hours' => null,
                'interests' => null,
                'push_enabled' => $pushEnabled,
            ]);

            $completion = $this->service->calculateProfileCompletion($user);

            // push_enabled her zaman sayıldığı için: 1/9 * 100 = 11%
            $this->assertEquals(11, $completion);
        });
    }

    /**
     * Property: Tüm alanlar doldurulduğunda, profile completion 100% olmalıdır.
     * 
     * @test
     */
    public function profile_completion_is_100_when_all_fields_filled()
    {
        $this->forAll(
            Generator\bind(Generator\string(), function($str) { 
                $trimmed = substr($str, 0, 50);
                // Boş string üretilirse, en az 1 karakter ekle
                return Generator\constant($trimmed === '' ? 'TestPlayer' : $trimmed);
            }),
            Generator\choose(1, 100),
            Generator\elements(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror']),
            Generator\elements(['Europe', 'Asia', 'America']),
            Generator\elements(['TPP', 'FPP']),
            Generator\elements(['Solo', 'Duo', 'Squad']),
            // En az 1 elemanlı array üret - sabit non-empty array'lerden seç
            Generator\elements([
                ['morning'],
                ['afternoon'],
                ['evening'],
                ['night'],
                ['morning', 'afternoon'],
                ['evening', 'night'],
                ['morning', 'afternoon', 'evening', 'night']
            ]),
            // En az 1 elemanlı array üret - sabit non-empty array'lerden seç
            Generator\elements([
                ['lfg'],
                ['clan'],
                ['tournament'],
                ['social'],
                ['lfg', 'clan'],
                ['tournament', 'social'],
                ['lfg', 'clan', 'tournament', 'social']
            ]),
            Generator\bool()
        )
        ->then(function (
            $pubgId,
            $playerLevel,
            $playerTier,
            $mainServer,
            $favoriteMode,
            $favoriteType,
            $activeHours,
            $interests,
            $pushEnabled
        ) {
            // Tüm alanlar dolu kullanıcı
            $user = User::factory()->create([
                'pubg_id' => $pubgId,
                'player_level' => $playerLevel,
                'player_tier' => $playerTier,
                'main_server' => $mainServer,
                'favorite_mode' => $favoriteMode,
                'favorite_type' => $favoriteType,
                'active_hours' => $activeHours,
                'interests' => $interests,
                'push_enabled' => $pushEnabled,
            ]);

            $completion = $this->service->calculateProfileCompletion($user);

            // Tüm alanlar dolu: 9/9 * 100 = 100%
            $this->assertEquals(100, $completion);
        });
    }

    /**
     * Property: Boş array'ler doldurulmuş sayılmamalıdır.
     * 
     * @test
     */
    public function profile_completion_ignores_empty_arrays()
    {
        $this->forAll(
            Generator\bind(Generator\string(), function($str) { return Generator\constant(substr($str, 0, 50)); }),
            Generator\choose(1, 100),
            Generator\bool()
        )
        ->then(function ($pubgId, $playerLevel, $pushEnabled) {
            // Bazı alanlar dolu, array'ler boş
            $user = User::factory()->create([
                'pubg_id' => $pubgId,
                'player_level' => $playerLevel,
                'player_tier' => null,
                'main_server' => null,
                'favorite_mode' => null,
                'favorite_type' => null,
                'active_hours' => [], // Boş array
                'interests' => [], // Boş array
                'push_enabled' => $pushEnabled,
            ]);

            $completion = $this->service->calculateProfileCompletion($user);

            // Manuel hesaplama
            $filledCount = 0;
            if (!empty($pubgId)) $filledCount++; // pubg_id
            if (!empty($playerLevel)) $filledCount++; // player_level
            $filledCount++; // push_enabled (her zaman sayılır)
            
            $expectedCompletion = (int) round(($filledCount / 9) * 100);
            
            $this->assertEquals($expectedCompletion, $completion);
        });
    }

    /**
     * Property: Profile completion monotonic olmalıdır - 
     * alan ekledikçe yüzde artmalı veya aynı kalmalıdır.
     * 
     * @test
     */
    public function profile_completion_is_monotonic()
    {
        $this->forAll(
            Generator\bind(Generator\string(), function($str) { return Generator\constant(substr($str, 0, 50)); }),
            Generator\choose(1, 100),
            Generator\elements(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror']),
            Generator\bool()
        )
        ->then(function ($pubgId, $playerLevel, $playerTier, $pushEnabled) {
            // İlk durum: 2 alan dolu
            $user1 = User::factory()->create([
                'pubg_id' => $pubgId,
                'player_level' => null,
                'player_tier' => null,
                'main_server' => null,
                'favorite_mode' => null,
                'favorite_type' => null,
                'active_hours' => null,
                'interests' => null,
                'push_enabled' => $pushEnabled,
            ]);

            $completion1 = $this->service->calculateProfileCompletion($user1);

            // İkinci durum: 3 alan dolu (player_level eklendi)
            $user2 = User::factory()->create([
                'pubg_id' => $pubgId,
                'player_level' => $playerLevel,
                'player_tier' => null,
                'main_server' => null,
                'favorite_mode' => null,
                'favorite_type' => null,
                'active_hours' => null,
                'interests' => null,
                'push_enabled' => $pushEnabled,
            ]);

            $completion2 = $this->service->calculateProfileCompletion($user2);

            // Üçüncü durum: 4 alan dolu (player_tier eklendi)
            $user3 = User::factory()->create([
                'pubg_id' => $pubgId,
                'player_level' => $playerLevel,
                'player_tier' => $playerTier,
                'main_server' => null,
                'favorite_mode' => null,
                'favorite_type' => null,
                'active_hours' => null,
                'interests' => null,
                'push_enabled' => $pushEnabled,
            ]);

            $completion3 = $this->service->calculateProfileCompletion($user3);

            // Monotonic property: completion1 <= completion2 <= completion3
            $this->assertLessThanOrEqual(
                $completion2,
                $completion1,
                "Adding fields should not decrease completion. " .
                "Before: {$completion1}%, After: {$completion2}%"
            );

            $this->assertLessThanOrEqual(
                $completion3,
                $completion2,
                "Adding fields should not decrease completion. " .
                "Before: {$completion2}%, After: {$completion3}%"
            );
        });
    }
}
