<?php

namespace App\Plugin\Events;

use App\Facades\EventManager;

class HirasawaEvent
{
    public function call(): HirasawaEventCall
    {
        EventManager::callEvent($this);
        return new HirasawaEventCall($this);
    }
}