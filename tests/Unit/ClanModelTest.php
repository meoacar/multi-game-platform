<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Game;
use App\Models\Clan;
use App\Models\ClanApplication;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClanModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function clan_belongs_to_user_as_leader()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $clan = Clan::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $this->assertInstanceOf(User::class, $clan->user);
        $this->assertEquals($user->id, $clan->user->id);
    }

    /** @test */
    public function clan_belongs_to_game()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $clan = Clan::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $this->assertInstanceOf(Game::class, $clan->game);
        $this->assertEquals($game->id, $clan->game->id);
    }

    /** @test */
    public function clan_has_many_applications()
    {
        $leader = User::factory()->create();
        $applicant = User::factory()->create();
        Profile::factory()->create(['user_id' => $leader->id]);
        Profile::factory()->create(['user_id' => $applicant->id]);
        $game = Game::factory()->create();
        
        $clan = Clan::factory()->create([
            'user_id' => $leader->id,
            'game_id' => $game->id,
        ]);

        ClanApplication::factory()->count(3)->create([
            'clan_id' => $clan->id,
            'user_id' => $applicant->id,
        ]);

        $this->assertCount(3, $clan->applications);
        $this->assertInstanceOf(ClanApplication::class, $clan->applications->first());
    }

    /** @test */
    public function clan_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $clan = Clan::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);
        $clanId = $clan->id;

        $clan->delete();

        $this->assertSoftDeleted('clans', ['id' => $clanId]);
    }

    /** @test */
    public function clan_has_slug()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $clan = Clan::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'name' => 'Elite Squad',
        ]);

        $this->assertNotNull($clan->slug);
    }
}
