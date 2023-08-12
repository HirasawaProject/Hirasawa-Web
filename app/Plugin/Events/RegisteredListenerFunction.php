<?php

namespace App\Plugin\Events;

use App\Plugin\HirasawaPlugin;
use App\Plugin\Events\EventListener;
use App\Plugin\Events\HirasawaEvent;
use ReflectionMethod;

class RegisteredListenerFunction
{
    
    function __construct(public EventListener $eventListener, public ReflectionMethod $method, public EventHandler $eventHandler, public HirasawaPlugin $plugin) { }

    public function call(HirasawaEvent $event)
    {
        $this->method->invoke($this->eventListener, $event);
    }
}