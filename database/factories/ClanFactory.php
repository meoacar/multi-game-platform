<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClanFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();
        
        return [
            'user_id' => User::factory(),
            'game_id' => Game::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'logo_path' => null,
            'description' => fake()->paragraph(),
            'requirements' => fake()->sentence(),
            'min_rank' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond']),
            'max_rank' => fake()->randomElement(['Diamond', 'Crown', 'Ace', 'Conqueror']),
            'min_age_range' => '18-24',
            'max_age_range' => '36+',
            'city' => fake()->randomElement(['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya']),
        ];
    }
}
