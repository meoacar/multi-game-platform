<?php

namespace Tests\Feature\Api;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\Clan;
use App\Models\LfgPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Game API Test
 * 
 * API endpoint'lerinin doğru çalıştığını test eder
 * 
 * Requirements: 12.1, 12.2, 12.3, 12.4, 12.5
 */
class GameApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Oyun listesi endpoint'i çalışıyor
     * 
     * @test
     * @validates Requirements 12.1
     */
    public function it_returns_list_of_games()
    {
        // Arrange: Oyunlar oluştur
        Game::factory()->count(3)->create(['status' => 'active']);
        Game::factory()->count(2)->create(['status' => 'inactive']);

        // Act: API'yi çağır
        $response = $this->getJson('/api/v1/games');

        // Assert: Tüm oyunlar dönmeli
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'logo',
                        'description',
                        'status',
                        'settings',
                    ]
                ],
                'meta' => [
                    'total',
                    'active_count',
                    'inactive_count',
                ]
            ])
            ->assertJson([
                'success' => true,
                'meta' => [
                    'total' => 5,
                    'active_count' => 3,
                    'inactive_count' => 2,
                ]
            ]);
    }

    /**
     * Test: Aktif oyunları filtreleme
     * 
     * @test
     * @validates Requirements 12.1, 12.3
     */
    public function it_filters_active_games()
    {
        // Arrange
        Game::factory()->count(3)->create(['status' => 'active']);
        Game::factory()->count(2)->create(['status' => 'inactive']);

        // Act: Sadece aktif oyunları iste
        $response = $this->getJson('/api/v1/games?status=active');

        // Assert: Sadece aktif oyunlar dönmeli
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');

        $games = $response->json('data');
        foreach ($games as $game) {
            $this->assertEquals('active', $game['status']);
        }
    }

    /**
     * Test: İnaktif oyunları filtreleme
     * 
     * @test
     * @validates Requirements 12.1, 12.3
     */
    public function it_filters_inactive_games()
    {
        // Arrange
        Game::factory()->count(3)->create(['status' => 'active']);
        Game::factory()->count(2)->create(['status' => 'inactive']);

        // Act: Sadece inaktif oyunları iste
        $response = $this->getJson('/api/v1/games?status=inactive');

        // Assert: Sadece inaktif oyunlar dönmeli
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $games = $response->json('data');
        foreach ($games as $game) {
            $this->assertEquals('inactive', $game['status']);
        }
    }

    /**
     * Test: Backward compatibility - is_active parametresi
     * 
     * @test
     * @validates Requirements 12.5
     */
    public function it_supports_backward_compatible_is_active_parameter()
    {
        // Arrange
        Game::factory()->count(3)->create(['status' => 'active']);
        Game::factory()->count(2)->create(['status' => 'inactive']);

        // Act: Eski format ile aktif oyunları iste
        $response = $this->getJson('/api/v1/games?is_active=1');

        // Assert: Sadece aktif oyunlar dönmeli
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test: İstatistiklerle birlikte oyun listesi
     * 
     * @test
     * @validates Requirements 12.4
     */
    public function it_returns_games_with_statistics()
    {
        // Arrange: Oyun ve ilişkili veriler oluştur
        $game = Game::factory()->create(['status' => 'active']);
        Tournament::factory()->count(5)->create(['game_id' => $game->id]);
        Clan::factory()->count(3)->create(['game_id' => $game->id]);
        LfgPost::factory()->count(7)->create(['game_id' => $game->id]);

        // Act: İstatistiklerle birlikte iste
        $response = $this->getJson('/api/v1/games?with_stats=1');

        // Assert: İstatistikler dönmeli
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'stats' => [
                            'tournaments_count',
                            'clans_count',
                            'lfg_posts_count',
                        ]
                    ]
                ]
            ]);

        $gameData = $response->json('data.0');
        $this->assertEquals(5, $gameData['stats']['tournaments_count']);
        $this->assertEquals(3, $gameData['stats']['clans_count']);
        $this->assertEquals(7, $gameData['stats']['lfg_posts_count']);
    }

    /**
     * Test: ID ile oyun detayı
     * 
     * @test
     * @validates Requirements 12.2
     */
    public function it_returns_game_by_id()
    {
        // Arrange
        $game = Game::factory()->create([
            'name' => 'PUBG Mobile',
            'slug' => 'pubg',
            'status' => 'active',
        ]);

        // Act: ID ile oyun detayını al
        $response = $this->getJson("/api/v1/games/{$game->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $game->id,
                    'name' => 'PUBG Mobile',
                    'slug' => 'pubg',
                    'status' => 'active',
                ]
            ]);
    }

    /**
     * Test: Slug ile oyun detayı
     * 
     * @test
     * @validates Requirements 12.2
     */
    public function it_returns_game_by_slug()
    {
        // Arrange
        $game = Game::factory()->create([
            'name' => 'PUBG Mobile',
            'slug' => 'pubg',
            'status' => 'active',
        ]);

        // Act: Slug ile oyun detayını al
        $response = $this->getJson('/api/v1/games/pubg');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $game->id,
                    'name' => 'PUBG Mobile',
                    'slug' => 'pubg',
                    'status' => 'active',
                ]
            ]);
    }

    /**
     * Test: İstatistiklerle birlikte oyun detayı
     * 
     * @test
     * @validates Requirements 12.4
     */
    public function it_returns_game_detail_with_statistics()
    {
        // Arrange
        $game = Game::factory()->create(['slug' => 'pubg']);
        Tournament::factory()->count(10)->create(['game_id' => $game->id]);
        Clan::factory()->count(5)->create(['game_id' => $game->id]);

        // Act: İstatistiklerle birlikte iste
        $response = $this->getJson('/api/v1/games/pubg?with_stats=1');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'stats' => [
                        'tournaments_count',
                        'clans_count',
                    ]
                ]
            ]);

        $gameData = $response->json('data');
        $this->assertEquals(10, $gameData['stats']['tournaments_count']);
        $this->assertEquals(5, $gameData['stats']['clans_count']);
    }

    /**
     * Test: Olmayan oyun 404 döner
     * 
     * @test
     */
    public function it_returns_404_for_non_existent_game()
    {
        // Act: Olmayan oyunu iste
        $response = $this->getJson('/api/v1/games/999');

        // Assert: 404 dönmeli
        $response->assertStatus(404);
    }

    /**
     * Test: Olmayan slug 404 döner
     * 
     * @test
     */
    public function it_returns_404_for_non_existent_slug()
    {
        // Act: Olmayan slug ile iste
        $response = $this->getJson('/api/v1/games/non-existent-game');

        // Assert: 404 dönmeli
        $response->assertStatus(404);
    }

    /**
     * Test: Oyun ayarları JSON formatında dönüyor
     * 
     * @test
     * @validates Requirements 12.4
     */
    public function it_returns_game_settings_as_json()
    {
        // Arrange
        $game = Game::factory()->create([
            'slug' => 'pubg',
            'settings' => [
                'theme_color' => '#FF6B00',
                'max_team_size' => 4,
                'platforms' => ['Android', 'iOS'],
            ]
        ]);

        // Act
        $response = $this->getJson('/api/v1/games/pubg');

        // Assert: Settings JSON olarak dönmeli
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'settings' => [
                        'theme_color' => '#FF6B00',
                        'max_team_size' => 4,
                        'platforms' => ['Android', 'iOS'],
                    ]
                ]
            ]);
    }
}
