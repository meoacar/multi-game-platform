<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Models\Tournament;
use App\Models\Clan;
use App\Models\LfgPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test Helpers Test
 * 
 * TestCase sınıfındaki helper metodların doğru çalıştığını test eder
 * Requirements: 17.1, 17.2, 17.4
 */
class TestHelpersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function setGameContext_oyun_contextini_dogru_ayarlar()
    {
        $game = Game::factory()->create();
        
        $this->setGameContext($game);
        
        $this->assertEquals($game->id, session('game_id'));
        $this->assertEquals($game->slug, session('game_slug'));
        $this->assertNotNull(session('game'));
    }

    /** @test */
    public function clearGameContext_oyun_contextini_temizler()
    {
        $game = Game::factory()->create();
        $this->setGameContext($game);
        
        $this->clearGameContext();
        
        $this->assertFalse(session()->has('game_id'));
        $this->assertFalse(session()->has('game_slug'));
        $this->assertFalse(session()->has('game'));
    }

    /** @test */
    public function createGameWithData_oyun_ve_iliskili_verileri_olusturur()
    {
        $game = $this->createGameWithData(
            tournamentCount: 3,
            clanCount: 2,
            lfgCount: 5
        );
        
        $this->assertInstanceOf(Game::class, $game);
        $this->assertEquals(3, Tournament::where('game_id', $game->id)->count());
        $this->assertEquals(2, Clan::where('game_id', $game->id)->count());
        $this->assertEquals(5, LfgPost::where('game_id', $game->id)->count());
    }

    /** @test */
    public function createGameWithData_sifir_veri_ile_calisir()
    {
        $game = $this->createGameWithData(
            tournamentCount: 0,
            clanCount: 0,
            lfgCount: 0
        );
        
        $this->assertInstanceOf(Game::class, $game);
        $this->assertEquals(0, Tournament::where('game_id', $game->id)->count());
        $this->assertEquals(0, Clan::where('game_id', $game->id)->count());
        $this->assertEquals(0, LfgPost::where('game_id', $game->id)->count());
    }

    /** @test */
    public function assertBelongsToGame_dogru_oyuna_ait_entityyi_dogrular()
    {
        $game = Game::factory()->create();
        $tournament = Tournament::factory()->create(['game_id' => $game->id]);
        
        $this->assertBelongsToGame($tournament, $game);
        
        // Test geçti, assertion başarılı
        $this->assertTrue(true);
    }

    /** @test */
    public function assertAllBelongToGame_collectiondaki_tum_entityleri_dogrular()
    {
        $game = Game::factory()->create();
        $tournaments = Tournament::factory()->count(5)->create(['game_id' => $game->id]);
        
        $this->assertAllBelongToGame($tournaments, $game);
        
        // Test geçti, assertion başarılı
        $this->assertTrue(true);
    }

    /** @test */
    public function assertCrossGame_cross_game_entityyi_dogrular()
    {
        $user = User::factory()->create();
        
        $this->assertCrossGame($user);
        
        // Test geçti, assertion başarılı
        $this->assertTrue(true);
    }

    /** @test */
    public function assertGameContextIs_session_contextini_dogrular()
    {
        $game = Game::factory()->create();
        $this->setGameContext($game);
        
        $this->assertGameContextIs($game);
        
        // Test geçti, assertion başarılı
        $this->assertTrue(true);
    }

    /** @test */
    public function assertNoGameContext_bos_contexti_dogrular()
    {
        $this->clearGameContext();
        
        $this->assertNoGameContext();
        
        // Test geçti, assertion başarılı
        $this->assertTrue(true);
    }

    /** @test */
    public function actingAsWithGameContext_kullanici_ve_oyun_contextini_ayarlar()
    {
        $user = User::factory()->create();
        $game = Game::factory()->create();
        
        $this->actingAsWithGameContext($user, $game);
        
        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
        $this->assertEquals($game->id, session('game_id'));
    }

    /** @test */
    public function createIsolatedGames_birden_fazla_izole_oyun_olusturur()
    {
        $games = $this->createIsolatedGames(gameCount: 3, dataPerGame: 2);
        
        $this->assertCount(3, $games);
        
        foreach ($games as $game) {
            $this->assertEquals(2, Tournament::where('game_id', $game->id)->count());
            $this->assertEquals(2, Clan::where('game_id', $game->id)->count());
            $this->assertEquals(2, LfgPost::where('game_id', $game->id)->count());
        }
    }

    /** @test */
    public function createIsolatedGames_oyunlar_arasi_veri_izolasyonu_saglar()
    {
        $games = $this->createIsolatedGames(gameCount: 2, dataPerGame: 5);
        
        $game1 = $games->first();
        $game2 = $games->last();
        
        // Her oyunun kendi verisi olmalı
        $game1Tournaments = Tournament::where('game_id', $game1->id)->get();
        $game2Tournaments = Tournament::where('game_id', $game2->id)->get();
        
        $this->assertCount(5, $game1Tournaments);
        $this->assertCount(5, $game2Tournaments);
        
        // Hiçbir turnuva diğer oyuna ait olmamalı
        $this->assertAllBelongToGame($game1Tournaments, $game1);
        $this->assertAllBelongToGame($game2Tournaments, $game2);
    }

    /** @test */
    public function helper_metodlar_birlikte_calisir()
    {
        // Oyun ve veri oluştur
        $game = $this->createGameWithData(tournamentCount: 3);
        
        // Context ayarla
        $this->setGameContext($game);
        
        // Context'i doğrula
        $this->assertGameContextIs($game);
        
        // Verileri kontrol et
        $tournaments = Tournament::where('game_id', $game->id)->get();
        $this->assertAllBelongToGame($tournaments, $game);
        
        // Context'i temizle
        $this->clearGameContext();
        
        // Temizlendiğini doğrula
        $this->assertNoGameContext();
    }

    /** @test */
    public function helper_metodlar_farkli_oyunlarla_calisir()
    {
        $game1 = Game::factory()->create(['slug' => 'pubg']);
        $game2 = Game::factory()->create(['slug' => 'codm']);
        
        // İlk oyun context'i
        $this->setGameContext($game1);
        $this->assertGameContextIs($game1);
        
        // İkinci oyun context'ine geç
        $this->setGameContext($game2);
        $this->assertGameContextIs($game2);
        
        // İlk oyun context'i artık geçerli değil
        $this->assertNotEquals($game1->id, session('game_id'));
    }
}
