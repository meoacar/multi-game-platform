<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use App\Models\LfgPost;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebLfgTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Game::factory()->create(['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile']);
    }

    /** @test */
    public function lfg_listesi_goruntulenebilir()
    {
        $response = $this->get('/ilanlar');

        $response->assertStatus(200);
        $response->assertViewIs('lfg.index');
    }

    /** @test */
    public function lfg_detay_sayfasi_goruntulenebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->get("/ilanlar/{$lfgPost->id}");

        $response->assertStatus(200);
        $response->assertViewIs('lfg.show');
        $response->assertViewHas('lfgPost');
    }

    /** @test */
    public function giris_yapmis_kullanici_ilan_olusturma_sayfasini_gorebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/ilanlar/yeni');

        $response->assertStatus(200);
        $response->assertViewIs('lfg.create');
    }

    /** @test */
    public function misafir_kullanici_ilan_olusturma_sayfasini_goremez()
    {
        $response = $this->get('/ilanlar/yeni');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function kullanici_kendi_ilanini_duzenleme_sayfasini_gorebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($user)->get("/ilanlar/{$lfgPost->id}/duzenle");

        $response->assertStatus(200);
        $response->assertViewIs('lfg.edit');
    }

    /** @test */
    public function kullanici_baska_kullanicinin_ilanini_duzenleme_sayfasini_goremez()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        Profile::factory()->create(['user_id' => $owner->id]);
        Profile::factory()->create(['user_id' => $otherUser->id]);
        $game = Game::first();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $owner->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($otherUser)->get("/ilanlar/{$lfgPost->id}/duzenle");

        $response->assertStatus(403);
    }
}
