<?php

namespace App\Services;

use App\Models\Score;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ReplayService
{
    public function getReplayBasePath(Score $score): string
    {
        return "replays/{$score->beatmap->osu_id}";
    }
    public function getReplayPath(Score $score): string
    {
        return $this->getReplayBasePath($score) . "/{$score->id}.osr";
    }

    public function getReplayUrl(Score $score): string
    {
        return Storage::url($this->getReplayPath($score));
    }

    public function deleteReplay(Score $score)
    {
        Storage::delete($this->getReplayPath($score));
    }

    public function saveReplay(Score $score, UploadedFile $file): bool
    {
        return Storage::putFileAs($this->getReplayBasePath($score), $file, "{$score->id}.osr");
    }
}