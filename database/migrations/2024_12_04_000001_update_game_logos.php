<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Oyun logolarını güncelle
     * 
     * public/images/analogolar klasöründeki resimleri kullan
     */
    public function up(): void
    {
        // Logo path'lerini güncelle
        $games = [
            ['slug' => 'pubg', 'logo' => 'images/analogolar/pubgmoile.jpg'],
            ['slug' => 'cod', 'logo' => 'images/analogolar/callofduty.jpg'],
            ['slug' => 'valorant', 'logo' => 'images/analogolar/valo.webp'],
            ['slug' => 'csgo', 'logo' => 'images/analogolar/csgo.png'],
            ['slug' => 'lol', 'logo' => 'images/analogolar/lol.jpg'],
        ];

        foreach ($games as $game) {
            DB::table('games')
                ->where('slug', $game['slug'])
                ->update(['logo' => $game['logo']]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('games')->update(['logo' => null]);
    }
};
