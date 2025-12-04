<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('subdomain')->nullable()->after('slug');
            $table->unique('subdomain');
        });

        // Mevcut oyunlar için subdomain değerlerini slug'dan oluştur
        DB::table('games')->update([
            'subdomain' => DB::raw('slug')
        ]);
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropUnique(['subdomain']);
            $table->dropColumn('subdomain');
        });
    }
};
