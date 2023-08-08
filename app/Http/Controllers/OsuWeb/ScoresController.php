<?php

namespace App\Http\Controllers\OsuWeb;

use App\Http\Controllers\Controller;
use App\Models\Beatmap;
use Illuminate\Http\Request;
use App\Enums\Mode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\Rijndael;
use App\Models\Score;
use App\Services\ScoreService;
use App\Services\LeaderboardService;
use App\Services\ChartService;
use App\Services\ReplayService;


class ScoresController extends Controller
{
    private ScoreService $scoreService;
    private LeaderboardService $leaderboardService;
    private ChartService $chartService;
    private ReplayService $replayService;

    function __construct(ScoreService $scoreService, LeaderboardService $leaderboardService, ChartService $chartService, ReplayService $replayService)
    {
        $this->scoreService = new ScoreService();
        $this->leaderboardService = new LeaderboardService();
        $this->chartService = new ChartService();
        $this->replayService = new ReplayService();
    }

    function getScores(Request $request)
    {
        if (Auth::attempt([
            'username' => $request->input('us'),
            'password' => $request->input('ha')
        ])) {
            $beatmap = Beatmap::where('hash', $request->input('c'))->first();
            $mode = Mode::from($request->input('m'));
            $scores = $beatmap ? $beatmap->getTop50($mode) : null;
            $userScore = $beatmap ? $beatmap->getUserScore(Auth::user(), $mode) : null;

            return view('osuweb.leaderboard.index', compact('beatmap', 'mode', 'scores', 'userScore'));
        } else {
            return response('error:pass');
        }
    }

    function submitScore(Request $request)
    {
        if ($request->input('ft') > 0) {
            // This is a failed score, we don't care about it
            return response('');
        }
        // We are not passed the username so we need to grab it from the encrypted score object
        $iv = base64_decode($request->input('iv'));
        $key = "osu!-scoreburgr---------" . $request->input('osuver');
        $score = base64_decode($request->input('score'));

        $decrypted = $this->decrypt($key, $iv, $score);
        $scoreData = $this->scoreService->decodeSubmittedScore($decrypted);


        if (Auth::attempt([
            'username' => $scoreData['username'],
            'password' => $request->input('pass')
        ])) {
            $beatmap = Beatmap::where('hash', $scoreData['beatmap_hash'])->first();
            if (!$beatmap) {
                return response('error: beatmap not found');
            }

            $mode = Mode::from($scoreData['mode']);
            $oldScore = $beatmap->getUserScore(Auth::user(), $mode);
            $newScore = $this->scoreService->createScore(Auth::user(), $beatmap, $mode, $scoreData);
            $oldStats = Auth::user()->getUserStats($mode);

            if ($newScore->exists) {
                $this->leaderboardService->processBeatmapLeaderboard($beatmap, $mode);
                $this->leaderboardService->processGlobalLeaderboard($mode);
                $this->leaderboardService->processUserLeaderboard(Auth::user(), $mode);
                if ($oldScore) {
                    $this->replayService->deleteReplay($oldScore);
                }
                $this->replayService->saveReplay($newScore, $request->file('score'));
                $newScore->refresh();
            }

            $newStats = Auth::user()->getUserStats($mode);
            $newStats->total_score += $newScore->score;
            $newStats->save();


            $charts = $this->chartService->generateCharts(Auth::user(), $beatmap, $oldScore, $newScore, $oldStats, $newStats);
            return view('osuweb.score-submission.index', compact('beatmap', 'charts'));
        } else {
            return response('error: pass');
        }
    }

    function decrypt($key, $iv, $data) {
        $rijndael = new Rijndael('cbc');
        $rijndael->setKey($key);
        $rijndael->setKeyLength(256);
        $rijndael->disablePadding();
        $rijndael->setBlockLength(256);
        $rijndael->setIv($iv);
        
        return $rijndael->decrypt($data);
    }
}
