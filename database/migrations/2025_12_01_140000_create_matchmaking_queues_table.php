<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Matchmaking kuyruk tablosu
     * Eşleşme bekleyen kullanıcıların bilgilerini tutar
     */
    public function up(): void
    {
        Schema::create('matchmaking_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            
            // Oyun modu (squad, duo, solo)
            $table->enum('mode', ['squad', 'duo', 'solo'])->default('squad');
            
            // Rank tercihleri
            $table->string('min_rank')->nullable();
            $table->string('max_rank')->nullable();
            
            // Lokasyon ve iletişim tercihleri
            $table->string('city')->nullable();
            $table->boolean('microphone_required')->default(false);
            
            // Oyun stili (aggressive, balanced, defensive)
            $table->enum('play_style', ['aggressive', 'balanced', 'defensive'])->nullable();
            
            // Kuyruk durumu
            $table->enum('status', ['searching', 'matched', 'cancelled', 'expired'])->default('searching');
            
            // Zaman aşımı ve deneme sayısı
            $table->timestamp('expires_at');
            $table->integer('search_attempts')->default(0);
            
            $table->timestamps();
            
            // Index'ler - performans için kritik
            $table->index(['user_id', 'status']);
            $table->index(['game_id', 'mode', 'status']);
            $table->index(['status', 'expires_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_queues');
    }
};
