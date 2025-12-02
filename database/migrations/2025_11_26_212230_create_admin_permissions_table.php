<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admin yetkileri tablosunu oluştur.
     * Bu tablo admin panelinde kullanılacak granular yetkileri saklar.
     */
    public function up(): void
    {
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Yetki adı (örn: Kullanıcı Düzenleme)');
            $table->string('slug')->unique()->comment('Yetki slug (örn: users.edit)');
            $table->string('group', 100)->comment('Yetki grubu (örn: users, content, moderation)');
            $table->text('description')->nullable()->comment('Yetki açıklaması');
            $table->timestamps();
            
            // Index'ler
            $table->index('group');
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_permissions');
    }
};
