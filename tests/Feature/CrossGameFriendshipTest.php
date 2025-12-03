<?php

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossGameFriendshipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // İki farklı oyun oluştur
        $this->pubg = Game::factory()->create([
            'name' => 'PUBG Mobile',
            'slug' => 'pubg',
            'status' => 'active',
        ]);
        
        $this->cod = Game::factory()->create([
            'name' => 'Call of Duty Mobile',
            'slug' => 'cod',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function friendship_table_does_not_have_game_id_column()
    {
        // Friendships tablosunda game_id kolonu olmamalı
        $this->assertFalse(
            \Schema::hasColumn('friendships', 'game_id'),
            'Friendships tablosu game_id kolonu içermemeli (cross-game özellik)'
        );
    }

    /** @test */
    public function users_can_be_friends_across_different_games()
    {
        // İki kullanıcı oluştur
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // PUBG oyununda arkadaşlık isteği gönder
        session(['game_id' => $this->pubg->id]);
        
        $friendship = Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'pending',
        ]);

        // COD oyununa geç
        session(['game_id' => $this->cod->id]);
        
        // Arkadaşlık hala erişilebilir olmalı
        $retrievedFriendship = Friendship::where('user_id', $user1->id)
            ->where('friend_id', $user2->id)
            ->first();

        $this->assertNotNull($retrievedFriendship);
        $this->assertEquals($friendship->id, $retrievedFriendship->id);
        $this->assertEquals('pending', $retrievedFriendship->status);
    }

    /** @test */
    public function friendship_persists_when_switching_between_games()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // PUBG'de arkadaşlık oluştur
        session(['game_id' => $this->pubg->id]);
        
        $friendship = Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'accepted',
        ]);

        // COD'a geç ve arkadaşlığı kontrol et
        session(['game_id' => $this->cod->id]);
        $friendshipInCod = Friendship::find($friendship->id);
        $this->assertNotNull($friendshipInCod);
        $this->assertEquals('accepted', $friendshipInCod->status);

        // Tekrar PUBG'ye dön
        session(['game_id' => $this->pubg->id]);
        $friendshipInPubg = Friendship::find($friendship->id);
        $this->assertNotNull($friendshipInPubg);
        $this->assertEquals('accepted', $friendshipInPubg->status);
    }

    /** @test */
    public function friendship_status_changes_are_visible_across_all_games()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // PUBG'de pending arkadaşlık oluştur
        session(['game_id' => $this->pubg->id]);
        
        $friendship = Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'pending',
        ]);

        // COD'a geç ve arkadaşlığı kabul et
        session(['game_id' => $this->cod->id]);
        $friendship->accept();

        // PUBG'ye dön ve durumu kontrol et
        session(['game_id' => $this->pubg->id]);
        $friendship->refresh();
        $this->assertEquals('accepted', $friendship->status);
    }

    /** @test */
    public function user_can_see_all_friends_regardless_of_current_game()
    {
        $user = User::factory()->create();
        $friend1 = User::factory()->create();
        $friend2 = User::factory()->create();
        $friend3 = User::factory()->create();

        // Farklı oyunlarda arkadaşlıklar oluştur
        session(['game_id' => $this->pubg->id]);
        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friend1->id,
            'status' => 'accepted',
        ]);

        session(['game_id' => $this->cod->id]);
        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friend2->id,
            'status' => 'accepted',
        ]);

        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friend3->id,
            'status' => 'pending',
        ]);

        // Herhangi bir oyunda tüm arkadaşları görebilmeli
        session(['game_id' => $this->pubg->id]);
        $allFriendships = Friendship::where('user_id', $user->id)->get();
        $this->assertCount(3, $allFriendships);

        session(['game_id' => $this->cod->id]);
        $allFriendships = Friendship::where('user_id', $user->id)->get();
        $this->assertCount(3, $allFriendships);
    }

    /** @test */
    public function friendship_model_does_not_have_game_relationship()
    {
        // Friendship model'inde game() ilişkisi olmamalı
        $friendship = new Friendship();
        
        $this->assertFalse(
            method_exists($friendship, 'game'),
            'Friendship model\'inde game() ilişkisi olmamalı (cross-game özellik)'
        );
    }

    /** @test */
    public function blocking_a_friend_works_across_all_games()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // PUBG'de arkadaşlık oluştur
        session(['game_id' => $this->pubg->id]);
        
        $friendship = Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'accepted',
        ]);

        // COD'a geç ve engelle
        session(['game_id' => $this->cod->id]);
        $friendship->block();

        // PUBG'de kontrol et
        session(['game_id' => $this->pubg->id]);
        $friendship->refresh();
        $this->assertEquals('blocked', $friendship->status);
    }

    /** @test */
    public function rejecting_friendship_deletes_it_across_all_games()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // PUBG'de arkadaşlık isteği oluştur
        session(['game_id' => $this->pubg->id]);
        
        $friendship = Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'pending',
        ]);

        $friendshipId = $friendship->id;

        // COD'a geç ve reddet
        session(['game_id' => $this->cod->id]);
        $friendship->reject();

        // PUBG'de kontrol et - silinmiş olmalı
        session(['game_id' => $this->pubg->id]);
        $deletedFriendship = Friendship::find($friendshipId);
        $this->assertNull($deletedFriendship);
    }

    /** @test */
    public function friendship_unique_constraint_works_regardless_of_game_context()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // PUBG'de arkadaşlık oluştur
        session(['game_id' => $this->pubg->id]);
        
        Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'accepted',
        ]);

        // COD'a geç ve aynı arkadaşlığı oluşturmaya çalış
        session(['game_id' => $this->cod->id]);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id,
            'status' => 'pending',
        ]);
    }
}
