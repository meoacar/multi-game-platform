<?php

namespace Tests\Feature;

use App\Exceptions\GameContextException;
use App\Models\Clan;
use App\Models\Game;
use App\Models\LfgPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Game Context Authorization Test
 * 
 * Bu test, oyun bağlamı (game context) yetkilendirme kontrollerini test eder.
 * 
 * Test Edilen Özellikler:
 * - Cross-game erişim engelleme
 * - Game context kontrolü
 * - Admin bypass yetkisi
 * 
 * Requirements: 16.1, 16.2
 */
class GameContextAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Game $game1;
    protected Game $game2;

    protected function setUp(): void
    {
        parent::setUp();

        // Test kullanıcıları oluştur
        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);

        // Test oyunları oluştur
        $this->game1 = Game::factory()->create(['slug' => 'pubg', 'status' => 'active']);
        $this->game2 = Game::factory()->create(['slug' => 'cod', 'status' => 'active']);
    }

    /** @test */
    public function user_cannot_view_clan_from_different_game()
    {
        // Game 1'de klan oluştur
        $clan = Clan::factory()->create(['game_id' => $this->game1->id]);

        // Game 2 context'inde
        session(['game_id' => $this->game2->id]);

        // Kullanıcı game 2'deyken game 1'in klanını görmeye çalışır
        $this->actingAs($this->user);

        $this->expectException(GameContextException::class);
        $this->expectExceptionMessage('başka bir oyuna ait');

        // Policy kontrolü
        $this->user->can('view', $clan);
        
        // Manuel policy çağrısı
        app(\App\Policies\ClanPolicy::class)->view($this->user, $clan);
    }

    /** @test */
    public function user_cannot_update_lfg_post_from_different_game()
    {
        // Game 1'de LFG ilanı oluştur
        $lfgPost = LfgPost::factory()->create([
            'game_id' => $this->game1->id,
            'user_id' => $this->user->id,
        ]);

        // Game 2 context'inde
        session(['game_id' => $this->game2->id]);

        // Kullanıcı kendi ilanını bile farklı game context'te güncelleyemez
        $this->actingAs($this->user);

        $this->expectException(GameContextException::class);

        // Policy kontrolü
        app(\App\Policies\LfgPostPolicy::class)->update($this->user, $lfgPost);
    }

    /** @test */
    public function admin_can_bypass_game_context_for_viewing()
    {
        // Game 1'de klan oluştur
        $clan = Clan::factory()->create(['game_id' => $this->game1->id]);

        // Game 2 context'inde
        session(['game_id' => $this->game2->id]);

        // Admin kullanıcı farklı oyunun klanını görebilir
        $this->actingAs($this->admin);

        // Policy kontrolü - exception fırlatmamalı
        $canView = app(\App\Policies\ClanPolicy::class)->view($this->admin, $clan);

        $this->assertTrue($canView);
    }

    /** @test */
    public function user_can_view_resource_in_correct_game_context()
    {
        // Game 1'de klan oluştur
        $clan = Clan::factory()->create(['game_id' => $this->game1->id]);

        // Game 1 context'inde
        session(['game_id' => $this->game1->id]);

        // Kullanıcı doğru game context'te klanı görebilir
        $this->actingAs($this->user);

        // Policy kontrolü - exception fırlatmamalı
        $canView = app(\App\Policies\ClanPolicy::class)->view($this->user, $clan);

        $this->assertTrue($canView);
    }

    /** @test */
    public function user_can_update_own_resource_in_correct_game_context()
    {
        // Game 1'de LFG ilanı oluştur
        $lfgPost = LfgPost::factory()->create([
            'game_id' => $this->game1->id,
            'user_id' => $this->user->id,
        ]);

        // Game 1 context'inde
        session(['game_id' => $this->game1->id]);

        // Kullanıcı doğru game context'te kendi ilanını güncelleyebilir
        $this->actingAs($this->user);

        // Policy kontrolü - exception fırlatmamalı
        $canUpdate = app(\App\Policies\LfgPostPolicy::class)->update($this->user, $lfgPost);

        $this->assertTrue($canUpdate);
    }

    /** @test */
    public function missing_game_context_throws_exception()
    {
        // Klan oluştur
        $clan = Clan::factory()->create(['game_id' => $this->game1->id]);

        // Game context yok
        session()->forget(['game_id', 'game']);

        // Kullanıcı game context olmadan erişmeye çalışır
        $this->actingAs($this->user);

        $this->expectException(GameContextException::class);
        $this->expectExceptionMessage('Oyun bağlamı bulunamadı');

        // Policy kontrolü
        app(\App\Policies\ClanPolicy::class)->view($this->user, $clan);
    }

    /** @test */
    public function user_can_create_resource_without_game_context_check()
    {
        // Game 1 context'inde
        session(['game_id' => $this->game1->id]);

        // Kullanıcı yeni kaynak oluşturabilir (create policy'de game context kontrolü yok)
        $this->actingAs($this->user);

        // Policy kontrolü - exception fırlatmamalı
        $canCreate = app(\App\Policies\ClanPolicy::class)->create($this->user);

        $this->assertTrue($canCreate);
    }
}
