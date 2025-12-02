<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nickname' => fake()->userName(),
            'pubg_id' => fake()->numerify('##########'),
            'rank' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Crown', 'Ace', 'Conqueror']),
            'server_region' => fake()->randomElement(['EU', 'ASIA', 'NA', 'MENA']),
            'city' => fake()->randomElement(['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya']),
            'age_range' => fake()->randomElement(['18-24', '25-30', '31-35', '36+']),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'play_style' => fake()->randomElement(['try-hard', 'chill', 'fun-first']),
            'bio' => fake()->sentence(),
        ];
    }
}
