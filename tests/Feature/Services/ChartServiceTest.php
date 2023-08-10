<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BeatmapSet;
use App\Models\Score;
use App\Models\User;
use App\Services\ChartService;

class ChartServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChartService $chartService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chartService = $this->app->make('App\Services\ChartService');
    }

    public function getGenerateChartForNewScore()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->create();

        $score = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
        ]);

        $chart = $this->chartService->generateCharts($score);
        $this->assertEquals([
            'chartId' => 'beatmap',
            'chartName' => 'Beatmap Ranking',
            'chartUrl' => config('osu_url') . "/b/{$beatmap->osu_id}",
            'rankedScoreBefore' => 0,
            'rankedScoreAfter' => $score->score,
            'maxComboBefore' => 0,
            'maxComboAfter' => $score->combo,
            'accuracyBefore' => 0,
            'accuracyAfter' => $score->accuracy,
            'ppBefore' => 0,
            'ppAfter' => 0,
            'rankBefore' => 0,
            'rankAfter' => $score->rank,
            'onlineScoreId' => $score->id
        ], $chart[0]);
    }
}
