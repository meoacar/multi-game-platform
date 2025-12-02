<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use App\Models\Clan;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebClanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Game::factory()->create(['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile']);
    }

    /** @test */
    public function klan_listesi_goruntulenebilir()
    {
        $response = $this->get('/klanlar');

        $response->assertStatus(200);
        $response->assertViewIs('clans.index');
    }

    /** @test */
    public function klan_detay_sayfasi_goruntulenebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        $clan = Clan::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->get("/klanlar/{$clan->slug}");

        $response->assertStatus(200);
        $response->assertViewIs('clans.show');
        $response->assertViewHas('clan');
    }

    /** @test */
    public function giris_yapmis_kullanici_klan_olusturma_sayfasini_gorebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/klanlar/yeni');

        $response->assertStatus(200);
        $response->assertViewIs('clans.create');
    }

    /** @test */
    public function misafir_kullanici_klan_olusturma_sayfasini_goremez()
    {
        $response = $this->get('/klanlar/yeni');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function klan_lideri_klan_duzenleme_sayfasini_gorebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        $clan = Clan::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($user)->get("/klanlar/{$clan->slug}/duzenle");

        $response->assertStatus(200);
        $response->assertViewIs('clans.edit');
    }

    /** @test */
    public function klan_lideri_olmayan_klan_duzenleme_sayfasini_goremez()
    {
        $leader = User::factory()->create();
        $otherUser = User::factory()->create();
        Profile::factory()->create(['user_id' => $leader->id]);
        Profile::factory()->create(['user_id' => $otherUser->id]);
        $game = Game::first();
        
        $clan = Clan::factory()->create([
            'user_id' => $leader->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($otherUser)->get("/klanlar/{$clan->slug}/duzenle");

        $response->assertStatus(403);
    }
}
