<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateGameSubdomainsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Oyun subdomain değerleri güncelleniyor...');

        // Tüm oyunlar için subdomain = slug yap
        DB::table('games')->update([
            'subdomain' => DB::raw('slug')
        ]);

        $this->command->info('✅ Subdomain değerleri güncellendi!');
        
        // Kontrol
        $games = DB::table('games')->select('id', 'name', 'slug', 'subdomain')->get();
        foreach ($games as $game) {
            $this->command->info("  - {$game->name}: {$game->subdomain}");
        }
    }
}
