<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Klan Başvuruları Tablosu
     * Kullanıcıların klanlara yaptığı başvurular
     */
    public function up(): void
    {
        Schema::create('clan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('clan_id')->constrained()->onDelete('cascade');
            
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            
            $table->timestamps();
            
            // Index'ler
            $table->index(['clan_id', 'status']);
            $table->index('user_id');
            
            // Bir kullanıcı aynı klana sadece bir kez başvurabilir
            $table->unique(['user_id', 'clan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clan_applications');
    }
};
