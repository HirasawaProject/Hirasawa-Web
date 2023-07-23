<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeatmapSet extends Model
{
    use HasFactory;

    public function beatmaps()
    {
        return $this->hasMany(Beatmap::class);
    }
}
