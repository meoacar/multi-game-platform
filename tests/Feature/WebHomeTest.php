<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use App\Models\LfgPost;
use App\Models\Clan;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebHomeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ana_sayfa_goruntulenebilir()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /** @test */
    public function ana_sayfa_son_ilanlari_gosterir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        LfgPost::factory()->count(3)->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('recentLfgPosts');
    }

    /** @test */
    public function ana_sayfa_klanlari_gosterir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        Clan::factory()->count(2)->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('featuredClans');
    }

    /** @test */
    public function ana_sayfa_istatistikleri_gosterir()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('totalUsers');
        $response->assertViewHas('totalLfgPosts');
        $response->assertViewHas('totalClans');
    }
}
