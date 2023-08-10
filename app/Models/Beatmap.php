<?php

namespace App\Models;

use App\Enums\Mode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beatmap extends Model
{
    use HasFactory;

    public function beatmapSet()
    {
        return $this->belongsTo(BeatmapSet::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function beatmapStats()
    {
        return $this->hasMany(BeatmapStats::class);
    }

    public function getTop50(Mode $mode)
    {
        return $this->scores()->where('mode',  $mode->value)->orderBy('rank', 'asc')->limit(50)->get();
    }

    public function getScoresForMode(Mode $mode)
    {
        return $this->scores()->where('mode', $mode->value)->orderBy('score', 'desc')->get();
    }

    public function getBeatmapStats(Mode $mode)
    {
        return $this->beatmapStats()->where('mode', $mode->value)->first();
    }

    public function processLeaderboard()
    {
        foreach (Mode::cases() as $mode) {
            $this->processLeaderboardForMode($mode);
        }
    }

    public function processLeaderboardForMode(Mode $mode) {
        $scores = $this->scores()->where('mode', $mode->value)->orderBy('score', 'desc')->get();
        $rank = 1;
        foreach ($scores as $score) {
            $score->rank = $rank;
            $score->save();
            $rank++;
        }

        $stats = $this->getBeatmapStats($mode);
        $stats->ranks = $rank - 1;
        $stats->save();
    }

    public function getUserScore(User $user, Mode $mode)
    {
        return $this->scores()->where('mode', $mode->value)->where('user_id', $user->id)->first();
    }
}
