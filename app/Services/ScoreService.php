<?php

namespace App\Services;

use App\Models\Beatmap;
use App\Models\Score;
use App\Models\User;
use App\Enums\Mode;

class ScoreService
{
    function decodeSubmittedScore(string $score)
    {
        $scoreArray = explode(':', $score);
        return [
            'beatmap_hash' => $scoreArray[0],
            'username' => $scoreArray[1],
            'verification_hash' => $scoreArray[2],
            'count_300' => $scoreArray[3],
            'count_100' => $scoreArray[4],
            'count_50' => $scoreArray[5],   
            'count_geki' => $scoreArray[6],
            'count_katu' => $scoreArray[7],
            'count_miss' => $scoreArray[8],
            'score' => $scoreArray[9],
            'combo' => $scoreArray[10],
            'full_combo' => $scoreArray[11] == 'True',
            'rank' => $scoreArray[12],
            'mods' => $scoreArray[13],
            'pass' => $scoreArray[14] == 'True',
            'mode' => $scoreArray[15],
            'date' => $scoreArray[16],
            'version' => $scoreArray[17],
            'not_sure' => $scoreArray[18],
        ];
    }

    function createScore(User $user, Beatmap $beatmap, Mode $mode, array $newScore): ?Score
    {
        $userScore = $beatmap->getUserScore($user, $mode);

        $score = new Score();
        $score->beatmap_id = $beatmap->id;
        $score->user_id = $user->id;
        $score->score = $newScore['score'];
        $score->combo = $newScore['combo'];
        $score->full_combo = $newScore['full_combo'];
        $score->count_300 = $newScore['count_300'];
        $score->count_100 = $newScore['count_100'];
        $score->count_50 = $newScore['count_50'];
        $score->count_geki = $newScore['count_geki'];
        $score->count_katu = $newScore['count_katu'];
        $score->count_miss = $newScore['count_miss'];
        $score->mode = $mode->value;
        $score->rank = 0;
        $score->mods = $newScore['mods'];
        $score->accuracy = $this->calculateAccuracy($score);

        if ($userScore && $userScore->score > $newScore['score']) {
            return $score;
        }
        if ($userScore) {
            $userScore->delete();
        }
        $score->save();

        return $score;
    }

    /**
     * https://osu.ppy.sh/wiki/en/Gameplay/Accuracy
     */
    function calculateAccuracy(Score $score)
    {
        $accuracy = 0;
        switch ($score->mode) {
            case 0:
                // osu!
                $accuracy = ($score->count_300 * 300 + $score->count_100 * 100 + $score->count_50 * 50) / (($score->count_300 + $score->count_100 + $score->count_50 + $score->count_miss) * 300);
                break;
            case 1:
                // taiko
                $accuracy = ($score->count_300 + ($score->count_100 / 5)) / ($score->count_300 + $score->count_100 + $score->count_miss);
                break;
            case 2:
                // ctb
                $accuracy = ($score->count_300 + $score->count_100 + $score->count_50) / ($score->count_300 + $score->count_100 + $score->count_50 + $score->count_miss);
                break;
            case 3:
                // mania
                $accuracy = (300 * ($score->count_300 + $score->count_geki)) + (200 * $score->count_katu) + (100 * ($score->count_100 + $score->count_50)) / (300 * ($score->count_300 + $score->count_geki + $score->count_katu + $score->count_100 + $score->count_50 + $score->count_miss));
                break;                
        }
        
        return $accuracy * 100;
    }
}