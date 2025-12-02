<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * LFG (Looking For Group) İlanları Tablosu
     * Kullanıcıların takım arama ilanları
     */
    public function up(): void
    {
        Schema::create('lfg_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            
            // İlan bilgileri
            $table->string('title');
            $table->text('description');
            
            // Rank gereksinimleri
            $table->string('min_rank')->nullable();
            $table->string('max_rank')->nullable();
            
            // Oyun modu (Squad TPP, Duo FPP vb.)
            $table->string('mode')->nullable();
            
            // Gereksinimler
            $table->boolean('microphone_required')->default(false);
            $table->string('min_age_range')->nullable();
            $table->string('max_age_range')->nullable();
            
            // Filtreler
            $table->string('city')->nullable();
            $table->string('play_style_tag')->nullable(); // try-hard, chill, fun-first
            
            // Durum
            $table->enum('status', ['open', 'closed'])->default('open');
            
            // Ek özellikler
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->text('admin_notes')->nullable();
            $table->integer('views_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index'ler
            $table->index(['game_id', 'status', 'created_at']);
            $table->index(['city', 'play_style_tag']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lfg_posts');
    }
};
