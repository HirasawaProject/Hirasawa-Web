{{ $beatmap->beatmapSet->status }}|false|{{ $beatmap->osu_id }}|{{ $beatmap->beatmapSet->osu_id }}|{{ $beatmap->getRankCount($mode) }}
0
[bold:0,size:20] {{ $beatmap->beatmapSet->artist }}|{{ $beatmap->beatmapSet->title }}
{{ $beatmap->offset }}
@if ($userScore != null)
    @include('osuweb.leaderboard.score-info', ['score' => $userScore])

@else

@endif