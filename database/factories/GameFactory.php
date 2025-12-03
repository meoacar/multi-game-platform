<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Game Factory
 * 
 * Multi-game platform için oyun test verisi üretir
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company() . ' ' . fake()->randomElement(['Game', 'Mobile', 'Arena', 'Battle']);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'logo' => fake()->imageUrl(200, 200, 'games', true),
            'icon' => null, // Backward compatibility
            'description' => fake()->paragraph(),
            'status' => 'active',
            'is_active' => true, // Backward compatibility
            'settings' => [
                'theme_color' => fake()->hexColor(),
                'secondary_color' => fake()->hexColor(),
                'max_team_size' => fake()->numberBetween(2, 10),
                'platforms' => fake()->randomElements(
                    ['Android', 'iOS', 'PC', 'Console'],
                    fake()->numberBetween(1, 4)
                ),
                'features' => [
                    'tournaments' => fake()->boolean(80),
                    'clans' => fake()->boolean(80),
                    'lfg' => fake()->boolean(80),
                    'matchmaking' => fake()->boolean(60),
                ],
            ],
            'order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * State: İnaktif oyun
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
            'is_active' => false,
        ]);
    }

    /**
     * State: PUBG Mobile oyunu
     */
    public function pubg(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'PUBG Mobile',
            'slug' => 'pubg',
            'description' => 'PlayerUnknown\'s Battlegrounds Mobile - Battle Royale oyunu',
            'settings' => [
                'theme_color' => '#FF6B00',
                'secondary_color' => '#FFB800',
                'max_team_size' => 4,
                'platforms' => ['Android', 'iOS'],
                'features' => [
                    'tournaments' => true,
                    'clans' => true,
                    'lfg' => true,
                    'matchmaking' => true,
                ],
            ],
        ]);
    }

    /**
     * State: Call of Duty Mobile oyunu
     */
    public function codm(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Call of Duty Mobile',
            'slug' => 'codm',
            'description' => 'Call of Duty Mobile - FPS oyunu',
            'settings' => [
                'theme_color' => '#00A651',
                'secondary_color' => '#FFD700',
                'max_team_size' => 5,
                'platforms' => ['Android', 'iOS'],
                'features' => [
                    'tournaments' => true,
                    'clans' => true,
                    'lfg' => true,
                    'matchmaking' => true,
                ],
            ],
        ]);
    }
}
