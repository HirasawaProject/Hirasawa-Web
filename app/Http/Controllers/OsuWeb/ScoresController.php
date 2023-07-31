<?php

namespace App\Http\Controllers\OsuWeb;

use App\Http\Controllers\Controller;
use App\Models\Beatmap;
use Illuminate\Http\Request;
use App\Enums\Mode;
use Illuminate\Support\Facades\Auth;

class ScoresController extends Controller
{
    function getScores(Request $request)
    {
        if (Auth::attempt([
            'username' => $request->input('us'),
            'password' => $request->input('ha')
        ])) {
            $beatmap = Beatmap::where('hash', $request->input('c'))->first();
            $mode = Mode::from($request->input('m'));
            $scores = $beatmap ? $beatmap->getTop50($mode) : null;
            $userScore = $beatmap ? $beatmap->scores()->where('rank', '<', 50)->inRandomOrder()->first() : null;

            return view('osuweb.leaderboard.index', compact('beatmap', 'mode', 'scores', 'userScore'));
        } else {
            return response('error:pass');
        }
    }
}
