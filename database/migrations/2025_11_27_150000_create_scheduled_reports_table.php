<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Zamanlanmış Raporlar Tablosu
 * 
 * Otomatik olarak oluşturulacak ve email ile gönderilecek raporları saklar
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Rapor adı
            $table->enum('type', ['users', 'content', 'platform', 'all']); // Rapor tipi
            $table->enum('format', ['pdf', 'excel', 'both'])->default('pdf'); // Export formatı
            $table->enum('frequency', ['daily', 'weekly', 'monthly']); // Gönderim sıklığı
            $table->string('email'); // Gönderilecek email adresi
            $table->json('recipients')->nullable(); // Birden fazla alıcı için
            $table->json('filters')->nullable(); // Rapor filtreleri (tarih aralığı vb.)
            $table->boolean('is_active')->default(true); // Aktif/pasif durumu
            $table->timestamp('last_sent_at')->nullable(); // Son gönderim zamanı
            $table->timestamp('next_send_at')->nullable(); // Sonraki gönderim zamanı
            $table->unsignedBigInteger('created_by')->nullable(); // Oluşturan admin
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['is_active', 'next_send_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
    }
};
