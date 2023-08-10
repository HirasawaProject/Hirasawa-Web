<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BeatmapSet;
use App\Models\Score;
use App\Models\User;
use App\Services\ReplayService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ReplayServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReplayService $replayService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->replayService = $this->app->make('App\Services\ReplayService');
    }

    public function testGetReplayBasePath()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->create();

        $score = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
        ]);
        $this->assertEquals("replays/{$beatmap->osu_id}", $this->replayService->getReplayBasePath($score));
    }

    public function testGetReplayPath()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->create();

        $score = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
        ]);
        $this->assertEquals("replays/{$beatmap->osu_id}/{$score->id}.osr", $this->replayService->getReplayPath($score));
    }

    public function testDeleteReplay()
    {
        Storage::fake();

        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->create();

        $score = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
        ]);

        Storage::put($this->replayService->getReplayPath($score), 'test');
        $this->replayService->deleteReplay($score);
        Storage::assertMissing($this->replayService->getReplayPath($score));
    }

    public function testSaveReplay()
    {
        Storage::fake();

        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->create();

        $score = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
        ]);

        $file = UploadedFile::fake()->create('replay.osr', 100);

        $this->replayService->saveReplay($score, $file);
        Storage::assertExists($this->replayService->getReplayPath($score));
    }
}
