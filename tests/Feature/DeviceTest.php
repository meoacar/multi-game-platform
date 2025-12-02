<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Device;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function kullanici_cihaz_bilgisi_olusturabilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/me/device', [
                'device_name' => 'Poco X6 Pro',
                'graphics_settings' => 'HDR + Extreme',
                'fps_setting' => '90 FPS',
                'gyro_enabled' => true,
                'sensitivity_settings' => [
                    'general' => 80,
                    'ads' => 60,
                    'gyro' => 300,
                ],
                'notes' => 'Smooth gameplay',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'device_name' => 'Poco X6 Pro',
        ]);
    }

    /** @test */
    public function kullanici_kendi_cihaz_bilgisini_gorebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        Device::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me/device');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['device_name', 'graphics_settings', 'fps_setting']
            ]);
    }

    /** @test */
    public function kullanici_cihaz_bilgisini_guncelleyebilir()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        Device::factory()->create([
            'user_id' => $user->id,
            'device_name' => 'Old Device',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/v1/me/device', [
                'device_name' => 'New Device',
                'graphics_settings' => 'Ultra HD',
                'fps_setting' => '120 FPS',
                'gyro_enabled' => false,
                'sensitivity_settings' => ['general' => 100],
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'device_name' => 'New Device',
        ]);
    }

    /** @test */
    public function kullanici_tum_cihazlari_gorebilir()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Profile::factory()->create(['user_id' => $user1->id]);
        Profile::factory()->create(['user_id' => $user2->id]);
        
        Device::factory()->create(['user_id' => $user1->id, 'device_name' => 'iPhone 15']);
        Device::factory()->create(['user_id' => $user2->id, 'device_name' => 'Samsung S24']);

        $response = $this->getJson('/api/v1/devices');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function cihazlar_isme_gore_filtrelenebilir()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Profile::factory()->create(['user_id' => $user1->id]);
        Profile::factory()->create(['user_id' => $user2->id]);
        
        Device::factory()->create(['user_id' => $user1->id, 'device_name' => 'iPhone 15']);
        Device::factory()->create(['user_id' => $user2->id, 'device_name' => 'Samsung S24']);

        $response = $this->getJson('/api/v1/devices?device_name=iPhone');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
