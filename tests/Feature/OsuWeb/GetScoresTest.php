<?php

namespace Tests\Feature\OsuWeb;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\BeatmapSet;
use App\Models\Score;
use App\Enums\Mode;
use App\Services\LeaderboardService;

class GetScoresTest extends TestCase
{
    use RefreshDatabase;

    private LeaderboardService $leaderboardService;

    public function setUp(): void
    {
        parent::setUp();

        $this->leaderboardService = $this->app->make('App\Services\LeaderboardService');
    }

    private function constructUrl($params): string
    {
        return '/web/osu-osz2-getscores.php?' . http_build_query($params);
    }

    /**
     * A basic feature test example.
     */
    public function test_see_leaderboard_on_invalid_hash(): void
    {
        $user = User::factory()->create();

        $this->withHeaders([
            'User-Agent' => 'osu!'
        ])->get($this->constructUrl([
            'us' => $user->username,
            'ha' => md5('password'),
            'm' => '0',
            'c' => 'invalid-hash',
        ]))->assertStatus(200)->assertSee('-1|false');
    }

    public function test_see_leaderboard_on_map_without_scores(): void
    {
        $user = User::factory()->create();
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();

        $response = $this->withHeaders([
            'User-Agent' => 'osu!'
        ])->get($this->constructUrl([
            'us' => $user->username,
            'ha' => md5('password'),
            'm' => '0',
            'c' => $beatmap->hash,
        ]));

        $contentLines = explode("\n", $response->getContent());
        $this->assertEquals(6, count($contentLines));
        $this->assertEquals("1|false|{$beatmap->osu_id}|{$beatmapSet->osu_id}|0", $contentLines[0]);
    }

    public function test_see_leaderboard_on_map_with_scores(): void
    {
        $user = User::factory()->create();
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();

        $user2 = User::factory()->create();
        $score = Score::factory()->create([
            'user_id' => $user2->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
        ]);

        $response = $this->withHeaders([
            'User-Agent' => 'osu!'
        ])->get($this->constructUrl([
            'us' => $user->username,
            'ha' => md5('password'),
            'm' => '0',
            'c' => $beatmap->hash,
        ]));

        $contentLines = explode("\n", $response->getContent());
        $this->assertEquals(7, count($contentLines));
        $this->assertEquals("1|false|{$beatmap->osu_id}|{$beatmapSet->osu_id}|0", $contentLines[0]);
        $this->assertEquals("", $contentLines[4]);
        $this->assertStringContainsString($user2->username, $contentLines[5]);
    }

    public function test_see_leaderboard_on_map_with_scores_and_user_score(): void
    {
        $user = User::factory()->create();
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();

        $score = Score::factory()->create([
            'user_id' => $user->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
            'score' => 1000000,
        ]);

        $user2 = User::factory()->create();
        $score2 = Score::factory()->create([
            'user_id' => $user2->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
            'score' => 2000000,
        ]);

        $this->leaderboardService->processBeatmapLeaderboard($beatmap, Mode::OSU);

        $response = $this->withHeaders([
            'User-Agent' => 'osu!'
        ])->get($this->constructUrl([
            'us' => $user->username,
            'ha' => md5('password'),
            'm' => '0',
            'c' => $beatmap->hash,
        ]));

        $contentLines = explode("\n", $response->getContent());
        $this->assertEquals(8, count($contentLines));
        $this->assertEquals("1|false|{$beatmap->osu_id}|{$beatmapSet->osu_id}|0", $contentLines[0]);
        $this->assertStringContainsString($user->username, $contentLines[4]);
        $this->assertStringContainsString($user2->username, $contentLines[5]);
        $this->assertStringContainsString($user->username, $contentLines[6]);
    }

    public function test_see_leaderboard_on_map_with_scores_and_user_score_but_on_different_mode(): void
    {
        $user = User::factory()->create();
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();

        $score = Score::factory()->create([
            'user_id' => $user->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
            'score' => 1000000,
        ]);

        $user2 = User::factory()->create();
        $score2 = Score::factory()->create([
            'user_id' => $user2->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
            'score' => 2000000,
        ]);

        $response = $this->withHeaders([
            'User-Agent' => 'osu!'
        ])->get($this->constructUrl([
            'us' => $user->username,
            'ha' => md5('password'),
            'm' => '1',
            'c' => $beatmap->hash,
        ]));

        $contentLines = explode("\n", $response->getContent());
        $this->assertEquals(6, count($contentLines));
        $this->assertEquals("1|false|{$beatmap->osu_id}|{$beatmapSet->osu_id}|0", $contentLines[0]);
    }

    public function test_see_leaderboard_on_map_with_scores_and_user_score_but_on_two_different_modes(): void
    {
        $user = User::factory()->create();
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();

        $score = Score::factory()->create([
            'user_id' => $user->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
            'score' => 1000000,
        ]);

        $score = Score::factory()->create([
            'user_id' => $user->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 1,
            'score' => 1000000,
        ]);

        $user2 = User::factory()->create();
        $score2 = Score::factory()->create([
            'user_id' => $user2->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
            'score' => 2000000,
        ]);

        $user3 = User::factory()->create();
        $score3 = Score::factory()->create([
            'user_id' => $user3->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 1,
            'score' => 2000000,
        ]);

        $this->leaderboardService->processBeatmapLeaderboard($beatmap, Mode::OSU);
        $this->leaderboardService->processBeatmapLeaderboard($beatmap, Mode::TAIKO);

        $response = $this->withHeaders([
            'User-Agent' => 'osu!'
        ])->get($this->constructUrl([
            'us' => $user->username,
            'ha' => md5('password'),
            'm' => '0',
            'c' => $beatmap->hash,
        ]));

        $contentLines = explode("\n", $response->getContent());
        $this->assertEquals(8, count($contentLines));
        $this->assertEquals("1|false|{$beatmap->osu_id}|{$beatmapSet->osu_id}|0", $contentLines[0]);
        $this->assertStringContainsString($user->username, $contentLines[4]);
        $this->assertStringContainsString($user2->username, $contentLines[5]);
        $this->assertStringContainsString($user->username, $contentLines[6]);

        $response = $this->withHeaders([
            'User-Agent' => 'osu!'
        ])->get($this->constructUrl([
            'us' => $user->username,
            'ha' => md5('password'),
            'm' => '1',
            'c' => $beatmap->hash,
        ]));

        $contentLines = explode("\n", $response->getContent());
        $this->assertEquals(8, count($contentLines));
        $this->assertEquals("1|false|{$beatmap->osu_id}|{$beatmapSet->osu_id}|0", $contentLines[0]);
        $this->assertStringContainsString($user->username, $contentLines[4]);
        $this->assertStringContainsString($user3->username, $contentLines[5]);
        $this->assertStringContainsString($user->username, $contentLines[6]);
    }

}
