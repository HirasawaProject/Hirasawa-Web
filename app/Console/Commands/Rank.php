<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Rank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beatmap:rank {mapsetId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $endpoint = 'https://osu.ppy.sh/api/get_beatmaps';
        $mapsetId = $this->argument('mapsetId');

        $params = [
            'k' => 'e9d82625d325db57dbb30810fa86bf09e6cd8a45',
            's' => $mapsetId,
        ];

        $url = $endpoint . '?' . http_build_query($params);
        // Connect to the URL using Laravel's HTTP Client
        $response = \Illuminate\Support\Facades\Http::get($url)->json();
        $beatmapSet = new \App\Models\BeatmapSet();
        $beatmapSet->osu_id = $mapsetId;
        $beatmapSet->artist = $response[0]['artist'];
        $beatmapSet->title = $response[0]['title'];
        $beatmapSet->status = 1;
        $beatmapSet->mapper_name = $response[0]['creator'];
        $beatmapSet->genre_id = $response[0]['genre_id'];
        $beatmapSet->language_id = $response[0]['language_id'];
        $beatmapSet->rating = 0;
        $beatmapSet->save();
        foreach ($response as $apiBeatmap) {
            $beatmap = new \App\Models\Beatmap();
            $beatmap->osu_id = $apiBeatmap['beatmap_id'];
            $beatmap->beatmap_set_id = $beatmapSet->id;
            $beatmap->difficulty_name = $apiBeatmap['version'];
            $beatmap->hash = $apiBeatmap['file_md5'];
            $beatmap->offset = 0;
            $beatmap->total_length = $apiBeatmap['total_length'];
            $beatmap->hit_length = $apiBeatmap['hit_length'];
            $beatmap->circle_size = $apiBeatmap['diff_size'];
            $beatmap->overall_difficulty = $apiBeatmap['diff_overall'];
            $beatmap->approach_rate = $apiBeatmap['diff_approach'];
            $beatmap->health_drain = $apiBeatmap['diff_drain'];
            $beatmap->mode = $apiBeatmap['mode'];
            $beatmap->count_normal = $apiBeatmap['count_normal'];
            $beatmap->count_slider = $apiBeatmap['count_slider'];
            $beatmap->count_spinner = $apiBeatmap['count_spinner'];
            $beatmap->bpm = $apiBeatmap['bpm'];
            $beatmap->has_storyboard = $apiBeatmap['storyboard'] == 1;
            $beatmap->max_combo = $apiBeatmap['max_combo'];
            $beatmap->save();
        }
    }
}
