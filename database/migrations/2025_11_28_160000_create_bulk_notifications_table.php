<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Toplu bildirimler tablosunu oluştur.
     * Bu tablo admin panelinden kullanıcılara toplu bildirim göndermek için kullanılır.
     */
    public function up(): void
    {
        Schema::create('bulk_notifications', function (Blueprint $table) {
            $table->id();
            
            // Bildirim içeriği
            $table->string('title'); // Bildirim başlığı
            $table->text('message'); // Bildirim mesajı
            
            // Bildirim türü (email, sms, push, site içi)
            $table->enum('type', ['email', 'sms', 'push', 'site']);
            
            // Hedef kitle belirleme
            $table->enum('target_type', ['all', 'segment', 'custom']);
            $table->json('target_criteria')->nullable(); // Segment kriterleri (örn: aktif kullanıcılar, yeni kullanıcılar)
            $table->json('user_ids')->nullable(); // Özel kullanıcı listesi
            
            // Durum takibi
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
            
            // Zamanlama
            $table->timestamp('scheduled_at')->nullable(); // Zamanlanmış gönderim tarihi
            $table->timestamp('sent_at')->nullable(); // Gerçek gönderim tarihi
            
            // İstatistikler
            $table->unsignedInteger('total_recipients')->default(0); // Toplam alıcı sayısı
            $table->unsignedInteger('sent_count')->default(0); // Başarıyla gönderilen sayısı
            $table->unsignedInteger('failed_count')->default(0); // Başarısız gönderim sayısı
            
            // Oluşturan admin
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Index'ler
            $table->index('status');
            $table->index('type');
            $table->index('scheduled_at');
            $table->index('created_by');
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_notifications');
    }
};
