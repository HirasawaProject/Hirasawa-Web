<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\UserStatsService;
use App\Enums\Mode;
use App\Models\User;

class UserStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserStatsService $scoreService;

    public function setUp(): void
    {
        parent::setUp();
        $this->userStatsService = $this->app->make('App\Services\UserStatsService');
    }

    function testUpdateBlankUserStats()
    {
        $user = User::factory()->create();

        $score = [
            'score' => 1000000,
            'combo' => 100,
            'count_300' => 100,
            'count_100' => 100,
            'count_50' => 100,
            'count_miss' => 100,
        ];

        $this->userStatsService->updateUserStats($user, Mode::OSU, $score);

        $newUserStats = $user->getUserStats(Mode::OSU);
        $this->assertEquals(1, $newUserStats->play_count);
        $this->assertEquals(1000000, $newUserStats->total_score);
        $this->assertEquals(100, $newUserStats->max_combo);
        $this->assertEquals(300, $newUserStats->total_hits);
    }

    function testUpdateExistingUserStatsWithBetterMaxCombo()
    {
        $user = User::factory()->create();
        $userStats = $user->getUserStats(Mode::OSU);
        $userStats->play_count = 200;
        $userStats->total_score = 2000000;
        $userStats->max_combo = 500;
        $userStats->total_hits = 1000;
        $userStats->save();

        $score = [
            'score' => 1000000,
            'combo' => 100,
            'count_300' => 100,
            'count_100' => 100,
            'count_50' => 100,
            'count_miss' => 100,
        ];

        $this->userStatsService->updateUserStats($user, Mode::OSU, $score);

        $newUserStats = $user->getUserStats(Mode::OSU);
        $this->assertEquals(201, $newUserStats->play_count);
        $this->assertEquals(3000000, $newUserStats->total_score);
        $this->assertEquals(500, $newUserStats->max_combo);
        $this->assertEquals(1300, $newUserStats->total_hits);
    }

    function testUpdateExistingUserStatsWithWorseMaxCombo()
    {
        $user = User::factory()->create();
        $userStats = $user->getUserStats(Mode::OSU);
        $userStats->play_count = 200;
        $userStats->total_score = 2000000;
        $userStats->max_combo = 10;
        $userStats->total_hits = 1000;
        $userStats->save();

        $score = [
            'score' => 1000000,
            'combo' => 100,
            'count_300' => 100,
            'count_100' => 100,
            'count_50' => 100,
            'count_miss' => 100,
        ];

        $this->userStatsService->updateUserStats($user, Mode::OSU, $score);

        $newUserStats = $user->getUserStats(Mode::OSU);
        $this->assertEquals(201, $newUserStats->play_count);
        $this->assertEquals(3000000, $newUserStats->total_score);
        $this->assertEquals(100, $newUserStats->max_combo);
        $this->assertEquals(1300, $newUserStats->total_hits);
    }
}
