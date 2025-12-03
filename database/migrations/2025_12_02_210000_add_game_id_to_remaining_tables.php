<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration: Kalan tablolara game_id ekleme
     * 
     * Bu migration, çoklu oyun desteği için gerekli olan game_id kolonlarını
     * henüz eklenmemiş tablolara ekler.
     * 
     * Eklenen tablolar:
     * - badges (nullable - bazı rozetler tüm oyunlar için geçerli olabilir)
     * - notifications (nullable - bazı bildirimler oyuna özel olmayabilir)
     * - community_posts (NOT NULL - topluluk gönderileri oyuna özeldir)
     */
    public function up(): void
    {
        // 1. badges tablosuna game_id ekle (nullable)
        Schema::table('badges', function (Blueprint $table) {
            $table->foreignId('game_id')
                ->nullable()
                ->after('id')
                ->constrained('games')
                ->onDelete('set null');
            
            // Index ekle
            $table->index('game_id');
        });

        // 2. notifications tablosuna game_id ekle (nullable)
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('game_id')
                ->nullable()
                ->after('id')
                ->constrained('games')
                ->onDelete('set null');
            
            // Index ekle
            $table->index('game_id');
        });

        // 3. community_posts tablosuna game_id ekle (NOT NULL)
        Schema::table('community_posts', function (Blueprint $table) {
            // Önce nullable olarak ekle (mevcut veriler için)
            $table->foreignId('game_id')
                ->nullable()
                ->after('id')
                ->constrained('games')
                ->onDelete('cascade');
            
            // Index ekle
            $table->index(['game_id', 'type', 'created_at']);
        });

        // Mevcut community_posts verilerini game_id=1 (PUBG) ile güncelle
        DB::table('community_posts')
            ->whereNull('game_id')
            ->update(['game_id' => 1]);

        // Şimdi game_id'yi NOT NULL yap
        Schema::table('community_posts', function (Blueprint $table) {
            $table->foreignId('game_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Foreign key ve kolonları kaldır
        Schema::table('badges', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropIndex(['game_id']);
            $table->dropColumn('game_id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropIndex(['game_id']);
            $table->dropColumn('game_id');
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropIndex(['game_id', 'type', 'created_at']);
            $table->dropColumn('game_id');
        });
    }
};
