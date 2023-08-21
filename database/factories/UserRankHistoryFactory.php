<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserRankHistory>
 */
class UserRankHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'mode' => $this->faker->numberBetween(0, 3),
            'rank' => $this->faker->numberBetween(1, 1000000),
            'date' => now(),
        ];
    }
}
