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

    public function getTop50(Mode $mode)
    {
        return $this->scores()->where('gamemode',  $mode->value)->orderBy('rank', 'asc')->limit(50)->get();
    }

    public function getRankCount(Mode $mode)
    {
        switch ($mode) {
            case Mode::OSU:
                return $this->osu_ranks;
            case Mode::TAIKO:
                return $this->taiko_ranks;
            case Mode::CATCH_THE_BEAT:
                return $this->ctb_ranks;
            case Mode::MANIA:
                return $this->mania_ranks;
        }
    }

    public function processLeaderboard()
    {
        foreach (Mode::cases() as $mode) {
            $this->processLeaderboardForMode($mode);
        }
    }

    public function processLeaderboardForMode(Mode $mode) {
        $scores = $this->scores()->where('gamemode', $mode->value)->orderBy('score', 'desc')->get();
        $rank = 1;
        foreach ($scores as $score) {
            $score->rank = $rank;
            $score->save();
            $rank++;
        }

        switch ($mode) {
            case Mode::OSU:
                $this->osu_ranks = $rank - 1;
                break;
            case Mode::TAIKO:
                $this->taiko_ranks = $rank - 1;
                break;
            case Mode::CATCH_THE_BEAT:
                $this->ctb_ranks = $rank - 1;
                break;
            case Mode::MANIA:
                $this->mania_ranks = $rank - 1;
                break;
        }

        $this->save();
    }

    public function getUserScore(User $user, Mode $mode)
    {
        return $this->scores()->where('gamemode', $mode->value)->where('user_id', $user->id)->first();
    }
}
