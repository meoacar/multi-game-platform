<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function kullanici_kendi_profilini_gorebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'profile' => ['nickname', 'rank', 'city']
            ]);
    }

    /** @test */
    public function kullanici_profilini_guncelleyebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/me/profile', [
                'nickname' => 'ProGamer',
                'pubg_id' => '123456789',
                'rank' => 'Ace',
                'server_region' => 'EU',
                'city' => 'İstanbul',
                'age_range' => '18-24',
                'play_style' => 'try-hard',
                'bio' => 'Competitive player',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'nickname' => 'ProGamer',
            'rank' => 'Ace',
        ]);
    }

    /** @test */
    public function misafir_kullanici_profil_guncelleyemez()
    {
        $response = $this->putJson('/api/v1/me/profile', [
            'nickname' => 'ProGamer',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function profil_guncelleme_icin_nickname_gereklidir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/me/profile', [
                'nickname' => '',
                'rank' => 'Ace',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nickname']);
    }
}
