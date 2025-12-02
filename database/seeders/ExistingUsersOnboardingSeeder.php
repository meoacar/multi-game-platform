<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

/**
 * Mevcut Kullanıcılar İçin Onboarding Seeder
 * 
 * Bu seeder, onboarding sistemi eklenmeden önce kayıt olmuş
 * mevcut kullanıcıların onboarding_completed flag'ini true olarak ayarlar.
 * Böylece mevcut kullanıcılar onboarding sürecine zorlanmaz.
 */
class ExistingUsersOnboardingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 Mevcut kullanıcılar için onboarding durumu güncelleniyor...');

        // Onboarding kolonları eklenmiş mi kontrol et
        if (!DB::getSchemaBuilder()->hasColumn('users', 'onboarding_completed')) {
            $this->command->error('❌ Onboarding kolonları henüz eklenmemiş! Önce migration\'ı çalıştırın.');
            return;
        }

        // Tüm mevcut kullanıcıları güncelle
        $updatedCount = User::where('onboarding_completed', false)
            ->orWhereNull('onboarding_completed')
            ->update([
                'onboarding_completed' => true,
                'onboarding_step' => 4, // Tamamlanmış olarak işaretle
                'profile_completion' => 50, // Kısmi tamamlanma
            ]);

        $this->command->info("✅ {$updatedCount} kullanıcının onboarding durumu güncellendi.");
        $this->command->info('📊 Bu kullanıcılar artık onboarding sürecine zorlanmayacak.');
        $this->command->info('💡 İsteyen kullanıcılar profil sayfasından bilgilerini tamamlayabilir.');
    }
}
