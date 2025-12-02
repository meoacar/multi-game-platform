<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use App\Models\LfgPost;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LfgTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Game::factory()->create(['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile']);
    }

    /** @test */
    public function kullanici_lfg_ilani_olusturabilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/lfg', [
                'game_id' => $game->id,
                'title' => 'Akşam squad arıyorum',
                'description' => 'Chill oyun için 2 kişi lazım',
                'min_rank' => 'Gold',
                'max_rank' => 'Ace',
                'mode' => 'Squad TPP',
                'microphone_required' => true,
                'city' => 'İstanbul',
                'play_style_tag' => 'chill',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'status']
            ]);

        $this->assertDatabaseHas('lfg_posts', [
            'user_id' => $user->id,
            'title' => 'Akşam squad arıyorum',
            'status' => 'open',
        ]);
    }

    /** @test */
    public function misafir_kullanici_lfg_ilani_olusturamaz()
    {
        $game = Game::first();

        $response = $this->postJson('/api/v1/lfg', [
            'game_id' => $game->id,
            'title' => 'Test ilan',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function kullanici_tum_lfg_ilanlarini_gorebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        LfgPost::factory()->count(3)->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->getJson('/api/v1/lfg');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function kullanici_kendi_ilanini_guncelleyebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/lfg/{$lfgPost->id}", [
                'title' => 'Güncellenmiş başlık',
                'description' => 'Güncellenmiş açıklama',
                'min_rank' => 'Diamond',
                'max_rank' => 'Ace',
                'mode' => 'Squad FPP',
                'microphone_required' => false,
                'city' => 'Ankara',
                'play_style_tag' => 'try-hard',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('lfg_posts', [
            'id' => $lfgPost->id,
            'title' => 'Güncellenmiş başlık',
        ]);
    }

    /** @test */
    public function kullanici_baska_kullanicinin_ilanini_guncelleyemez()
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

        $response = $this->actingAs($otherUser, 'sanctum')
            ->putJson("/api/v1/lfg/{$lfgPost->id}", [
                'title' => 'Hack attempt',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function kullanici_kendi_ilanini_silebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/lfg/{$lfgPost->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('lfg_posts', ['id' => $lfgPost->id]);
    }

    /** @test */
    public function lfg_ilanlari_filtrelenebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'city' => 'İstanbul',
            'play_style_tag' => 'try-hard',
        ]);

        LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'city' => 'Ankara',
            'play_style_tag' => 'chill',
        ]);

        $response = $this->getJson('/api/v1/lfg?city=İstanbul');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
