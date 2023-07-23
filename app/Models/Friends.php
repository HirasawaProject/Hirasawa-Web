<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Friends extends Pivot
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function friend()
    {
        return $this->belongsTo(User::class);
    }
}
