<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Matchmaking geçmiş tablosu
     * Tamamlanan eşleşmelerin geçmişini tutar
     */
    public function up(): void
    {
        Schema::create('matchmaking_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->nullable()->constrained('matchmaking_matches')->onDelete('set null');
            
            // Sonuç (completed, cancelled, timeout, rejected)
            $table->enum('result', ['completed', 'cancelled', 'timeout', 'rejected']);
            
            // Bekleme süresi (saniye)
            $table->integer('wait_time_seconds')->default(0);
            
            // Uyumluluk skoru
            $table->integer('compatibility_score')->nullable();
            
            // Eşleşen oyuncular (JSON)
            $table->json('matched_users')->nullable();
            
            $table->timestamps();
            
            // Index'ler
            $table->index('user_id');
            $table->index('match_id');
            $table->index(['user_id', 'result']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_history');
    }
};
