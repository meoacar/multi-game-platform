<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Rules\ValidGameId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Game ID Validation Tests
 * 
 * Requirements: 16.3 - Game ID validation in form requests
 */
class GameIdValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Test için oyunlar oluştur
        Game::factory()->create([
            'id' => 1,
            'slug' => 'pubg',
            'status' => 'active',
        ]);
        
        Game::factory()->create([
            'id' => 2,
            'slug' => 'cod',
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function it_validates_active_game_id()
    {
        $validator = Validator::make(
            ['game_id' => 1],
            ['game_id' => ['required', new ValidGameId()]]
        );

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_rejects_inactive_game_id()
    {
        $validator = Validator::make(
            ['game_id' => 2],
            ['game_id' => ['required', new ValidGameId()]]
        );

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('aktif değil', $validator->errors()->first('game_id'));
    }

    /** @test */
    public function it_rejects_non_existent_game_id()
    {
        $validator = Validator::make(
            ['game_id' => 999],
            ['game_id' => ['required', new ValidGameId()]]
        );

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('bulunamadı', $validator->errors()->first('game_id'));
    }

    /** @test */
    public function it_rejects_non_numeric_game_id()
    {
        $validator = Validator::make(
            ['game_id' => 'invalid'],
            ['game_id' => ['required', new ValidGameId()]]
        );

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('format', $validator->errors()->first('game_id'));
    }

    /** @test */
    public function it_allows_inactive_games_with_any_method()
    {
        $validator = Validator::make(
            ['game_id' => 2],
            ['game_id' => ['required', ValidGameId::any()]]
        );

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_game_context_match()
    {
        session(['game_id' => 1]);

        $validator = Validator::make(
            ['game_id' => 1],
            ['game_id' => ['required', ValidGameId::matchContext()]]
        );

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_rejects_mismatched_game_context()
    {
        session(['game_id' => 1]);
        
        // Aktif bir oyun oluştur
        $game2 = Game::factory()->create([
            'id' => 3,
            'slug' => 'valorant',
            'status' => 'active',
        ]);

        $validator = Validator::make(
            ['game_id' => $game2->id],
            ['game_id' => ['required', ValidGameId::matchContext()]]
        );

        $this->assertTrue($validator->fails());
        $this->assertStringContainsString('eşleşmiyor', $validator->errors()->first('game_id'));
    }

    /** @test */
    public function it_validates_clan_creation_with_valid_game_id()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/clans', [
                'game_id' => $game->id,
                'name' => 'Test Clan',
                'description' => 'This is a test clan description',
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_rejects_clan_creation_with_invalid_game_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/clans', [
                'game_id' => 999,
                'name' => 'Test Clan',
                'description' => 'This is a test clan description',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('game_id');
    }

    /** @test */
    public function it_validates_lfg_post_creation_with_valid_game_id()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/lfg', [
                'game_id' => $game->id,
                'title' => 'Looking for squad',
                'description' => 'Need 2 more players for ranked games',
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_rejects_lfg_post_creation_with_inactive_game()
    {
        $user = User::factory()->create();
        $inactiveGame = Game::factory()->create(['status' => 'inactive']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/lfg', [
                'game_id' => $inactiveGame->id,
                'title' => 'Looking for squad',
                'description' => 'Need 2 more players for ranked games',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('game_id');
    }

    /** @test */
    public function it_validates_community_post_validation_rules()
    {
        $game = Game::factory()->create(['status' => 'active']);

        // Valid data
        $validator = Validator::make(
            [
                'game_id' => $game->id,
                'type' => 'general',
                'content' => 'This is a test community post',
            ],
            [
                'game_id' => ['required', new ValidGameId()],
                'type' => 'required|in:intro,general',
                'content' => 'required|string|max:1000|min:10',
            ]
        );

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_rejects_community_post_without_game_id()
    {
        // Missing game_id
        $validator = Validator::make(
            [
                'type' => 'general',
                'content' => 'This is a test community post',
            ],
            [
                'game_id' => ['required', new ValidGameId()],
                'type' => 'required|in:intro,general',
                'content' => 'required|string|max:1000|min:10',
            ]
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('game_id', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_guide_with_nullable_game_id()
    {
        // Without game_id (nullable)
        $validator = Validator::make(
            [
                'title' => 'Test Guide',
                'content' => str_repeat('This is a test guide content. ', 20),
                'is_published' => true,
            ],
            [
                'game_id' => ['nullable', new ValidGameId()],
                'title' => 'required|string|max:255',
                'content' => 'required|string|min:100',
            ]
        );

        // game_id nullable olduğu için hata vermemeli
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_matchmaking_queue_with_valid_game_id()
    {
        $game = Game::factory()->create(['status' => 'active']);

        $validator = Validator::make(
            [
                'game_id' => $game->id,
                'mode' => 'squad',
            ],
            [
                'game_id' => ['required', new ValidGameId()],
                'mode' => 'required|in:squad,duo,solo',
            ]
        );

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_rejects_matchmaking_queue_without_game_id()
    {
        $validator = Validator::make(
            [
                'mode' => 'squad',
            ],
            [
                'game_id' => ['required', new ValidGameId()],
                'mode' => 'required|in:squad,duo,solo',
            ]
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('game_id', $validator->errors()->toArray());
    }
}
