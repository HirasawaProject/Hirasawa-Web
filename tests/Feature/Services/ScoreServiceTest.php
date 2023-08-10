<?php

namespace Tests\Feature\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ScoreService;
use App\Models\Score;
use App\Models\Beatmap;
use App\Models\BeatmapSet;
use App\Models\User;
use App\Models\UserStats;
use App\Enums\Mode;

class ScoreServiceTest extends TestCase
{
    private ScoreService $scoreService;

    public function setUp(): void
    {
        parent::setUp();
        $this->scoreService = $this->app->make('App\Services\ScoreService');
    }

    /**
     * This is a basic test of taking the decrypted score object from my own play and checking the values match
     * 
     * The "not_sure" variable is changed to "asdf" because we are not sure what it is and don't want to expose anything
     */
    public function testDecodingEncodedScore()
    {
        $encodedScore = "8d9756be1d932eb2f4760664a1497e48:Connor:f759254d81d851dbbf128624037d483f:111:2:0:42:2:3:407250:137:False:A:0:True:0:230808043154:20230727:asdf";
        $decodedScore = $this->scoreService->decodeSubmittedScore($encodedScore);
        $this->assertEquals($decodedScore['beatmap_hash'], "8d9756be1d932eb2f4760664a1497e48");
        $this->assertEquals($decodedScore['username'], "Connor");
        $this->assertEquals($decodedScore['verification_hash'], "f759254d81d851dbbf128624037d483f"); // We don't use this
        $this->assertEquals($decodedScore['count_300'], 111);
        $this->assertEquals($decodedScore['count_100'], 2);
        $this->assertEquals($decodedScore['count_50'], 0);
        $this->assertEquals($decodedScore['count_geki'], 42);
        $this->assertEquals($decodedScore['count_katu'], 2);
        $this->assertEquals($decodedScore['count_miss'], 3);
        $this->assertEquals($decodedScore['score'], 407250);
        $this->assertEquals($decodedScore['combo'], 137);
        $this->assertEquals($decodedScore['full_combo'], false);
        $this->assertEquals($decodedScore['rank'], "A");
        $this->assertEquals($decodedScore['mods'], 0);
        $this->assertEquals($decodedScore['pass'], true);
        $this->assertEquals($decodedScore['mode'], 0);
        $this->assertEquals($decodedScore['date'], 230808043154); // We don't use this
        $this->assertEquals($decodedScore['version'], 20230727);
        $this->assertEquals($decodedScore['not_sure'], "asdf");    
    }

    public function testCreateScoreWithNoUserScore()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->withStats()->create();

        $newScore = $this->scoreService->decodeSubmittedScore("{$beatmap->hash}:Connor:f759254d81d851dbbf128624037d483f:111:2:0:42:2:3:407250:137:False:A:0:True:0:230808043154:20230727:asdf");
        $score = $this->scoreService->createScore($user, $beatmap, Mode::OSU, $newScore);
        
