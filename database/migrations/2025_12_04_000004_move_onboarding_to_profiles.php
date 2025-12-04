<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration: Onboarding kolonlarını users'dan profiles'a taşı
     * 
     * Her oyun için ayrı onboarding süreci olacak.
     * Kullanıcı her oyun için ayrı profil oluşturacak ve onboarding tamamlayacak.
     */
    public function up(): void
    {
        // profiles tablosuna onboarding kolonlarını ekle
        Schema::table('profiles', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false)->after('is_profile_completed');
            $table->integer('onboarding_step')->default(0)->after('onboarding_completed');
        });

        // Mevcut profillerin onboarding durumunu users'dan kopyala
        DB::statement('
            UPDATE profiles p
            INNER JOIN users u ON p.user_id = u.id
            SET p.onboarding_completed = u.onboarding_completed,
                p.onboarding_step = u.onboarding_step
            WHERE u.onboarding_completed IS NOT NULL
        ');

        // users tablosundan onboarding kolonlarını kaldır
        Schema::table('users', function (Blueprint $table) {
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
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // users tablosuna kolonları geri ekle
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false);
            $table->integer('onboarding_step')->default(0);
            $table->integer('profile_completion')->default(0);
            $table->string('pubg_id')->nullable();
            $table->integer('player_level')->nullable();
            $table->string('player_tier')->nullable();
            $table->string('main_server')->nullable();
            $table->string('favorite_mode')->nullable();
            $table->string('favorite_type')->nullable();
            $table->json('active_hours')->nullable();
            $table->json('interests')->nullable();
        });

        // Verileri geri kopyala (sadece ilk profil)
        DB::statement('
            UPDATE users u
            INNER JOIN (
                SELECT user_id, onboarding_completed, onboarding_step
                FROM profiles
                WHERE game_id = 1
            ) p ON u.id = p.user_id
            SET u.onboarding_completed = p.onboarding_completed,
                u.onboarding_step = p.onboarding_step
        ');

        // profiles tablosundan kolonları kaldır
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'onboarding_completed',
                'onboarding_step',
            ]);
        });
    }
};
