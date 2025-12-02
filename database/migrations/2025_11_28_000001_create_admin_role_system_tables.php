<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admin rol sistemi pivot tablolarını oluştur
     */
    public function up(): void
    {
        // Kullanıcı-Rol ilişki tablosu
        Schema::create('user_role', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            $table->foreignId('role_id')
                  ->constrained('admin_roles')
                  ->onDelete('cascade');
            
            $table->primary(['user_id', 'role_id']);
            $table->timestamps();
        });

        // Rol-Yetki ilişki tablosu
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')
                  ->constrained('admin_roles')
                  ->onDelete('cascade');
            
            $table->foreignId('permission_id')
                  ->constrained('admin_permissions')
                  ->onDelete('cascade');
            
            $table->primary(['role_id', 'permission_id']);
            $table->timestamps();
        });
    }

    /**
     * Migration'ı geri al
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('user_role');
    }
};
