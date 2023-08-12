<?php

namespace App\Plugin\Internal;

use App\Plugin\HirasawaPlugin;
use App\Facades\EventManager;
use App\Plugin\Internal\Listeners\RemoteListener;

class InternalPlugin extends HirasawaPlugin
{
    public function onEnable(): void
    {
        EventManager::registerEvents(new RemoteListener(), $this);
    }

    public function onDisable(): void
    {
        
    }
}