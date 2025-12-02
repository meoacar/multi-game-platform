<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Oyun listesini oluştur
     * PUBG Mobile, Call of Duty Mobile, Mobile Legends: Bang Bang
     */
    public function run(): void
    {
        $games = [
            [
                'name' => 'PUBG Mobile',
                'slug' => 'pubg-mobile',
                'description' => 'Battle royale oyunu - 100 oyuncu, tek kazanan',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Call of Duty Mobile',
                'slug' => 'call-of-duty-mobile',
                'description' => 'FPS aksiyon oyunu - Multiplayer ve Battle Royale',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Mobile Legends: Bang Bang',
                'slug' => 'mobile-legends',
                'description' => 'MOBA oyunu - 5v5 takım savaşları',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'description' => 'Battle royale oyunu - Hızlı tempolu aksiyon',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Valorant Mobile',
                'slug' => 'valorant-mobile',
                'description' => 'Taktiksel FPS oyunu (Yakında)',
                'is_active' => false,
                'order' => 5,
            ],
        ];

        foreach ($games as $game) {
            Game::create($game);
        }

        $this->command->info('✅ ' . count($games) . ' oyun eklendi!');
    }
}
