<?php

namespace App\Services;

use App\Models\Beatmap;
use App\Models\Score;
use App\Models\User;
use App\Models\UserStats;
use App\Enums\Mode;

class LeaderboardService
{
    function processGlobalLeaderboard(Mode $mode)
    {
        $index = 1;
        foreach (UserStats::where('mode', $mode->value)->orderBy('ranked_score')->get() as $userStat) {
            $userStat->rank = $index;
            $userStat->save();
            $index++;
        }
    }

    function processBeatmapLeaderboard(Beatmap $beatmap, Mode $mode)
    {
        $index = 1;
        foreach ($beatmap->getScoresForMode($mode) as $score) {
            $score->rank = $index;
            $score->save();
            $index++;
        }
    }

    function processUserLeaderboard(User $user, Mode $mode)
    {
        $totalAccuracy = 0;
        $totalScores = 0;
        $totalRankedScore = 0;
        foreach ($user->scores as $score) {
            $totalAccuracy += $score->accuracy;
            $totalRankedScore += $score->score;
            $totalScores++;
        }

        $stats = $user->getUserStats($mode);
        $stats->accuracy = $totalScores > 0 ? $totalAccuracy / $totalScores : 0;
        $stats->ranked_score = $totalRankedScore;
        $stats->save();
    }
}