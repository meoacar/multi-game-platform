<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kullanıcı-Yetki pivot tablosunu oluştur.
     * Bu tablo kullanıcılara özel olarak verilen veya kaldırılan yetkileri saklar.
     * granted = true: Yetki verildi, granted = false: Yetki kaldırıldı
     */
    public function up(): void
    {
        Schema::create('user_permission', function (Blueprint $table) {
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Kullanıcı ID');
            
            $table->foreignId('permission_id')
                  ->constrained('admin_permissions')
                  ->onDelete('cascade')
                  ->comment('Yetki ID');
            
            $table->boolean('granted')
                  ->default(true)
                  ->comment('Yetki verildi mi? (true: verildi, false: kaldırıldı)');
            
            // Composite primary key
            $table->primary(['user_id', 'permission_id']);
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permission');
    }
};
