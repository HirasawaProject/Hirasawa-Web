<?php

namespace App\Services;

use App\Enums\Mode;
use App\Models\User;

class UserStatsService
{
    function updateUserStats(User $user, Mode $mode, array $scoreData)
    {
        $userStats = $user->getUserStats($mode);
        $userStats->play_count++;
        $userStats->total_score += $scoreData['score'];
        $userStats->max_combo = max($userStats->max_combo, $scoreData['max_combo']);
        $userStats->total_hits += match($mode) {
            Mode::OSU => $scoreData['count_300'] + $scoreData['count_100'] + $scoreData['count_50'],
            Mode::TAIKO => $scoreData['count_300'] + $scoreData['count_100'],
            Mode::CATCH_THE_BEAT => $scoreData['count_300'] + $scoreData['count_100'] + $scoreData['count_50'],
            Mode::MANIA => $scoreData['count_300'] + $scoreData['count_100'] + $scoreData['count_geki'] + $scoreData['count_katu'] + $scoreData['count_50'],
        };
        $userStats->save();
    }
}