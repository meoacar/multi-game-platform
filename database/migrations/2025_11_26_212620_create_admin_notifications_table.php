<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admin bildirimleri tablosunu oluştur.
     * Bu tablo admin panelinde gösterilecek bildirimleri saklar.
     */
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100)->comment('Bildirim tipi (örn: report, user_registered, content_flagged)');
            $table->string('title')->comment('Bildirim başlığı');
            $table->text('message')->comment('Bildirim mesajı');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])
                  ->default('medium')
                  ->comment('Öncelik seviyesi');
            $table->enum('target_type', ['all', 'role', 'user'])
                  ->default('all')
                  ->comment('Hedef tip (tüm adminler, belirli rol, belirli kullanıcı)');
            $table->unsignedBigInteger('target_id')
                  ->nullable()
                  ->comment('Hedef ID (role_id veya user_id)');
            $table->json('read_by')
                  ->nullable()
                  ->comment('Okuyan admin ID\'leri (JSON array)');
            $table->string('action_url', 500)
                  ->nullable()
                  ->comment('Tıklandığında gidilecek URL');
            $table->timestamp('expires_at')
                  ->nullable()
                  ->comment('Bildirim son geçerlilik tarihi');
            $table->timestamps();
            
            // Index'ler
            $table->index('type');
            $table->index('priority');
            $table->index('target_type');
            $table->index('created_at');
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
