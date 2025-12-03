<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    protected $model = Tournament::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true) . ' Tournament';
        
        return [
            'game_id' => Game::factory(),
            'organizer_id' => User::factory(),
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'rules' => fake()->paragraphs(3, true),
            'prize_pool' => fake()->numberBetween(1000, 50000),
            'max_teams' => fake()->numberBetween(8, 32),
            'team_size' => fake()->numberBetween(1, 4),
            'registration_starts_at' => fake()->dateTimeBetween('now', '+1 week'),
            'registration_ends_at' => fake()->dateTimeBetween('+1 week', '+2 weeks'),
            'tournament_starts_at' => fake()->dateTimeBetween('+2 weeks', '+3 weeks'),
            'tournament_ends_at' => fake()->dateTimeBetween('+3 weeks', '+1 month'),
            'status' => 'upcoming',
        ];
    }
}
