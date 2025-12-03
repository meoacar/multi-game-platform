<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kullanıcının ana oyunu
            $table->foreignId('game_id')
                ->nullable()
                ->after('id')
                ->constrained('games')
                ->nullOnDelete();
            
            // Index ekle
            $table->index('game_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropIndex(['game_id']);
            $table->dropColumn('game_id');
        });
    }
};
