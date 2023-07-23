<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beatmap extends Model
{
    use HasFactory;

    public function beatmapSet()
    {
        return $this->belongsTo(BeatmapSet::class);
    }
}
