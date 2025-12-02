<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * LFG İlan Başvuruları Tablosu
     * Kullanıcıların ilanlara yaptığı başvurular
     */
    public function up(): void
    {
        Schema::create('lfg_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lfg_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            
            $table->timestamps();
            
            // Index'ler
            $table->index(['lfg_post_id', 'status']);
            $table->index('user_id');
            
            // Bir kullanıcı aynı ilana sadece bir kez başvurabilir
            $table->unique(['lfg_post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lfg_applications');
    }
};
