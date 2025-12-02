<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'device_name' => fake()->randomElement(['iPhone 15 Pro', 'Samsung S24', 'Poco X6 Pro', 'OnePlus 12', 'Xiaomi 14']),
            'graphics_settings' => fake()->randomElement(['Smooth + Extreme', 'HDR + Ultra', 'Ultra HD + High']),
            'fps_setting' => fake()->randomElement(['60 FPS', '90 FPS', '120 FPS']),
            'gyro_enabled' => fake()->boolean(),
            'sensitivity_settings' => [
                'general' => fake()->numberBetween(50, 100),
                'ads' => fake()->numberBetween(40, 80),
                'gyro' => fake()->numberBetween(200, 400),
            ],
            'notes' => fake()->sentence(),
        ];
    }
}
