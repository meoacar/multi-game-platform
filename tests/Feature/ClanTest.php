<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use App\Models\Clan;
use App\Models\ClanApplication;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Game::factory()->create(['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile']);
    }

    /** @test */
    public function kullanici_klan_olusturabilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/clans', [
                'game_id' => $game->id,
                'name' => 'Elite Squad',
                'description' => 'Competitive clan',
                'requirements' => 'Ace rank minimum',
                'min_rank' => 'Ace',
                'max_rank' => 'Conqueror',
                'city' => 'İstanbul',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'slug']
            ]);

        $this->assertDatabaseHas('clans', [
            'user_id' => $user->id,
            'name' => 'Elite Squad',
        ]);
    }

    /** @test */
    public function misafir_kullanici_klan_olusturamaz()
    {
        $game = Game::first();

        $response = $this->postJson('/api/v1/clans', [
            'game_id' => $game->id,
            'name' => 'Test Clan',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function kullanici_klanlari_gorebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        Clan::factory()->count(3)->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->getJson('/api/v1/clans');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function kullanici_klana_basvurabilir()
    {
        $leader = User::factory()->create();
        $applicant = User::factory()->create();
        Profile::factory()->create(['user_id' => $leader->id]);
        Profile::factory()->create(['user_id' => $applicant->id]);
        $game = Game::first();
        
        $clan = Clan::factory()->create([
            'user_id' => $leader->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($applicant, 'sanctum')
            ->postJson("/api/v1/clans/{$clan->id}/apply", [
                'message' => 'Klana katılmak istiyorum',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('clan_applications', [
            'user_id' => $applicant->id,
            'clan_id' => $clan->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function klan_lideri_basvurulari_gorebilir()
    {
        $leader = User::factory()->create();
        $applicant = User::factory()->create();
        Profile::factory()->create(['user_id' => $leader->id]);
        Profile::factory()->create(['user_id' => $applicant->id]);
        $game = Game::first();
        
        $clan = Clan::factory()->create([
            'user_id' => $leader->id,
            'game_id' => $game->id,
        ]);

        ClanApplication::factory()->create([
            'user_id' => $applicant->id,
            'clan_id' => $clan->id,
        ]);

        $response = $this->actingAs($leader, 'sanctum')
            ->getJson("/api/v1/clans/{$clan->id}/applications");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function klan_lideri_basvuruyu_onaylayabilir()
    {
        $leader = User::factory()->create();
        $applicant = User::factory()->create();
        Profile::factory()->create(['user_id' => $leader->id]);
        Profile::factory()->create(['user_id' => $applicant->id]);
        $game = Game::first();
        
        $clan = Clan::factory()->create([
            'user_id' => $leader->id,
            'game_id' => $game->id,
        ]);

        $application = ClanApplication::factory()->create([
            'user_id' => $applicant->id,
            'clan_id' => $clan->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($leader, 'sanctum')
            ->postJson("/api/v1/clan-applications/{$application->id}/status", [
                'status' => 'accepted',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('clan_applications', [
            'id' => $application->id,
            'status' => 'accepted',
        ]);
    }

    /** @test */
    public function klan_lideri_olmayan_basvuruyu_onaylayamaz()
    {
        $leader = User::factory()->create();
        $otherUser = User::factory()->create();
        $applicant = User::factory()->create();
        Profile::factory()->create(['user_id' => $leader->id]);
        Profile::factory()->create(['user_id' => $otherUser->id]);
        Profile::factory()->create(['user_id' => $applicant->id]);
        $game = Game::first();
        
        $clan = Clan::factory()->create([
            'user_id' => $leader->id,
            'game_id' => $game->id,
        ]);

        $application = ClanApplication::factory()->create([
            'user_id' => $applicant->id,
            'clan_id' => $clan->id,
        ]);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->postJson("/api/v1/clan-applications/{$application->id}/status", [
                'status' => 'accepted',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function kullanici_kendi_klanini_guncelleyebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::first();
        
        $clan = Clan::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/clans/{$clan->id}", [
                'name' => 'Updated Clan Name',
                'description' => 'Updated description',
                'requirements' => 'Updated requirements',
                'min_rank' => 'Diamond',
                'max_rank' => 'Ace',
                'city' => 'Ankara',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('clans', [
            'id' => $clan->id,
            'name' => 'Updated Clan Name',
        ]);
    }
}
