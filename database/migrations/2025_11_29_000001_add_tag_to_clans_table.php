<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Clans tablosuna tag kolonu ekle
     */
    public function up(): void
    {
        Schema::table('clans', function (Blueprint $table) {
            $table->string('tag', 10)->nullable()->after('name');
            $table->index('tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clans', function (Blueprint $table) {
            $table->dropIndex(['tag']);
            $table->dropColumn('tag');
        });
    }
};
