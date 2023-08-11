<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserStats>
 */
class UserStatsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ranked_score' => $this->faker->numberBetween(0, 1000000),
            'accuracy' => $this->faker->randomFloat(2, 0, 100),
            'play_count' => $this->faker->numberBetween(0, 1000000),
            'total_score' => $this->faker->numberBetween(0, 1000000),
            'rank' => $this->faker->numberBetween(0, 1000000),
            'pp' => $this->faker->numberBetween(0, 2500),
            'mode' => $this->faker->numberBetween(0, 3),
        ];
    }
}
