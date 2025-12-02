<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Profile;
use App\Models\Device;
use App\Models\LfgPost;
use App\Models\Clan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_one_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Profile::class, $user->profile);
        $this->assertEquals($profile->id, $user->profile->id);
    }

    /** @test */
    public function user_has_one_device()
    {
        $user = User::factory()->create();
        $device = Device::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Device::class, $user->device);
        $this->assertEquals($device->id, $user->device->id);
    }

    /** @test */
    public function user_has_many_lfg_posts()
    {
        $user = User::factory()->create();
        $game = \App\Models\Game::factory()->create();
        
        LfgPost::factory()->count(3)->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $this->assertCount(3, $user->lfgPosts);
        $this->assertInstanceOf(LfgPost::class, $user->lfgPosts->first());
    }

    /** @test */
    public function user_has_many_clans_as_leader()
    {
        $user = User::factory()->create();
        $game = \App\Models\Game::factory()->create();
        
        Clan::factory()->count(2)->create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $this->assertCount(2, $user->clans);
        $this->assertInstanceOf(Clan::class, $user->clans->first());
    }

    /** @test */
    public function user_is_admin_default_false()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->is_admin);
    }

    /** @test */
    public function user_status_default_active()
    {
        $user = User::factory()->create();

        $this->assertEquals('active', $user->status);
    }

    /** @test */
    public function user_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $userId]);
    }
}
