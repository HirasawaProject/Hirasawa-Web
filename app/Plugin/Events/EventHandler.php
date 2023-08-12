<?php

namespace App\Plugin\Events;

use Attribute;

#[Attribute]
class EventHandler
{
    public function __construct(
        public EventPriority $priority = EventPriority::NORMAL,
        public bool $bypassCancelled = false
    ) {}
}