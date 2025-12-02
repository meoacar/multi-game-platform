<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Kullanıcının favori PUBG Mobile haritalarını saklamak için JSON alan ekleniyor
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->json('favorite_maps')->nullable()->after('play_style');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('favorite_maps');
        });
    }
};
