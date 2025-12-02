<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Yedekleme logları tablosunu oluştur.
     * Bu tablo veritabanı ve dosya yedekleme işlemlerinin kaydını tutar.
     */
    public function up(): void
    {
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            
            // Yedekleme tipi: veritabanı, dosyalar veya tam yedek
            $table->enum('type', ['database', 'files', 'full'])->comment('Yedekleme tipi');
            
            // Yedekleme durumu
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])
                  ->comment('Yedekleme durumu');
            
            // Yedek dosya bilgileri
            $table->string('file_path', 500)->nullable()->comment('Yedek dosyasının yolu');
            $table->unsignedBigInteger('file_size')->nullable()->comment('Yedek dosya boyutu (byte)');
            
            // Zaman bilgileri
            $table->timestamp('started_at')->nullable()->comment('Yedekleme başlangıç zamanı');
            $table->timestamp('completed_at')->nullable()->comment('Yedekleme tamamlanma zamanı');
            
            // Hata bilgisi
            $table->text('error_message')->nullable()->comment('Hata mesajı (başarısız ise)');
            
            // Yedeklemeyi oluşturan admin
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Yedeklemeyi oluşturan admin kullanıcı');
            
            $table->timestamp('created_at')->useCurrent()->comment('Kayıt oluşturulma zamanı');
            
            // İndeksler - performans için
            $table->index('type', 'idx_backup_logs_type');
            $table->index('status', 'idx_backup_logs_status');
            $table->index('created_by', 'idx_backup_logs_created_by');
            $table->index('created_at', 'idx_backup_logs_created_at');
            $table->index(['type', 'status'], 'idx_backup_logs_type_status');
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