        $this->assertTrue($score->exists);
    }

    public function testCreateScoreWithWorseUserScore()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->withStats()->create();

        Score::factory()->create([
            'user_id' => $user->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
            'score' => 100000
        ]);

        $newScore = $this->scoreService->decodeSubmittedScore("{$beatmap->hash}:Connor:f759254d81d851dbbf128624037d483f:111:2:0:42:2:3:407250:137:False:A:0:True:0:230808043154:20230727:asdf");
        $score = $this->scoreService->createScore($user, $beatmap, Mode::OSU, $newScore);
        
        $this->assertTrue($score->exists);
        $this->assertEquals($beatmap->getUserScore($user, Mode::OSU)->score, 407250);
    }

    public function testCreateScoreWithBetterUserScore()
    {
        $beatmapSet = BeatmapSet::factory()->withBeatmaps()->create();
        $beatmap = $beatmapSet->beatmaps->first();
        $user = User::factory()->withStats()->create();

        Score::factory()->create([
            'user_id' => $user->id,
            'beatmap_id' => $beatmap->id,
            'mode' => 0,
            'score' => 10000000
        ]);

        $newScore = $this->scoreService->decodeSubmittedScore("{$beatmap->hash}:Connor:f759254d81d851dbbf128624037d483f:111:2:0:42:2:3:407250:137:False:A:0:True:0:230808043154:20230727:asdf");
        $score = $this->scoreService->createScore($user, $beatmap, Mode::OSU, $newScore);
        
        $this->assertFalse($score->exists);
        $this->assertEquals($beatmap->getUserScore($user, Mode::OSU)->score, 10000000);
    }

    /**
     * We're gonna test the osu! accuracy formula using random public scores from the osu! Reddit
     */
    public function testOsuAccuracy(): void
    {
        // Cookiezi - Freedom Dive [FOUR DIMENSIONS] +HR 99.71%
        $score = new Score();
        $score->count_300 = 1965;
        $score->count_geki = 223;
        $score->count_100 = 7;
        $score->count_katu = 6;
        $score->count_50 = 0;
        $score->count_miss = 1;
        $score->mode = 0;

        $this->assertEquals(99.71, round($this->scoreService->calculateAccuracy($score), 2));

        // chocomint - HyuN - The Apocalypse [Revelation] +HD
        $score = new Score();
        $score->count_300 = 1011;
        $score->count_geki = 226;
        $score->count_100 = 24;
        $score->count_katu = 18;
        $score->count_50 = 0;
        $score->count_miss = 0;
        $score->mode = 0;

        $this->assertEquals(98.45, round($this->scoreService->calculateAccuracy($score), 2));

        // MINHOCA LOKA | Wire - Brazil [Kuki's Extra] +HDDTHR 97.42%
        $score = new Score();
        $score->count_300 = 187;
        $score->count_geki = 36;
        $score->count_100 = 6;
        $score->count_katu = 6;
        $score->count_50 = 0;
        $score->count_miss = 1;
        $score->mode = 0;

        $this->assertEquals(97.42, round($this->scoreService->calculateAccuracy($score), 2));
        
        // Maxim Bogdan | Linkin Park - Living Things [Marathon] +HDDTHR 90.64% 
        $score = new Score();
        $score->count_300 = 4823;
        $score->count_geki = 828;
        $score->count_100 = 680;
        $score->count_katu = 431;
        $score->count_50 = 15;
        $score->count_miss = 56;
        $score->mode = 0;

        $this->assertEquals(90.64, round($this->scoreService->calculateAccuracy($score), 2));
    }

    public function testTaikoAccuracy(): void
    {
        // Shinchikuhome | Broken By The Scream - Kagerou feat. Isam (from MAKE MY DAY) & Eyegargoyle (from Ailiph Doepa) [Fervour] +DT 99.75%
        $score = new Score();
        $score->count_300 = 2834;
        $score->count_geki = 9;
        $score->count_100 = 14;
        $score->count_katu = 0;
        $score->count_miss = 0;
        $score->mode = 1;

        $this->assertEquals(99.75, round($this->scoreService->calculateAccuracy($score), 2));

        // Red Riding Hood | Kobaryo - HUG AND KILL 98.51%
        $score = new Score();
        $score->count_300 = 1990;
        $score->count_geki = 7;
        $score->count_100 = 61;
        $score->count_katu = 1;
        $score->count_miss = 0;
        $score->mode = 1;

        $this->assertEquals(98.51, round($this->scoreService->calculateAccuracy($score), 2));

        // shinchikuhome | Ariabl'eyeS - Kegarenaki Bara Juuji [Hivie & KyeX's Rosenkreuz] +DT 98.32%
        $score = new Score();
        $score->count_300 = 2683;
        $score->count_geki = 8;
        $score->count_100 = 87;
        $score->count_katu = 8;
        $score->count_miss = 3;
        $score->mode = 1;

        $this->assertEquals(98.32, round($this->scoreService->calculateAccuracy($score), 2));

        // superSSS | Ghost Rule - DECO*27 [Mayday] +DT 94.68%
        $score = new Score();
        $score->count_300 = 1474;
        $score->count_geki = 0;
        $score->count_100 = 165;
        $score->count_katu = 0;
        $score->count_miss = 5;
        $score->mode = 1;

        $this->assertEquals(94.68, round($this->scoreService->calculateAccuracy($score), 2));
    }

    public function testCtbAccuracy(): void
    {
        // b-a-d-s123 | https://osu.ppy.sh/beatmapsets/1518662#fruits/3108717
        $score = new Score();

        $score->count_300 = 1077;
        $score->count_geki = 152;
        $score->count_100 = 134;
        $score->count_katu = 0;
        $score->count_50 = 148;
        $score->count_miss = 0;
        $score->mode = 2;

        $this->assertEquals(100, round($this->scoreService->calculateAccuracy($score), 2));

        // zxcmarkzxc | https://osu.ppy.sh/beatmapsets/777127#fruits/1634281
        $score = new Score();

        $score->count_300 = 285;
        $score->count_geki = 31;
        $score->count_100 = 51;
        $score->count_katu = 66;
        $score->count_50 = 450;
        $score->count_miss = 75;
        $score->mode = 2;

        $this->assertEquals(84.79, round($this->scoreService->calculateAccuracy($score), 2));

        // Natsuko | https://osu.ppy.sh/beatmapsets/1532642#fruits/3821321
        $score = new Score();

        $score->count_300 = 2094;
        $score->count_geki = 157;
        $score->count_100 = 0;
        $score->count_katu = 1;
        $score->count_50 = 25;
        $score->count_miss = 75;
        $score->mode = 2;

        $this->assertEquals(96.54, round($this->scoreService->calculateAccuracy($score), 2));

        // jongwon12 | https://osu.ppy.sh/beatmapsets/1972993#fruits/4094099
        $score = new Score();

        $score->count_300 = 1206;
        $score->count_geki = 325;
        $score->count_100 = 8;
        $score->count_katu = 0;
        $score->count_50 = 169;
        $score->count_miss = 4;
        $score->mode = 2;

        $this->assertEquals(99.71, round($this->scoreService->calculateAccuracy($score), 2));
    }

    public function testManiaAccuracy()
    {
        // 2ky | https://osu.ppy.sh/beatmapsets/2028339#mania/4226695
        $score = new Score();

        $score->count_300 = 0;
        $score->count_geki = 657;
        $score->count_100 = 0;
        $score->count_katu = 0;
        $score->count_50 = 0;
        $score->count_miss = 0;
        $score->mode = 3;

        $this->assertEquals(100, round($this->scoreService->calculateAccuracy($score), 2));

        // [ReyZ] | https://osu.ppy.sh/beatmapsets/1700009#mania/3473682
        $score = new Score();

        $score->count_300 = 726;
        $score->count_geki = 2187;
        $score->count_100 = 44;
        $score->count_katu = 119;
        $score->count_50 = 3;
        $score->count_miss = 88;
        $score->mode = 3;

        $this->assertEquals(94.96, round($this->scoreService->calculateAccuracy($score), 2));

        // Susu357 | https://osu.ppy.sh/beatmapsets/1992174#mania/4139450
        $score = new Score();

        $score->count_300 = 491;
        $score->count_geki = 2452;
        $score->count_100 = 3;
        $score->count_katu = 33;
        $score->count_50 = 0;
        $score->count_miss = 2;
        $score->mode = 3;

        $this->assertEquals(99.50, round($this->scoreService->calculateAccuracy($score), 2));

        // Araxcrow | https://osu.ppy.sh/beatmapsets/863309#mania/2973010
        $score = new Score();

        $score->count_300 = 520;
        $score->count_geki = 380;
        $score->count_100 = 205;
        $score->count_katu = 507;
        $score->count_50 = 33;
        $score->count_miss = 307;
        $score->mode = 3;

        $this->assertEquals(67.20, round($this->scoreService->calculateAccuracy($score), 2));
    }
}
