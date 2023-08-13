<?php

namespace App\Observers;

use App\Models\User;
use App\Enums\Mode;

class UserObserver
{
    public function created(User $user)
    {
        foreach (Mode::cases() as $mode) {
            $user->stats()->create([
                'mode' => $mode->value,
            ]);
        }
    }
}
