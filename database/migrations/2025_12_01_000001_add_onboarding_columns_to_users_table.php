<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Onboarding sistemi için users tablosuna gerekli kolonları ekler.
     * Bu migration, yeni kullanıcıların profil tamamlama sürecini yönetmek için kullanılır.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Onboarding durum bilgileri
            $table->boolean('onboarding_completed')->default(false)->after('status');
            $table->integer('onboarding_step')->default(0)->after('onboarding_completed');
            $table->integer('profile_completion')->default(0)->after('onboarding_step');
            
            // Adım 1: PUBG Profil Bilgileri
            $table->string('pubg_id', 50)->nullable()->after('profile_completion');
            $table->integer('player_level')->nullable()->after('pubg_id');
            $table->enum('player_tier', [
                'Bronze', 
                'Silver', 
                'Gold', 
                'Platinum', 
                'Diamond', 
                'Crown', 
                'Ace', 
                'Conqueror'
            ])->nullable()->after('player_level');
            $table->enum('main_server', ['Europe', 'Asia', 'America'])->nullable()->after('player_tier');
            
            // Adım 2: Oyun Tercihleri
            $table->enum('favorite_mode', ['TPP', 'FPP'])->nullable()->after('main_server');
            $table->enum('favorite_type', ['Solo', 'Duo', 'Squad'])->nullable()->after('favorite_mode');
            $table->json('active_hours')->nullable()->after('favorite_type');
            
            // Adım 3: İlgi Alanları
            $table->json('interests')->nullable()->after('active_hours');
            
            // Adım 4: Bildirim Tercihleri
            $table->boolean('push_enabled')->default(false)->after('interests');
            
            // Index'ler - performans için
            $table->index('onboarding_completed');
            $table->index('onboarding_step');
            $table->index('profile_completion');
            $table->index('pubg_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Index'leri kaldır
            $table->dropIndex(['onboarding_completed']);
            $table->dropIndex(['onboarding_step']);
            $table->dropIndex(['profile_completion']);
            $table->dropIndex(['pubg_id']);
            
            // Kolonları kaldır
            $table->dropColumn([
                'onboarding_completed',
                'onboarding_step',
                'profile_completion',
                'pubg_id',
                'player_level',
                'player_tier',
                'main_server',
                'favorite_mode',
                'favorite_type',
                'active_hours',
                'interests',
                'push_enabled',
            ]);
        });
    }
};
