<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Score;
use App\Models\User;
use App\Models\Beatmap;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Score>
 */
class ScoreFactory extends Factory
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
            'score' => $this->faker->numberBetween(1, 10000000),
            'combo' => $this->faker->numberBetween(1, 1000),
            'count_50' => $this->faker->numberBetween(1, 1000),
            'count_100' => $this->faker->numberBetween(1, 1000),
            'count_300' => $this->faker->numberBetween(1, 1000),
            'count_miss' => $this->faker->numberBetween(1, 1000),
            'count_katu' => $this->faker->numberBetween(1, 1000),
            'count_geki' => $this->faker->numberBetween(1, 1000),
            'full_combo' => $this->faker->boolean,
            'mods' => 0,
            'timestamp' => $this->faker->numberBetween(1, 1000),
            'beatmap_id' => Beatmap::inRandomOrder()->first()->id,
            'gamemode' => $this->faker->numberBetween(0, 3),
            'rank' => $this->faker->numberBetween(0, 3),
            'accuracy' => $this->faker->randomFloat(2, 0, 10),
            'has_replay' => $this->faker->boolean,
        ];
    }


}
