<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reports tablosunu düzelt
     */
    public function up(): void
    {
        // Sadece eksik kolonları ekle (rename işlemi kaldırıldı)
        Schema::table('reports', function (Blueprint $table) {
            // Status enum'ı güncelle
            DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'resolved', 'rejected') DEFAULT 'pending'");
            
            // Yeni kolonlar ekle
            if (!Schema::hasColumn('reports', 'priority')) {
                $table->string('priority')->default('medium')->after('status');
            }
            if (!Schema::hasColumn('reports', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('admin_comment');
            }
            if (!Schema::hasColumn('reports', 'resolved_by')) {
                $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->after('resolved_at');
            }
            if (!Schema::hasColumn('reports', 'resolution_note')) {
                $table->text('resolution_note')->nullable()->after('resolved_by');
            }
        });
    }

    /**
     * Migration'ı geri al
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['priority', 'resolved_at', 'resolved_by', 'resolution_note']);
        });
    }
};
