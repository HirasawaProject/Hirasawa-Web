<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionGroupUser extends Pivot
{
    public function permissionGroup()
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
