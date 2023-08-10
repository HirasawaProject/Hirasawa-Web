<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Enums\Mode;
use App\Models\UserStats;
use App\Services\LeaderboardService;

class LeaderboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private LeaderboardService $leaderboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leaderboardService = $this->app->make('App\Services\LeaderboardService');
    }

    public function testProcessGlobalLeaderboard()
    {
        $user1 = User::factory()->withStats()->create();
        $user1Stats = $user1->getUserStats(Mode::OSU);
        $user2 = User::factory()->withStats()->create();
        $user2Stats = $user2->getUserStats(Mode::OSU);
        $user3 = User::factory()->withStats()->create();
        $user3Stats = $user3->getUserStats(Mode::OSU);
        $user4 = User::factory()->withStats()->create();
        $user4Stats = $user4->getUserStats(Mode::OSU);

        $user1Stats->ranked_score = 2_000_000;
        $user1Stats->save();

        $user2Stats->ranked_score = 5_000_000;
        $user2Stats->save();

        $user3Stats->ranked_score = 3_000_000;
        $user3Stats->save();

        $user4Stats->ranked_score = 10_000_000;
        $user4Stats->save();

        $this->leaderboardService->processGlobalLeaderboard(Mode::OSU);
        $userStats = UserStats::orderBy('rank', 'asc')->get();

        $this->assertEquals($user4->id, $userStats[0]->user_id);
        $this->assertEquals(1, $userStats[0]->rank);

        $this->assertEquals($user2->id, $userStats[1]->user_id);
        $this->assertEquals(2, $userStats[1]->rank);

        $this->assertEquals($user3->id, $userStats[2]->user_id);
        $this->assertEquals(3, $userStats[2]->rank);

        $this->assertEquals($user1->id, $userStats[3]->user_id);
        $this->assertEquals(4, $userStats[3]->rank);
    }
}
