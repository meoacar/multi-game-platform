<?php

namespace Tests\Feature;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tum_oyunlar_listelenebilir()
    {
        Game::factory()->count(3)->create(['is_active' => true]);

        $response = $this->getJson('/api/v1/games');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function sadece_aktif_oyunlar_listelenebilir()
    {
        Game::factory()->count(2)->create(['is_active' => true]);
        Game::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/games?is_active=1');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function oyun_detayi_goruntulenebilir()
    {
        $game = Game::factory()->create();

        $response = $this->getJson("/api/v1/games/{$game->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'slug', 'is_active']
            ]);
    }
}
