<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Matchmaking tercihler tablosu
     * Kullanıcıların varsayılan eşleşme tercihlerini tutar
     */
    public function up(): void
    {
        Schema::create('matchmaking_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Varsayılan oyun modu
            $table->enum('default_mode', ['squad', 'duo', 'solo'])->default('squad');
            
            // Otomatik kabul
            $table->boolean('auto_accept_matches')->default(false);
            
            // Mikrofon zorunluluğu
            $table->boolean('microphone_required')->default(false);
            
            // Tercih edilen oyun stili
            $table->enum('preferred_play_style', ['aggressive', 'balanced', 'defensive'])->nullable();
            
            // Sadece aynı şehir
            $table->boolean('same_city_only')->default(false);
            
            // Maksimum rank farkı (1-5)
            $table->integer('max_rank_difference')->default(2);
            
            $table->timestamps();
            
            // Index
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchmaking_preferences');
    }
};
