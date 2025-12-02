<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Kullanıcı cihaz ve hassasiyet ayarları
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Cihaz Bilgileri
            $table->string('device_name'); // "Poco X6 Pro", "iPhone 15 Pro"
            $table->string('graphics_settings')->nullable(); // "HDR + Extreme", "Smooth + Extreme"
            $table->string('fps_setting')->nullable(); // "60 FPS", "90 FPS", "120 FPS"
            $table->boolean('gyro_enabled')->default(false); // Gyro açık mı?
            
            // Hassasiyet Ayarları (JSON)
            $table->json('sensitivity_settings')->nullable(); // { "general": 80, "ads": 60, "gyro": 300 }
            
            // Notlar
            $table->text('notes')->nullable(); // Kullanıcının notları
            
            $table->timestamps();
            
            // Index'ler - Cihaz bazlı arama için
            $table->index('device_name');
            $table->index('fps_setting');
            $table->index('gyro_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
