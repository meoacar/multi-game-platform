<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration: profiles tablosuna game_id ekleme
     * 
     * Her kullanıcı her oyun için farklı profile sahip olabilir.
     * Örnek: Bir kullanıcının PUBG'de Diamond, Valorant'ta Gold profili olabilir.
     * 
     * ÖNEMLI: user_id unique constraint kaldırılıyor, 
     * yerine (user_id, game_id) composite unique ekleniyor.
     */
    public function up(): void
    {
        // Önce mevcut foreign key'i kaldır
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Sonra unique constraint'i kaldır
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });

        // game_id kolonunu ekle
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreignId('game_id')
                ->nullable()
                ->after('user_id')
                ->constrained('games')
                ->onDelete('cascade');
            
            // Index ekle
            $table->index('game_id');
        });

        // Mevcut profilleri game_id=1 (PUBG) ile güncelle
        DB::table('profiles')
            ->whereNull('game_id')
            ->update(['game_id' => 1]);

        // game_id'yi NOT NULL yap
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreignId('game_id')->nullable(false)->change();
        });

        // Yeni composite unique constraint ekle (user_id + game_id)
        Schema::table('profiles', function (Blueprint $table) {
            $table->unique(['user_id', 'game_id'], 'profiles_user_game_unique');
        });

        // Foreign key'i geri ekle
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Foreign key'i kaldır
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Composite unique constraint'i kaldır
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropUnique('profiles_user_game_unique');
        });

        // game_id kolonunu kaldır
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropIndex(['game_id']);
            $table->dropColumn('game_id');
        });

        // Eski user_id unique constraint'i geri ekle
        Schema::table('profiles', function (Blueprint $table) {
            $table->unique('user_id');
        });

        // Foreign key'i geri ekle
        Schema::table('profiles', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
