<?php

namespace App\Http\Controllers\OsuWeb;

use App\Http\Controllers\Controller;
use App\Models\Beatmap;
use Illuminate\Http\Request;
use App\Enums\Mode;

class ScoresController extends Controller
{
    function getScores(Request $request)
    {
        $beatmap = Beatmap::where('hash', $request->input('c'))->first();
        $mode = Mode::from($request->input('m'));
        $scores = $beatmap ? $beatmap->getTop50($mode) : null;
        $userScore = $beatmap ? $beatmap->scores()->where('rank', '<', 50)->inRandomOrder()->first() : null;

        return view('osuweb.leaderboard.index', compact('beatmap', 'mode', 'scores', 'userScore'));
    }
}
