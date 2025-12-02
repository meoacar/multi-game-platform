<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Oyun listesi - PUBG Mobile, Call of Duty Mobile, MLBB, etc.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "PUBG Mobile", "Call of Duty Mobile"
            $table->string('slug')->unique(); // "pubg-mobile", "call-of-duty-mobile"
            $table->string('icon')->nullable(); // Oyun ikonu
            $table->text('description')->nullable(); // Oyun açıklaması
            $table->boolean('is_active')->default(true); // Aktif/Pasif
            $table->integer('order')->default(0); // Sıralama
            $table->timestamps();
            
            // Index'ler
            $table->index('is_active');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
