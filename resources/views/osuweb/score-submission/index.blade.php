@include('osuweb.score-submission.header', ['beatmap' => $beatmap])

@foreach($charts as $chart)
    @keyvaluepipeimplode($chart)

@endforeach