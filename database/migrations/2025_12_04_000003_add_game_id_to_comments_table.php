<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration: comments tablosuna game_id ekleme
     * 
     * Yorumlar oyuna özel içeriklere (rehber, topluluk gönderisi vb.) ait olduğu için
     * game_id ile filtrelenmeli.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('game_id')
                ->nullable()
                ->after('id')
                ->constrained('games')
                ->onDelete('cascade');
            
            // Index ekle
            $table->index(['game_id', 'created_at']);
        });

        // Mevcut yorumları ilişkili oldukları içeriğin game_id'si ile güncelle
        // Guide posts için
        DB::statement('
            UPDATE comments c
            INNER JOIN guide_posts gp ON c.commentable_id = gp.id
            SET c.game_id = gp.game_id
            WHERE c.commentable_type = "App\\\\Models\\\\GuidePost"
            AND c.game_id IS NULL
        ');

        // Community posts için
        DB::statement('
            UPDATE comments c
            INNER JOIN community_posts cp ON c.commentable_id = cp.id
            SET c.game_id = cp.game_id
            WHERE c.commentable_type = "App\\\\Models\\\\CommunityPost"
            AND c.game_id IS NULL
        ');

        // Kalan NULL değerleri game_id=1 (PUBG) ile güncelle
        DB::table('comments')
            ->whereNull('game_id')
            ->update(['game_id' => 1]);

        // game_id'yi NOT NULL yap
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('game_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropIndex(['game_id', 'created_at']);
            $table->dropColumn('game_id');
        });
    }
};
