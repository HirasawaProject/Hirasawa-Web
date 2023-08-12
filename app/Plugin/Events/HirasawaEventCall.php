<?php

namespace App\Plugin\Events;

class HirasawaEventCall
{
    private HirasawaEvent $event;

    function __construct(HirasawaEvent $event)
    {
        $this->event = $event;
    }

    function isCancelled(): bool
    {
        if (method_exists($this->event, 'isCancelled')) {
            return $this->event->isCancelled();
        }
        return false;
    }
    
    function then(callable $callback): HirasawaEventCall
    {
        if (!$this->isCancelled()) {
            $callback($this->event);
        }

        return $this;
    }

    function cancelled(callable $callback): HirasawaEventCall
    {
        if ($this->isCancelled()) {
            $callback($this->event);
        }

        return $this;
    }
}