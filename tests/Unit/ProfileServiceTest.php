<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProfileService $profileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->profileService = new ProfileService();
    }

    /** @test */
    public function update_profile_updates_existing_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'OldNick',
        ]);

        $data = [
            'nickname' => 'NewNick',
            'rank' => 'Ace',
            'city' => 'İstanbul',
            'play_style' => 'try-hard',
        ];

        $result = $this->profileService->updateProfile($user, $data);

        $this->assertEquals('NewNick', $result->nickname);
        $this->assertEquals('Ace', $result->rank);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'nickname' => 'NewNick',
        ]);
    }

    /** @test */
    public function update_profile_creates_profile_if_not_exists()
    {
        $user = User::factory()->create();

        $data = [
            'nickname' => 'NewPlayer',
            'rank' => 'Gold',
            'city' => 'Ankara',
        ];

        $result = $this->profileService->updateProfile($user, $data);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertEquals('NewPlayer', $result->nickname);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'nickname' => 'NewPlayer',
        ]);
    }

    /** @test */
    public function get_profile_returns_user_profile()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $result = $this->profileService->getProfile($user);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertEquals($profile->id, $result->id);
    }
}
