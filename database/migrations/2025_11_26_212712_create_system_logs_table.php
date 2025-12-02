<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sistem logları tablosunu oluştur.
     * Bu tablo sistem hatalarını, uyarılarını ve güvenlik olaylarını saklar.
     */
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['error', 'warning', 'info', 'security', 'performance'])
                  ->comment('Log tipi');
            $table->string('level', 50)->comment('Log seviyesi (örn: critical, error, warning, info, debug)');
            $table->text('message')->comment('Log mesajı');
            $table->json('context')
                  ->nullable()
                  ->comment('Ek bağlam bilgileri (JSON)');
            $table->text('stack_trace')
                  ->nullable()
                  ->comment('Hata stack trace (sadece error tipinde)');
            $table->string('ip_address', 45)
                  ->nullable()
                  ->comment('IP adresi (IPv4 veya IPv6)');
            $table->text('user_agent')
                  ->nullable()
                  ->comment('Kullanıcı tarayıcı bilgisi');
            $table->string('url', 500)
                  ->nullable()
                  ->comment('İstek URL\'i');
            $table->timestamp('created_at')->comment('Log oluşturulma zamanı');
            
            // Index'ler
            $table->index('type');
            $table->index('level');
            $table->index('created_at');
            $table->index('ip_address');
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
