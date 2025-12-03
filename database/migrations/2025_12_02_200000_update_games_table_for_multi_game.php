<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Multi-game platform için games tablosunu güncelle
     * - logo alanı ekle (icon yerine)
     * - status enum ekle (is_active yerine)
     * - settings JSON alanı ekle (theme_color, max_team_size, platforms)
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Logo alanı ekle (icon'dan sonra)
            $table->string('logo')->nullable()->after('slug');
            
            // Status enum ekle (is_active'den sonra)
            $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
            
            // Settings JSON alanı ekle
            $table->json('settings')->nullable()->after('status');
            
            // Status için index ekle
            $table->index('status');
        });
        
        // Mevcut verileri migrate et
        DB::table('games')->update([
            'status' => DB::raw("CASE WHEN is_active = 1 THEN 'active' ELSE 'inactive' END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['logo', 'status', 'settings']);
            $table->dropIndex(['games_status_index']);
        });
    }
};
