<?php

namespace App\Plugin\Internal\Listeners;

use App\Plugin\Events\EventListener;
use App\Plugin\Events\EventHandler;
use App\Plugin\Events\EventPriority;
use App\Plugin\Events\Remote\RemoteMessageReceivedEvent;
use App\Plugin\Events\Remote\RemoteHirasawaEventReceivedEvent;

class RemoteListener implements EventListener
{
    #[EventHandler(priority: EventPriority::LOWEST)]
    function onRemoteMessage(RemoteMessageReceivedEvent $event)
    {
        if ($event->namespace == 'event') {
            // Seperate class and package from message key
            $segments = explode('.', $event->key);
            $class = array_pop($segments);
            $package = implode('.', $segments);
            $hirasawaEvent = new RemoteHirasawaEventReceivedEvent($package, $class, json_decode($event->payload, true));
            $hirasawaEvent->call();
        }
    }
}