<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            
            // Temel bilgiler
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Görsel bilgileri
            $table->string('image_path');
            $table->string('mobile_image_path')->nullable(); // Mobil için ayrı görsel
            $table->string('link_url')->nullable();
            $table->enum('link_target', ['_self', '_blank'])->default('_self');
            
            // Konum ve sıralama
            $table->enum('location', [
                'home_hero',        // Ana sayfa hero slider
                'home_top',         // Ana sayfa üst banner
                'home_middle',      // Ana sayfa orta banner
                'home_bottom',      // Ana sayfa alt banner
                'sidebar',          // Sidebar banner
                'content_top',      // İçerik üstü
                'content_bottom',   // İçerik altı
                'popup',            // Popup banner
            ])->default('home_hero');
            $table->integer('order')->default(0);
            
            // Zamanlama
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            
            // Hedef kitle
            $table->enum('target_audience', [
                'all',              // Tüm kullanıcılar
                'guests',           // Misafirler (giriş yapmamış)
                'members',          // Üyeler (giriş yapmış)
                'new_members',      // Yeni üyeler (30 gün içinde kayıt)
                'active_members',   // Aktif üyeler (son 7 gün içinde aktif)
                'inactive_members', // Pasif üyeler (30+ gün aktif değil)
                'premium',          // Premium üyeler (gelecek için)
                'custom',           // Özel segment
            ])->default('all');
            
            // Özel hedef kitle kriterleri (JSON)
            $table->json('target_criteria')->nullable();
            
            // A/B Testing
            $table->boolean('is_ab_test')->default(false);
            $table->string('ab_test_group')->nullable(); // A, B, C, vb.
            $table->foreignId('ab_test_parent_id')->nullable()->constrained('banners')->onDelete('cascade');
            $table->integer('ab_test_weight')->default(50); // Gösterim ağırlığı (%)
            
            // İstatistikler
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('click_count')->default(0);
            
            // Durum
            $table->boolean('is_active')->default(true);
            
            // Ek ayarlar (JSON)
            $table->json('settings')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // İndeksler
            $table->index('location');
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
            $table->index('target_audience');
            $table->index('ab_test_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
