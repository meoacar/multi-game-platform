<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Profiles tablosuna oyun istatistikleri ekleniyor
     * K/D, win rate, matches played, kills, headshots, vb.
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Maç İstatistikleri
            $table->integer('matches_played')->default(0)->after('is_profile_completed'); // Oynanan maç sayısı
            $table->integer('wins')->default(0)->after('matches_played'); // Kazanılan maç sayısı
            $table->decimal('win_rate', 5, 2)->default(0)->after('wins'); // Kazanma oranı (%)
            
            // Öldürme İstatistikleri
            $table->integer('kills')->default(0)->after('win_rate'); // Toplam öldürme
            $table->integer('deaths')->default(0)->after('kills'); // Toplam ölüm
            $table->decimal('kd_ratio', 5, 2)->default(0)->after('deaths'); // K/D oranı
            
            // Kafa Vuruşu İstatistikleri
            $table->integer('headshots')->default(0)->after('kd_ratio'); // Kafa vuruşu sayısı
            $table->decimal('headshot_rate', 5, 2)->default(0)->after('headshots'); // Kafa vuruşu oranı (%)
            
            // Diğer İstatistikler
            $table->integer('top_10_finishes')->default(0)->after('headshot_rate'); // İlk 10'a girme
            $table->bigInteger('damage_dealt')->default(0)->after('top_10_finishes'); // Verilen hasar
            $table->integer('survival_time')->default(0)->after('damage_dealt'); // Hayatta kalma süresi (dakika)
            $table->integer('longest_kill')->default(0)->after('survival_time'); // En uzak öldürme (metre)
            
            // Index'ler - Sıralama ve filtreleme için
            $table->index('kd_ratio');
            $table->index('win_rate');
            $table->index('matches_played');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Index'leri kaldır
            $table->dropIndex(['kd_ratio']);
            $table->dropIndex(['win_rate']);
            $table->dropIndex(['matches_played']);
            
            // Kolonları kaldır
            $table->dropColumn([
                'matches_played',
                'wins',
                'win_rate',
                'kills',
                'deaths',
                'kd_ratio',
                'headshots',
                'headshot_rate',
                'top_10_finishes',
                'damage_dealt',
                'survival_time',
                'longest_kill',
            ]);
        });
    }
};
