<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Clan;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClanApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'clan_id' => Clan::factory(),
            'message' => fake()->paragraph(),
            'status' => 'pending',
        ];
    }
}
