<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Matchmaking eşleşme tablosu
     * Oluşturulan eşleşmeleri ve kabul durumlarını tutar
     */
    public function up(): void
    {
        Schema::create('matchmaking_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            
            // Oyun modu
            $table->enum('mode', ['squad', 'duo', 'solo'])->default('squad');
            
            // Eşleşen kullanıcılar (JSON array)
            $table->json('user_ids');
            
            // Uyumluluk skoru (0-100)
            $table->integer('compatibility_score')->default(0);
            
            // Eşleşme kriterleri (JSON)
            $table->json('match_criteria')->nullable();
            
            // Eşleşme durumu
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            
            // Her kullanıcının kabul durumu (JSON: {user_id: accepted/rejected/pending})
            $table->json('acceptance_status')->nullable();
            
            // Zaman aşımı (30 saniye)
            $table->timestamp('expires_at');
            
            $table->timestamps();
            
            // Index'ler
            $table->index(['status', 'expires_at']);
            $table->index('game_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_matches');
    }
};
