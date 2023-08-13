<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BeatmapSet;
use App\Models\Score;
use App\Models\User;
use App\Services\ChartService;
use App\Enums\Mode;

class ChartServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChartService $chartService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chartService = $this->app->make('App\Services\ChartService');
    }

    public function testGetGenerateChartForNewScore()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->create();

        $userStatsBefore = $user->getUserStats(Mode::OSU);

        $score = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
        ]);

        $userStatsAfter = $user->getUserStats(Mode::OSU);
        
        $chart = $this->chartService->generateCharts($user, $beatmap, null, $score, $userStatsBefore, $userStatsAfter);

        $this->assertCount(2, $chart);

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

        $this->assertEquals([
            'chartId' => 'overall',
            'chartName' => 'Overall Ranking',
            'chartUrl' => config('osu_url') . "/u/{$user->id}",
            'rankedScoreBefore' => $userStatsBefore->ranked_score,
            'rankedScoreAfter' => $userStatsAfter->ranked_score,
            'totalScoreBefore' => $userStatsBefore->total_score,
            'totalScoreAfter' => $userStatsAfter->total_score,
            'accuracyBefore' => $userStatsBefore->accuracy,
            'accuracyAfter' => $userStatsAfter->accuracy,
            'ppBefore' => 0,
            'ppAfter' => 0,
            'rankBefore' => $userStatsBefore->rank,
            'rankAfter' => $userStatsAfter->rank,
            'onlineScoreId' => $score->id
        ], $chart[1]);
    }

    public function testGetGenerateChartForNewBetterScore()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->create();

        $userStatsBefore = $user->getUserStats(Mode::OSU);

        $scoreBefore = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
            'score' => 1000,
        ]);

        $scoreAfter = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
            'score' => 2000,
        ]);

        

        $userStatsAfter = $user->getUserStats(Mode::OSU);
        
        $chart = $this->chartService->generateCharts($user, $beatmap, $scoreBefore, $scoreAfter, $userStatsBefore, $userStatsAfter);

        $this->assertCount(2, $chart);

        $this->assertEquals([
            'chartId' => 'beatmap',
            'chartName' => 'Beatmap Ranking',
            'chartUrl' => config('osu_url') . "/b/{$beatmap->osu_id}",
            'rankedScoreBefore' => $scoreBefore->score,
            'rankedScoreAfter' => $scoreAfter->score,
            'maxComboBefore' => $scoreBefore->combo,
            'maxComboAfter' => $scoreAfter->combo,
            'accuracyBefore' => $scoreBefore->accuracy,
            'accuracyAfter' => $scoreAfter->accuracy,
            'ppBefore' => 0,
            'ppAfter' => 0,
            'rankBefore' => $scoreBefore->rank,
            'rankAfter' => $scoreAfter->rank,
            'onlineScoreId' => $scoreAfter->id
        ], $chart[0]);

        $this->assertEquals([
            'chartId' => 'overall',
            'chartName' => 'Overall Ranking',
            'chartUrl' => config('osu_url') . "/u/{$user->id}",
            'rankedScoreBefore' => $userStatsBefore->ranked_score,
            'rankedScoreAfter' => $userStatsAfter->ranked_score,
            'totalScoreBefore' => $userStatsBefore->total_score,
            'totalScoreAfter' => $userStatsAfter->total_score,
            'accuracyBefore' => $userStatsBefore->accuracy,
            'accuracyAfter' => $userStatsAfter->accuracy,
            'ppBefore' => 0,
            'ppAfter' => 0,
            'rankBefore' => $userStatsBefore->rank,
            'rankAfter' => $userStatsAfter->rank,
            'onlineScoreId' => $scoreAfter->id
        ], $chart[1]);
    }

    public function testGetGenerateChartForNewWorseScore()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->create();

        $userStatsBefore = $user->getUserStats(Mode::OSU);

        $scoreBefore = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
            'score' => 2000,
        ]);

        $scoreAfter = Score::factory()->create([
            'beatmap_id' => $beatmap->id,
            'user_id' => $user->id,
            'score' => 1000,
        ]);
        $scoreAfter->delete(); // simulating score was never created
    

        $userStatsAfter = $user->getUserStats(Mode::OSU);
        
        $chart = $this->chartService->generateCharts($user, $beatmap, $scoreBefore, $scoreAfter, $userStatsBefore, $userStatsAfter);

        $this->assertCount(2, $chart);

        $this->assertEquals([
            'chartId' => 'beatmap',
            'chartName' => 'Beatmap Ranking',
            'chartUrl' => config('osu_url') . "/b/{$beatmap->osu_id}",
            'rankedScoreBefore' => $scoreBefore->score,
            'maxComboBefore' => $scoreBefore->combo,
            'accuracyBefore' => $scoreBefore->accuracy,
            'ppBefore' => 0,
            'ppAfter' => 0,
            'rankBefore' => $scoreBefore->rank,
            'onlineScoreId' => $scoreBefore->id
        ], $chart[0]);

        $this->assertEquals([
            'chartId' => 'overall',
            'chartName' => 'Overall Ranking',
            'chartUrl' => config('osu_url') . "/u/{$user->id}",
            'rankedScoreBefore' => $userStatsBefore->ranked_score,
            'rankedScoreAfter' => $userStatsAfter->ranked_score,
            'totalScoreBefore' => $userStatsBefore->total_score,
            'totalScoreAfter' => $userStatsAfter->total_score,
            'accuracyBefore' => $userStatsBefore->accuracy,
            'accuracyAfter' => $userStatsAfter->accuracy,
            'ppBefore' => 0,
            'ppAfter' => 0,
            'rankBefore' => $userStatsBefore->rank,
            'rankAfter' => $userStatsAfter->rank,
            'onlineScoreId' => $scoreBefore->id
        ], $chart[1]);
    }
}
