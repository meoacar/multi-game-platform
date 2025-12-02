<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Game;
use App\Models\LfgPost;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LfgPostModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lfg_post_belongs_to_user()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $this->assertInstanceOf(User::class, $lfgPost->user);
        $this->assertEquals($user->id, $lfgPost->user->id);
    }

    /** @test */
    public function lfg_post_belongs_to_game()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $this->assertInstanceOf(Game::class, $lfgPost->game);
        $this->assertEquals($game->id, $lfgPost->game->id);
    }

    /** @test */
    public function lfg_post_default_status_is_open()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $this->assertEquals('open', $lfgPost->status);
    }

    /** @test */
    public function lfg_post_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);
        $lfgPostId = $lfgPost->id;

        $lfgPost->delete();

        $this->assertSoftDeleted('lfg_posts', ['id' => $lfgPostId]);
    }

    /** @test */
    public function lfg_post_has_required_fields()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $game = Game::factory()->create();
        
        $lfgPost = LfgPost::factory()->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'title' => 'Test Title',
            'description' => 'Test Description',
            'min_rank' => 'Gold',
            'max_rank' => 'Ace',
            'mode' => 'Squad TPP',
        ]);

        $this->assertEquals('Test Title', $lfgPost->title);
        $this->assertEquals('Test Description', $lfgPost->description);
        $this->assertEquals('Gold', $lfgPost->min_rank);
        $this->assertEquals('Ace', $lfgPost->max_rank);
        $this->assertEquals('Squad TPP', $lfgPost->mode);
    }
}
