<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\OnboardingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * OnboardingService Unit Tests
 * 
 * Requirements: 1.3, 1.4, 1.5, 6.2, 6.5, 9.5
 * 
 * Test Senaryoları:
 * - saveStep() - Adım verilerini kaydetme
 * - calculateProfileCompletion() - Profil tamamlanma yüzdesi hesaplama
 * - completeOnboarding() - Onboarding tamamlama
 * - getCurrentStep() - Mevcut adımı alma
 * - moveToNextStep() - Sonraki adıma geçme
 * - moveToPreviousStep() - Önceki adıma dönme
 */
class OnboardingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OnboardingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OnboardingService();
    }

    // ==========================================
    // saveStep() Testleri
    // ==========================================

    /** @test */
    public function save_step_saves_data_and_updates_step_number()
    {
        // Kullanıcı oluştur
        $user = User::factory()->create([
            'onboarding_step' => 1,
            'profile_completion' => 0,
        ]);

        // Adım 1 verilerini kaydet
        $stepData = [
            'pubg_id' => 'TestPlayer123',
            'player_level' => 50,
            'player_tier' => 'Gold',
            'main_server' => 'Europe',
        ];

        $result = $this->service->saveStep($user, 1, $stepData);

        // Sonuç kontrolü
        $this->assertTrue($result);

        // Veritabanı kontrolü
        $user->refresh();
        $this->assertEquals('TestPlayer123', $user->pubg_id);
        $this->assertEquals(50, $user->player_level);
        $this->assertEquals('Gold', $user->player_tier);
        $this->assertEquals('Europe', $user->main_server);
        
        // Adım numarası bir sonraki adıma geçmiş olmalı
        $this->assertEquals(2, $user->onboarding_step);
        
        // Profil tamamlanma yüzdesi güncellenmiş olmalı
        $this->assertGreaterThan(0, $user->profile_completion);
    }

    /** @test */
    public function save_step_does_not_increment_step_on_last_step()
    {
        // Son adımda olan kullanıcı
        $user = User::factory()->create([
            'onboarding_step' => 4,
        ]);

        $stepData = [
            'push_enabled' => true,
        ];

        $result = $this->service->saveStep($user, 4, $stepData);

        $this->assertTrue($result);
        
        // Adım numarası 4'te kalmalı (5'e geçmemeli)
        $user->refresh();
        $this->assertEquals(4, $user->onboarding_step);
    }

    /** @test */
    public function save_step_updates_profile_completion()
    {
        $user = User::factory()->create([
            'onboarding_step' => 1,
            'profile_completion' => 0,
        ]);

        $stepData = [
            'pubg_id' => 'TestPlayer',
            'player_level' => 75,
        ];

        $this->service->saveStep($user, 1, $stepData);

        $user->refresh();
        
        // Profil tamamlanma yüzdesi hesaplanmış olmalı
        $this->assertGreaterThan(0, $user->profile_completion);
        $this->assertLessThanOrEqual(100, $user->profile_completion);
    }

    // ==========================================
    // calculateProfileCompletion() Testleri
    // ==========================================

    /** @test */
    public function calculate_profile_completion_returns_zero_for_empty_profile()
    {
        // Hiç veri girilmemiş kullanıcı
        $user = User::factory()->create([
            'pubg_id' => null,
            'player_level' => null,
            'player_tier' => null,
            'main_server' => null,
            'favorite_mode' => null,
            'favorite_type' => null,
            'active_hours' => null,
            'interests' => null,
            'push_enabled' => false,
        ]);

        $completion = $this->service->calculateProfileCompletion($user);

        // push_enabled her zaman sayıldığı için 11% olmalı (1/9 * 100)
        $this->assertEquals(11, $completion);
    }

    /** @test */
    public function calculate_profile_completion_returns_correct_percentage()
    {
        // Kısmen doldurulmuş profil
        $user = User::factory()->create([
            'pubg_id' => 'TestPlayer',
            'player_level' => 50,
            'player_tier' => 'Gold',
            'main_server' => 'Europe',
            'favorite_mode' => null,
            'favorite_type' => null,
            'active_hours' => null,
            'interests' => null,
            'push_enabled' => true,
        ]);

        $completion = $this->service->calculateProfileCompletion($user);

        // 5 alan dolu (pubg_id, player_level, player_tier, main_server, push_enabled)
        // 5/9 * 100 = 56%
        $this->assertEquals(56, $completion);
    }

    /** @test */
    public function calculate_profile_completion_handles_array_fields()
    {
        // Array alanları olan kullanıcı
        $user = User::factory()->create([
            'pubg_id' => 'TestPlayer',
            'player_level' => 50,
            'player_tier' => 'Gold',
            'main_server' => 'Europe',
            'favorite_mode' => 'TPP',
            'favorite_type' => 'Squad',
            'active_hours' => ['evening', 'night'],
            'interests' => ['lfg', 'clan'],
            'push_enabled' => true,
        ]);

        $completion = $this->service->calculateProfileCompletion($user);

        // Tüm alanlar dolu: 9/9 * 100 = 100%
        $this->assertEquals(100, $completion);
    }

    /** @test */
    public function calculate_profile_completion_ignores_empty_arrays()
    {
        $user = User::factory()->create([
            'pubg_id' => 'TestPlayer',
            'player_level' => 50,
            'active_hours' => [], // Boş array
            'interests' => [], // Boş array
            'push_enabled' => true,
        ]);

        $completion = $this->service->calculateProfileCompletion($user);

        // 3 alan dolu (pubg_id, player_level, push_enabled)
        // 3/9 * 100 = 33%
        $this->assertEquals(33, $completion);
    }

    // ==========================================
    // completeOnboarding() Testleri
    // ==========================================

    /** @test */
    public function complete_onboarding_sets_completion_to_100_when_not_skipped()
    {
        $user = User::factory()->create([
            'onboarding_completed' => false,
            'onboarding_step' => 3,
            'profile_completion' => 50,
        ]);

        $this->service->completeOnboarding($user, false);

        $user->refresh();
        
        $this->assertTrue($user->onboarding_completed);
        $this->assertEquals(4, $user->onboarding_step);
        $this->assertEquals(100, $user->profile_completion);
    }

    /** @test */
    public function complete_onboarding_sets_completion_to_50_when_skipped()
    {
        $user = User::factory()->create([
            'onboarding_completed' => false,
            'onboarding_step' => 1,
            'profile_completion' => 0,
        ]);

        $this->service->completeOnboarding($user, true);

        $user->refresh();
        
        $this->assertTrue($user->onboarding_completed);
        $this->assertEquals(4, $user->onboarding_step);
        $this->assertEquals(50, $user->profile_completion);
    }

    /** @test */
    public function complete_onboarding_marks_as_completed()
    {
        $user = User::factory()->create([
            'onboarding_completed' => false,
        ]);

        $this->service->completeOnboarding($user);

        $user->refresh();
        
        $this->assertTrue($user->onboarding_completed);
        $this->assertTrue($user->hasCompletedOnboarding());
    }

    // ==========================================
    // getCurrentStep() Testleri
    // ==========================================

    /** @test */
    public function get_current_step_returns_correct_step()
    {
        $user = User::factory()->create([
            'onboarding_step' => 2,
        ]);

        $step = $this->service->getCurrentStep($user);

        $this->assertEquals(2, $step);
    }

    /** @test */
    public function get_current_step_returns_zero_for_new_user()
    {
        // Yeni kullanıcı oluştur (onboarding_step default 0 olacak)
        $user = User::factory()->create();

        $step = $this->service->getCurrentStep($user);

        $this->assertEquals(0, $step);
    }

    /** @test */
    public function get_current_step_returns_zero_when_step_is_zero()
    {
        $user = User::factory()->create([
            'onboarding_step' => 0,
        ]);

        $step = $this->service->getCurrentStep($user);

        $this->assertEquals(0, $step);
    }

    // ==========================================
    // moveToNextStep() Testleri
    // ==========================================

    /** @test */
    public function move_to_next_step_increments_step()
    {
        $user = User::factory()->create([
            'onboarding_step' => 1,
        ]);

        $this->service->moveToNextStep($user);

        $user->refresh();
        $this->assertEquals(2, $user->onboarding_step);
    }

    /** @test */
    public function move_to_next_step_does_not_exceed_total_steps()
    {
        $user = User::factory()->create([
            'onboarding_step' => 4,
        ]);

        $this->service->moveToNextStep($user);

        $user->refresh();
        
        // 4'te kalmalı, 5'e geçmemeli
        $this->assertEquals(4, $user->onboarding_step);
    }

    /** @test */
    public function move_to_next_step_works_from_step_zero()
    {
        $user = User::factory()->create([
            'onboarding_step' => 0,
        ]);

        $this->service->moveToNextStep($user);

        $user->refresh();
        $this->assertEquals(1, $user->onboarding_step);
    }

    // ==========================================
    // moveToPreviousStep() Testleri
    // ==========================================

    /** @test */
    public function move_to_previous_step_decrements_step()
    {
        $user = User::factory()->create([
            'onboarding_step' => 3,
        ]);

        $this->service->moveToPreviousStep($user);

        $user->refresh();
        $this->assertEquals(2, $user->onboarding_step);
    }

    /** @test */
    public function move_to_previous_step_does_not_go_below_one()
    {
        $user = User::factory()->create([
            'onboarding_step' => 1,
        ]);

        $this->service->moveToPreviousStep($user);

        $user->refresh();
        
        // 1'de kalmalı, 0'a gitmemeli
        $this->assertEquals(1, $user->onboarding_step);
    }

    /** @test */
    public function move_to_previous_step_does_nothing_from_step_zero()
    {
        $user = User::factory()->create([
            'onboarding_step' => 0,
        ]);

        $this->service->moveToPreviousStep($user);

        $user->refresh();
        
        // 0'da kalmalı
        $this->assertEquals(0, $user->onboarding_step);
    }

    // ==========================================
    // Entegrasyon Testleri
    // ==========================================

    /** @test */
    public function full_onboarding_flow_updates_all_fields_correctly()
    {
        // Yeni kullanıcı
        $user = User::factory()->create([
            'onboarding_step' => 1,
            'onboarding_completed' => false,
            'profile_completion' => 0,
        ]);

        // Adım 1: PUBG Profil Bilgileri
        $this->service->saveStep($user, 1, [
            'pubg_id' => 'ProPlayer',
            'player_level' => 80,
            'player_tier' => 'Diamond',
            'main_server' => 'Asia',
        ]);

        $user->refresh();
        $this->assertEquals(2, $user->onboarding_step);
        $this->assertGreaterThan(0, $user->profile_completion);

        // Adım 2: Oyun Tercihleri
        $this->service->saveStep($user, 2, [
            'favorite_mode' => 'FPP',
            'favorite_type' => 'Squad',
            'active_hours' => ['evening', 'night'],
        ]);

        $user->refresh();
        $this->assertEquals(3, $user->onboarding_step);

        // Adım 3: İlgi Alanları
        $this->service->saveStep($user, 3, [
            'interests' => ['lfg', 'clan', 'tournament'],
        ]);

        $user->refresh();
        $this->assertEquals(4, $user->onboarding_step);

        // Adım 4: Bildirimler
        $this->service->saveStep($user, 4, [
            'push_enabled' => true,
        ]);

        // Onboarding'i tamamla
        $this->service->completeOnboarding($user, false);

        $user->refresh();
        
        // Tüm kontroller
        $this->assertTrue($user->onboarding_completed);
        $this->assertEquals(4, $user->onboarding_step);
        $this->assertEquals(100, $user->profile_completion);
        $this->assertEquals('ProPlayer', $user->pubg_id);
        $this->assertEquals(80, $user->player_level);
        $this->assertEquals('Diamond', $user->player_tier);
        $this->assertEquals('FPP', $user->favorite_mode);
        $this->assertTrue($user->push_enabled);
    }

    /** @test */
    public function skipping_onboarding_preserves_partial_data()
    {
        $user = User::factory()->create([
            'onboarding_step' => 2,
            'pubg_id' => 'PartialUser',
            'player_level' => 30,
        ]);

        // Onboarding'i atla
        $this->service->completeOnboarding($user, true);

        $user->refresh();
        
        // Kısmi veriler korunmalı
        $this->assertEquals('PartialUser', $user->pubg_id);
        $this->assertEquals(30, $user->player_level);
        
        // Tamamlanma durumu
        $this->assertTrue($user->onboarding_completed);
        $this->assertEquals(50, $user->profile_completion);
    }

    /** @test */
    public function navigation_back_and_forth_preserves_data()
    {
        $user = User::factory()->create([
            'onboarding_step' => 2,
            'pubg_id' => 'NavTest',
            'player_level' => 60,
        ]);

        // İleri git
        $this->service->moveToNextStep($user);
        $user->refresh();
        $this->assertEquals(3, $user->onboarding_step);

        // Geri dön
        $this->service->moveToPreviousStep($user);
        $user->refresh();
        $this->assertEquals(2, $user->onboarding_step);

        // Veriler korunmalı
        $this->assertEquals('NavTest', $user->pubg_id);
        $this->assertEquals(60, $user->player_level);
    }
}
