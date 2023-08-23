<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserActivity>
 */
class UserActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return match($this->faker->randomElement(['rank', 'medal'])) {
            'rank' => [
                'user_id' => User::inRandomOrder()->first()->id,
                'activity_key' => 'hirasawa.rank-achieved',
                'params' => [
                    'rank' => $this->faker->randomBetween(1, 100),
                    'beatmap_id' => Beatmap::inRandomOrder()->first()->id,
                ],
            ],
            'hirasawa.medal-unlocked' => [
                'user_id' => User::inRandomOrder()->first()->id,
                'activity_key' => 'hirasawa.medal-unlocked',
                'params' => [
                    'medalName' => $this->faker->word(),
                ]
            ]
        };
    }
}
