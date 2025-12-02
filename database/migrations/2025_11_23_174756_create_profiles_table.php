<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Kullanıcı profil bilgileri - PUBG ve kişisel bilgiler
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // PUBG Bilgileri
            $table->string('nickname')->nullable(); // PUBG oyuncu adı
            $table->string('pubg_id')->nullable(); // PUBG ID
            $table->string('rank')->nullable(); // Bronze, Silver, Gold, Platinum, Diamond, Crown, Ace, Conqueror
            $table->string('server_region')->nullable(); // EU, MENA, ASIA, etc.
            
            // Kişisel Bilgiler
            $table->string('city')->nullable(); // İstanbul, Ankara, İzmir, etc.
            $table->string('age_range')->nullable(); // "18-24", "25-30", etc.
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('play_style')->nullable(); // "try-hard", "chill", "fun-first"
            $table->text('bio')->nullable(); // Kısa biyografi
            
            // Sosyal Medya
            $table->string('avatar_path')->nullable(); // Profil resmi
            $table->string('twitch_username')->nullable();
            $table->string('youtube_channel')->nullable();
            $table->string('discord_username')->nullable();
            
            // İstatistikler
            $table->integer('profile_views')->default(0); // Profil görüntülenme sayısı
            $table->boolean('is_profile_completed')->default(false); // Profil tamamlanma durumu
            
            $table->timestamps();
            
            // Index'ler - Filtreleme için
            $table->index('rank');
            $table->index('city');
            $table->index('server_region');
            $table->index('play_style');
            $table->index('is_profile_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
