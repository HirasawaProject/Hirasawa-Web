<?php

namespace App\Services;

use App\Models\Beatmap;
use App\Models\Score;
use App\Models\User;
use App\Models\UserStats;

class ChartService
{
    function generateCharts(User $user, Beatmap $beatmap, ?Score $oldScore, Score $newScore, UserStats $oldStats, UserStats $newStats): Array
    {
        // TODO: Allow plugins to register charts
        // For now we'll hardcode for our use

        if ($newScore->exists) {
            $onlineScoreId = $newScore->id;
        } else {
            $onlineScoreId = $oldScore->id;
        }

        return [
            [
                'chartId' => 'beatmap',
                'chartName' => 'Beatmap Ranking',
                'chartUrl' => config('osu_url') . "/b/{$beatmap->osu_id}",
                ...$this->generateBeforeAndAfter('rankedScore', $oldScore ? $oldScore->score : 0, $newScore->score, $newScore->exists),
                ...$this->generateBeforeAndAfter('maxCombo', $oldScore ? $oldScore->combo : 0, $newScore->combo, $newScore->exists),
                ...$this->generateBeforeAndAfter('accuracy', $oldScore ? $oldScore->accuracy : 0, $newScore->accuracy, $newScore->exists),
                'ppBefore' => 0,
                'ppAfter' => 0,
                ...$this->generateBeforeAndAfter('rank', $oldScore ? $oldScore->rank : 0, $newScore->rank, $newScore->exists),
                'onlineScoreId' => $onlineScoreId
            ],
            [
                'chartId' => 'overall',
                'chartName' => 'Overall Ranking',
                'chartUrl' => config('osu_url') . "/u/{$user->id}",
                'rankedScoreBefore' => $oldStats->ranked_score,
                'rankedScoreAfter' => $newStats->ranked_score,
                'totalScoreBefore' => $oldStats->total_score,
                'totalScoreAfter' => $newStats->total_score,
                'accuracyBefore' => $oldStats->accuracy,
                'accuracyAfter' => $newStats->accuracy,
                'ppBefore' => 0,
                'ppAfter' => 0,
                'rankBefore' => $oldStats->rank,
                'rankAfter' => $newStats->rank,
                'onlineScoreId' => $onlineScoreId
            ],

        ];
    }

    private function generateBeforeAndAfter(string $key, $before, $after, bool $doAfter): Array
    {
        $array =  [
            $key . 'Before' => $before,
        ];

        if ($doAfter) {
            $array[$key . 'After'] = $after;
        }
        return $array;
    }
}