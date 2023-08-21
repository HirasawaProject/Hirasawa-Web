<?php

namespace Tests\Feature\Commands\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Enums\Mode;
use App\Models\UserRankHistory;

class ProcessRankHistoryTest extends TestCase
{
    use RefreshDatabase;

    function testGenerateRankHistoryWithDefaultUserStats()
    {
        User::factory()->create();
        $this->artisan('tasks:rank-history:process')
            ->assertExitCode(0);
        $this->assertDatabaseCount('user_rank_histories', 0);
    }

    function testGenerateRankHistoryWithHighRankUserStats()
    {
        $user = User::factory()->create();
        foreach (Mode::cases() as $mode) {
            $user->getUserStats($mode)->increment('rank', 1);
        }
        $this->artisan('tasks:rank-history:process')
            ->assertExitCode(0);
        $this->assertDatabaseCount('user_rank_histories', 4);
    }

    function testRankHistoryCapsAt90()
    {
        $user = User::factory()->create();
        $user->getUserStats(Mode::OSU)->update(['rank' => 1]);
        for ($i = 1; $i < 100; $i++) {
            UserRankHistory::factory()->create([
                'user_id' => $user->id,
                'mode' => 0,
                'date' => now()->subDays($i)->toDateString(),
            ]);
        }
        $this->artisan('tasks:rank-history:process')
            ->assertExitCode(0);

        $this->assertDatabaseCount('user_rank_histories', 90);
    }

    
}
