<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Onboarding Service
 * 
 * Yeni kullanıcıların onboarding sürecini yönetir.
 * 4 adımlı profil tamamlama sürecini kontrol eder.
 */
class OnboardingService
{
    /**
     * Toplam onboarding adım sayısı
     */
    const TOTAL_STEPS = 4;

    /**
     * Adım verilerini kaydet
     * 
     * Kullanıcının belirli bir adımdaki verilerini kaydeder ve
     * onboarding_step değerini günceller.
     * 
     * @param User $user Kullanıcı modeli
     * @param int $step Adım numarası (1-4)
     * @param array $data Kaydedilecek veriler
     * @return bool Başarılı ise true
     */
    public function saveStep(User $user, int $step, array $data): bool
    {
        try {
            // Veriyi users tablosuna kaydet
            $user->update($data);
            
            // Profile tablosuna da kaydet
            $profile = $user->profile;
            if ($profile) {
                $profileData = [];
                
                // Step 1: PUBG Profil Bilgileri
                if ($step === 1) {
                    if (isset($data['player_tier'])) {
                        $profileData['rank'] = $data['player_tier'];
                    }
                    if (isset($data['pubg_id'])) {
                        $profileData['nickname'] = $data['pubg_id'];
                    }
                    if (isset($data['main_server'])) {
                        $profileData['server_region'] = $data['main_server'];
                    }
                }
                
                // Step 2: Oyun Tercihleri
                if ($step === 2) {
                    if (isset($data['favorite_mode'])) {
                        $profileData['favorite_mode'] = $data['favorite_mode'];
                    }
                    if (isset($data['favorite_type'])) {
                        $profileData['play_style'] = $data['favorite_type'];
                    }
                }
                
                // Step 3: İlgi Alanları
                if ($step === 3) {
                    // interests ve active_hours users tablosunda, profile'da yok
                    // Gerekirse profile tablosuna eklenebilir
                }
                
                if (!empty($profileData)) {
                    $profile->update($profileData);
                }
            }
            
            // Adım numarasını güncelle (bir sonraki adıma geç)
            if ($step < self::TOTAL_STEPS) {
                $user->updateOnboardingStep($step + 1);
            }
            
            // Profil tamamlanma yüzdesini güncelle
            $completion = $this->calculateProfileCompletion($user);
            $user->update(['profile_completion' => $completion]);
            
            Log::info("Onboarding step {$step} saved for user {$user->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Onboarding step save error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Profil tamamlanma yüzdesini hesapla
     * 
     * Kullanıcının doldurduğu onboarding alanlarına göre
     * profil tamamlanma yüzdesini hesaplar.
     * 
     * @param User $user Kullanıcı modeli
     * @return int Tamamlanma yüzdesi (0-100)
     */
    public function calculateProfileCompletion(User $user): int
    {
        // Kontrol edilecek alanlar
        $fields = [
            'pubg_id',
            'player_level',
            'player_tier',
            'main_server',
            'favorite_mode',
            'favorite_type',
            'active_hours',
            'interests',
            'push_enabled',
        ];

        $filledCount = 0;
        $totalFields = count($fields);

        foreach ($fields as $field) {
            $value = $user->$field;
            
            // Array alanlar için özel kontrol
            if (in_array($field, ['active_hours', 'interests'])) {
                if (!empty($value) && is_array($value) && count($value) > 0) {
                    $filledCount++;
                }
            } 
            // Boolean alanlar için özel kontrol (push_enabled)
            elseif ($field === 'push_enabled') {
                // push_enabled her zaman sayılır (true veya false)
                $filledCount++;
            }
            // Diğer alanlar - boş string'leri de kontrol et
            elseif (!empty($value) && $value !== '') {
                $filledCount++;
            }
        }

        return (int) round(($filledCount / $totalFields) * 100);
    }

    /**
     * Onboarding'i tamamla
     * 
     * Kullanıcının onboarding sürecini tamamlar.
     * Atlama durumunda profil tamamlanma yüzdesi 50 olarak ayarlanır.
     * 
     * @param User $user Kullanıcı modeli
     * @param bool $skipped Onboarding atlandı mı?
     * @return void
     */
    public function completeOnboarding(User $user, bool $skipped = false): void
    {
        $data = [
            'onboarding_completed' => true,
            'onboarding_step' => self::TOTAL_STEPS,
        ];

        if ($skipped) {
            // Atlandıysa profil tamamlanma 50%
            $data['profile_completion'] = 50;
            Log::info("User {$user->id} skipped onboarding");
        } else {
            // Tamamlandıysa profil tamamlanma 100%
            $data['profile_completion'] = 100;
            Log::info("User {$user->id} completed onboarding");
        }

        $user->update($data);
    }

    /**
     * Mevcut adımı al
     * 
     * Kullanıcının şu anki onboarding adımını döner.
     * 
     * @param User $user Kullanıcı modeli
     * @return int Mevcut adım numarası (0-4)
     */
    public function getCurrentStep(User $user): int
    {
        return $user->onboarding_step ?? 0;
    }

    /**
     * Sonraki adıma geç
     * 
     * Kullanıcıyı bir sonraki onboarding adımına taşır.
     * Son adımdan sonra geçiş yapmaz.
     * 
     * @param User $user Kullanıcı modeli
     * @return void
     */
    public function moveToNextStep(User $user): void
    {
        $currentStep = $this->getCurrentStep($user);
        
        if ($currentStep < self::TOTAL_STEPS) {
            $user->updateOnboardingStep($currentStep + 1);
            Log::info("User {$user->id} moved to step " . ($currentStep + 1));
        }
    }

    /**
     * Önceki adıma dön
     * 
     * Kullanıcıyı bir önceki onboarding adımına taşır.
     * İlk adımdan önce geçiş yapmaz.
     * 
     * @param User $user Kullanıcı modeli
     * @return void
     */
    public function moveToPreviousStep(User $user): void
    {
        $currentStep = $this->getCurrentStep($user);
        
        if ($currentStep > 1) {
            $user->updateOnboardingStep($currentStep - 1);
            Log::info("User {$user->id} moved back to step " . ($currentStep - 1));
        }
    }

    /**
     * Onboarding'in tamamlanıp tamamlanmadığını kontrol et
     * 
     * @param User $user Kullanıcı modeli
     * @return bool Tamamlandıysa true
     */
    public function isCompleted(User $user): bool
    {
        return $user->hasCompletedOnboarding();
    }

    /**
     * Onboarding'i sıfırla (test/debug için)
     * 
     * @param User $user Kullanıcı modeli
     * @return void
     */
    public function resetOnboarding(User $user): void
    {
        $user->update([
            'onboarding_completed' => false,
            'onboarding_step' => 0,
            'profile_completion' => 0,
        ]);
        
        Log::info("Onboarding reset for user {$user->id}");
    }
}
