@if ($beatmap == null)
    @include('osuweb.leaderboard.error-header')
@else
    @include('osuweb.leaderboard.header', ['hasOsz2' => false, 'beatmap' => $beatmap, 'mode' => $mode])
@endif
@if ($scores != null)
    @foreach ($scores as $score)
        @include('osuweb.leaderboard.score-info', ['score' => $score])

    @endforeach
@endif