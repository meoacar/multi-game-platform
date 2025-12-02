<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Klanlar Tablosu
     * Oyuncu klanları ve toplulukları
     */
    public function up(): void
    {
        Schema::create('clans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Klan lideri
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            
            // Klan bilgileri
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->text('description');
            $table->text('requirements')->nullable(); // Gereksinimler açıklaması
            
            // Rank gereksinimleri
            $table->string('min_rank')->nullable();
            $table->string('max_rank')->nullable();
            
            // Diğer gereksinimler
            $table->string('min_age_range')->nullable();
            $table->string('max_age_range')->nullable();
            $table->string('city')->nullable();
            
            // Klan özellikleri
            $table->boolean('is_verified')->default(false);
            $table->integer('member_count')->default(1); // Lider dahil
            $table->integer('max_members')->default(50);
            $table->string('discord_invite')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index'ler
            $table->index(['game_id', 'city']);
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clans');
    }
};
