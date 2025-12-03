<?php

namespace Tests;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\Clan;
use App\Models\LfgPost;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Base Test Case
 * 
 * Multi-game platform için test helper metodları içerir
 */
abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    /**
     * Oyun context'ini ayarla
     * 
     * Test sırasında belirli bir oyun bağlamı oluşturur.
     * Bu, DetectGame middleware'inin yaptığı işi simüle eder.
     * 
     * @param Game $game Ayarlanacak oyun
     * @return void
     */
    protected function setGameContext(Game $game): void
    {
        session([
            'game_id' => $game->id,
            'game_slug' => $game->slug,
            'game' => $game,
        ]);
        
        // View'a da paylaş (middleware gibi)
        view()->share('currentGame', $game);
    }
    
    /**
     * Oyun context'ini temizle
     * 
     * Test sonrası veya farklı bir context test etmeden önce
     * mevcut oyun bağlamını temizler.
     * 
     * @return void
     */
    protected function clearGameContext(): void
    {
        session()->forget(['game_id', 'game_slug', 'game']);
        view()->share('currentGame', null);
    }
    
    /**
     * Test verisi ile oyun oluştur
     * 
     * Bir oyun ve ona bağlı test verileri (turnuva, klan, LFG) oluşturur.
     * Bu, oyuna özel özellikleri test etmek için kullanışlıdır.
     * 
     * @param int $tournamentCount Oluşturulacak turnuva sayısı
     * @param int $clanCount Oluşturulacak klan sayısı
     * @param int $lfgCount Oluşturulacak LFG ilanı sayısı
     * @return Game Oluşturulan oyun
     */
    protected function createGameWithData(
        int $tournamentCount = 5,
        int $clanCount = 3,
        int $lfgCount = 10
    ): Game {
        $game = Game::factory()->create();
        
        // Turnuvalar oluştur
        if ($tournamentCount > 0) {
            Tournament::factory()->count($tournamentCount)->create([
                'game_id' => $game->id
            ]);
        }
        
        // Klanlar oluştur
        if ($clanCount > 0) {
            Clan::factory()->count($clanCount)->create([
                'game_id' => $game->id
            ]);
        }
        
        // LFG ilanları oluştur
        if ($lfgCount > 0) {
            LfgPost::factory()->count($lfgCount)->create([
                'game_id' => $game->id
            ]);
        }
        
        return $game;
    }
    
    /**
     * Oyuna özel assertion: Entity'nin doğru oyuna ait olduğunu kontrol et
     * 
     * @param mixed $entity Kontrol edilecek entity
     * @param Game $game Beklenen oyun
     * @param string $message Hata mesajı
     * @return void
     */
    protected function assertBelongsToGame($entity, Game $game, string $message = ''): void
    {
        $this->assertEquals(
            $game->id,
            $entity->game_id,
            $message ?: "Entity oyun ID'si {$game->id} olmalı, {$entity->game_id} bulundu"
        );
    }
    
    /**
     * Oyuna özel assertion: Collection'daki tüm entity'lerin doğru oyuna ait olduğunu kontrol et
     * 
     * @param \Illuminate\Support\Collection $collection Kontrol edilecek collection
     * @param Game $game Beklenen oyun
     * @param string $message Hata mesajı
     * @return void
     */
    protected function assertAllBelongToGame($collection, Game $game, string $message = ''): void
    {
        $invalidItems = $collection->filter(function ($item) use ($game) {
            return $item->game_id !== $game->id;
        });
        
        $this->assertTrue(
            $invalidItems->isEmpty(),
            $message ?: "Collection'daki tüm entity'ler oyun ID {$game->id}'ye ait olmalı. " .
                       "{$invalidItems->count()} adet farklı oyuna ait entity bulundu."
        );
    }
    
    /**
     * Oyuna özel assertion: Entity'nin herhangi bir oyuna ait olmadığını kontrol et (cross-game)
     * 
     * @param mixed $entity Kontrol edilecek entity
     * @param string $message Hata mesajı
     * @return void
     */
    protected function assertCrossGame($entity, string $message = ''): void
    {
        $this->assertNull(
            $entity->game_id,
            $message ?: "Entity cross-game olmalı (game_id null), {$entity->game_id} bulundu"
        );
    }
    
    /**
     * Oyuna özel assertion: Session'da doğru oyun context'i olduğunu kontrol et
     * 
     * @param Game $game Beklenen oyun
     * @param string $message Hata mesajı
     * @return void
     */
    protected function assertGameContextIs(Game $game, string $message = ''): void
    {
        $this->assertEquals(
            $game->id,
            session('game_id'),
            $message ?: "Session'daki game_id {$game->id} olmalı, " . session('game_id') . " bulundu"
        );
        
        $this->assertEquals(
            $game->slug,
            session('game_slug'),
            $message ?: "Session'daki game_slug {$game->slug} olmalı, " . session('game_slug') . " bulundu"
        );
    }
    
    /**
     * Oyuna özel assertion: Session'da oyun context'i olmadığını kontrol et
     * 
     * @param string $message Hata mesajı
     * @return void
     */
    protected function assertNoGameContext(string $message = ''): void
    {
        $this->assertFalse(
            session()->has('game_id'),
            $message ?: "Session'da game_id olmamalı"
        );
        
        $this->assertFalse(
            session()->has('game_slug'),
            $message ?: "Session'da game_slug olmamalı"
        );
    }
    
    /**
     * Kullanıcı için oyun context'i ile giriş yap
     * 
     * Hem kullanıcı authentication hem de oyun context'i ayarlar.
     * 
     * @param User $user Giriş yapacak kullanıcı
     * @param Game $game Oyun context'i
     * @param string $guard Guard adı
     * @return $this
     */
    protected function actingAsWithGameContext(User $user, Game $game, string $guard = 'sanctum')
    {
        $this->actingAs($user, $guard);
        $this->setGameContext($game);
        
        return $this;
    }
    
    /**
     * Birden fazla oyun için izole test ortamı oluştur
     * 
     * Her oyun için ayrı test verisi oluşturur ve izolasyonu test etmek için kullanılır.
     * 
     * @param int $gameCount Oluşturulacak oyun sayısı
     * @param int $dataPerGame Her oyun için oluşturulacak veri sayısı
     * @return \Illuminate\Support\Collection Oluşturulan oyunlar
     */
    protected function createIsolatedGames(int $gameCount = 2, int $dataPerGame = 5): \Illuminate\Support\Collection
    {
        $games = collect();
        
        for ($i = 0; $i < $gameCount; $i++) {
            $game = $this->createGameWithData(
                tournamentCount: $dataPerGame,
                clanCount: $dataPerGame,
                lfgCount: $dataPerGame
            );
            
            $games->push($game);
        }
        
        return $games;
    }
}
