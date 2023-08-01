<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BeatmapSet;
use App\Models\Beatmap;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeatmapSet>
 */
class BeatmapSetFactory extends Factory
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
            'artist' => $this->faker->name,
            'title' => $this->faker->name,
            'status' => 1,
            'mapper_name' => $this->faker->name,
            'genre_id' => 1,
            'language_id' => 1,
            'rating' => $this->faker->randomFloat(2, 0, 10),
        ];
    }

    public function withBeatmaps($beatmapAttributes = [], $withLeaderboard = false): self
    {
        return $this->afterCreating(function (BeatmapSet $beatmapSet) use ($beatmapAttributes, $withLeaderboard) {
            $beatmapAttributes = array_merge($beatmapAttributes, [
                'beatmap_set_id' => $beatmapSet->id,
            ]);
            $beatmapFactory = Beatmap::factory();
            if ($withLeaderboard) {
                $beatmapFactory = $beatmapFactory->withLeaderboard(200);
            }
            $beatmap = $beatmapFactory->create($beatmapAttributes);
            $beatmapSet->beatmaps()->save($beatmap);
            $beatmap->processLeaderboard();
        });
    }
}
