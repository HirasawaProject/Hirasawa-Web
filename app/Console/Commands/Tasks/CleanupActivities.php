<?php

namespace App\Console\Commands\Tasks;

use Illuminate\Console\Command;
use App\Models\UserActivity;

class CleanupActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:activities:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove activities that are older than 90 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Removing activities older than 90 days...");

        $activities = UserActivity::where('created_at', '<', now()->subDays(90))->delete();

        $this->info("Done!");
    }
}
