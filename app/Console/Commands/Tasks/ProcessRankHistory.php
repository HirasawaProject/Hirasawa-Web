<?php

namespace App\Console\Commands\Tasks;

use Illuminate\Console\Command;
use App\Models\UserStats;
use App\Models\UserRankHistory;

class ProcessRankHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:rank-history:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process rank history for users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // just in case clear all UserRankHistory for today
        UserRankHistory::where('date', now()->toDateString())->delete();

        foreach (UserStats::all() as $userStats) {
            if ($userStats->rank === 0) {
                continue;
            }
            $rankHistory = new UserRankHistory();
            $rankHistory->user_id = $userStats->user_id;
            $rankHistory->mode = $userStats->mode;
            $rankHistory->rank = $userStats->rank;
            $rankHistory->save();
        }

        // delete all UserRankHistory where date is older than 90 days
        UserRankHistory::where('date', '<', now()->subDays(90)->toDateString())->delete();
    }
}
