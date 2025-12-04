<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration: guide_posts tablosundaki game_id'yi NOT NULL yap
     * 
     * Rehberler oyuna özeldir. Her oyunun farklı mekanikleri olduğu için
     * rehberler mutlaka bir oyuna ait olmalıdır.
     */
    public function up(): void
    {
        // Önce foreign key'i kaldır
        Schema::table('guide_posts', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
        });

        // Mevcut NULL game_id'leri game_id=1 (PUBG) ile güncelle
        DB::table('guide_posts')
            ->whereNull('game_id')
            ->update(['game_id' => 1]);

        // game_id'yi NOT NULL yap
        Schema::table('guide_posts', function (Blueprint $table) {
            $table->foreignId('game_id')->nullable(false)->change();
        });

        // Foreign key'i geri ekle (onDelete cascade ile)
        Schema::table('guide_posts', function (Blueprint $table) {
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Foreign key'i kaldır
        Schema::table('guide_posts', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
        });

        // game_id'yi tekrar nullable yap
        Schema::table('guide_posts', function (Blueprint $table) {
            $table->foreignId('game_id')->nullable()->change();
        });

        // Foreign key'i geri ekle (onDelete set null ile)
        Schema::table('guide_posts', function (Blueprint $table) {
            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->onDelete('set null');
        });
    }
};
