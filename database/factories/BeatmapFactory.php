<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Beatmap;
use App\Models\Score;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Beatmap>
 */
class BeatmapFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'osu_id' => $this->faker->unique()->numberBetween(1, 1000),
            'difficulty_name' => $this->faker->name,
            'hash' => md5($this->faker->name),
            'offset' => $this->faker->randomFloat(2, 0, 10),
            'total_length' => $this->faker->numberBetween(1, 1000),
            'hit_length' => $this->faker->numberBetween(1, 1000),
            'circle_size' => $this->faker->randomFloat(2, 0, 10),
            'overall_difficulty' => $this->faker->randomFloat(2, 0, 10),
            'approach_rate' => $this->faker->randomFloat(2, 0, 10),
            'health_drain' => $this->faker->randomFloat(2, 0, 10),
            'mode' => $this->faker->numberBetween(0, 3),
            'count_normal' => $this->faker->numberBetween(1, 1000),
            'count_slider' => $this->faker->numberBetween(1, 1000),
            'count_spinner' => $this->faker->numberBetween(1, 1000),
            'bpm' => $this->faker->randomFloat(2, 0, 10),
            'has_storyboard' => $this->faker->boolean,
            'max_combo' => $this->faker->numberBetween(1, 1000),
            'play_count' => $this->faker->numberBetween(1, 1000),
            'pass_count' => $this->faker->numberBetween(1, 1000)
        ];
    }

    public function withLeaderboard($count): self
    {
        return $this->afterCreating(function (Beatmap $beatmap) use ($count) {
            $leaderboard = Score::factory()->count($count)->create([
                'beatmap_id' => $beatmap->id,
            ]);
            foreach ($leaderboard as $score) {
                $beatmap->scores()->save($score);
            }
        });
    }

}
