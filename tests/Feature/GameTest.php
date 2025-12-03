<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Services\GameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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
        // Önce tüm oyunları temizle
        Game::query()->delete();
        
        // 2 aktif, 1 inaktif oyun oluştur
        Game::factory()->count(2)->create(['is_active' => true, 'status' => 'active']);
        Game::factory()->create(['is_active' => false, 'status' => 'inactive']);

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

    /** @test */
    public function aktif_oyunlar_cache_lenir()
    {
        // Cache'i temizle
        Cache::flush();
        
        // Oyunlar oluştur
        Game::factory()->count(3)->create(['status' => 'active']);
        
        $gameService = app(GameService::class);
        
        // İlk çağrı - veritabanından gelir
        $games1 = $gameService->getActiveGames();
        
        // Cache'de olmalı
        $prefix = config('games.cache.prefix', 'game');
        $this->assertTrue(Cache::has("{$prefix}s.active"));
        
        // İkinci çağrı - cache'den gelir
        $games2 = $gameService->getActiveGames();
        
        // Aynı sonuçları vermeli
        $this->assertEquals($games1->count(), $games2->count());
    }

    /** @test */
    public function oyun_guncellendiginde_cache_temizlenir()
    {
        // Cache'i temizle
        Cache::flush();
        
        $game = Game::factory()->create(['status' => 'active']);
        $gameService = app(GameService::class);
        
        // Cache'e yükle
        $gameService->getActiveGames();
        
        $prefix = config('games.cache.prefix', 'game');
        $this->assertTrue(Cache::has("{$prefix}s.active"));
        
        // Oyunu güncelle - Observer cache'i temizlemeli
        $game->update(['name' => 'Updated Game']);
        
        // Cache temizlenmiş olmalı
        $this->assertFalse(Cache::has("{$prefix}s.active"));
    }

    /** @test */
    public function oyun_silindiginde_cache_temizlenir()
    {
        // Cache'i temizle
        Cache::flush();
        
        $game = Game::factory()->create(['status' => 'active']);
        $gameService = app(GameService::class);
        
        // Cache'e yükle
        $gameService->getActiveGames();
        
        $prefix = config('games.cache.prefix', 'game');
        $this->assertTrue(Cache::has("{$prefix}s.active"));
        
        // Oyunu sil - Observer cache'i temizlemeli
        $game->delete();
        
        // Cache temizlenmiş olmalı
        $this->assertFalse(Cache::has("{$prefix}s.active"));
    }

    /** @test */
    public function platform_istatistikleri_cache_lenir()
    {
        // Cache'i temizle
        Cache::flush();
        
        Game::factory()->count(2)->create(['status' => 'active']);
        
        $gameService = app(GameService::class);
        
        // İlk çağrı
        $stats1 = $gameService->getPlatformStats();
        
        // Cache'de olmalı
        $this->assertTrue(Cache::has('platform.stats'));
        
        // İkinci çağrı - cache'den gelir
        $stats2 = $gameService->getPlatformStats();
        
        // Aynı sonuçları vermeli
        $this->assertEquals($stats1, $stats2);
    }

    /** @test */
    public function cache_warm_up_calisir()
    {
        // Cache'i temizle
        Cache::flush();
        
        Game::factory()->count(2)->create(['status' => 'active']);
        
        $gameService = app(GameService::class);
        
        // Warm up
        $warmed = $gameService->warmUpCache();
        
        // En az 3 cache key yüklenmiş olmalı (games.active, platform.stats, ve oyunlar)
        $this->assertGreaterThanOrEqual(3, count($warmed));
        
        // Cache'lerin yüklendiğini kontrol et
        $prefix = config('games.cache.prefix', 'game');
        $this->assertTrue(Cache::has("{$prefix}s.active"));
        $this->assertTrue(Cache::has('platform.stats'));
    }

    /** @test */
    public function tum_oyun_cache_leri_temizlenebilir()
    {
        // Cache'i temizle
        Cache::flush();
        
        $games = Game::factory()->count(2)->create(['status' => 'active']);
        
        $gameService = app(GameService::class);
        
        // Cache'leri yükle
        $gameService->warmUpCache();
        
        // Cache'lerin yüklendiğini kontrol et
        $prefix = config('games.cache.prefix', 'game');
        $this->assertTrue(Cache::has("{$prefix}s.active"));
        
        // Tüm cache'leri temizle
        $cleared = $gameService->clearAllGameCaches();
        
        // En az 3 cache key temizlenmiş olmalı
        $this->assertGreaterThanOrEqual(3, count($cleared));
        
        // Cache'lerin temizlendiğini kontrol et
        $this->assertFalse(Cache::has("{$prefix}s.active"));
        $this->assertFalse(Cache::has('platform.stats'));
    }
}
