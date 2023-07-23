<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    public function beatmap()
    {
        return $this->belongsTo(Beatmap::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
