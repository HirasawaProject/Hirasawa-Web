<?php

namespace App\Plugin\Activities;

use App\Models\UserActivity;

class RankAchievedActivity implements ActivityBuilder
{
    function build(UserActivity $activity): String
    {
        $username = $activity->user->username;
        $rank = $activity->params->rank;
        $beatmap = Beatmap::find($activity->params->beatmap_id);
        $beatmapTitle = $beatmap->beatmapset->title;
        $beatmapArtist = $beatmap->beatmapset->artist;
        $difficulty = $beatmap->name;

        $mode = match($activity->params->mode) {
            0 => 'osu!',
            1 => 'Taiko',
            2 => 'Catch the Beat',
            3 => 'osu!mania',
        };

        return "$username has reached rank #$rank on $beatmapArtist - $beatmapTitle [$difficulty] ($mode)";
    }

    function getRequiredParams(): Array
    {
        return [
            "rank",
            "beatmap_id",
            "mode"
        ];
    }
}