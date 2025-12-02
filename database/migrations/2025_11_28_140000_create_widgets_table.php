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
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['html', 'recent_content', 'popular', 'ad', 'custom'])->default('html');
            $table->enum('location', ['sidebar_left', 'sidebar_right', 'footer_1', 'footer_2', 'footer_3', 'footer_4'])->default('sidebar_right');
            $table->text('content')->nullable(); // HTML içerik veya JSON config
            $table->json('settings')->nullable(); // Widget'a özel ayarlar
            $table->integer('order')->default(0); // Sıralama
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index'ler
            $table->index(['location', 'order']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
