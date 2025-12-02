<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class LfgPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'game_id' => Game::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'min_rank' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond']),
            'max_rank' => fake()->randomElement(['Diamond', 'Crown', 'Ace', 'Conqueror']),
            'mode' => fake()->randomElement(['Squad TPP', 'Squad FPP', 'Duo TPP', 'Duo FPP']),
            'microphone_required' => fake()->boolean(),
            'min_age_range' => '18-24',
            'max_age_range' => '36+',
            'city' => fake()->randomElement(['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya']),
            'play_style_tag' => fake()->randomElement(['try-hard', 'chill', 'fun-first']),
            'status' => 'open',
        ];
    }
}
