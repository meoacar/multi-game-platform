<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_belongs_to_user()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $profile->user);
        $this->assertEquals($user->id, $profile->user->id);
    }

    /** @test */
    public function profile_has_required_fields()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'nickname' => 'TestPlayer',
            'rank' => 'Ace',
            'city' => 'İstanbul',
        ]);

        $this->assertEquals('TestPlayer', $profile->nickname);
        $this->assertEquals('Ace', $profile->rank);
        $this->assertEquals('İstanbul', $profile->city);
    }

    /** @test */
    public function profile_can_have_nullable_fields()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'bio' => null,
            'age_range' => null,
            'gender' => null,
        ]);

        $this->assertNull($profile->bio);
        $this->assertNull($profile->age_range);
        $this->assertNull($profile->gender);
    }
}
