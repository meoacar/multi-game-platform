<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admin rolleri tablosunu oluştur.
     * Bu tablo admin panelinde kullanılacak rolleri (super_admin, moderator, content_manager) saklar.
     */
    public function up(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Rol adı (örn: Süper Admin)');
            $table->string('slug')->unique()->comment('Rol slug (örn: super_admin)');
            $table->text('description')->nullable()->comment('Rol açıklaması');
            $table->string('color', 7)->default('#3B82F6')->comment('Rol rengi (hex format)');
            $table->string('icon', 50)->default('shield')->comment('Rol ikonu');
            $table->boolean('is_system')->default(false)->comment('Sistem rolü mu? (silinemez)');
            $table->timestamps();
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_roles');
    }
};
