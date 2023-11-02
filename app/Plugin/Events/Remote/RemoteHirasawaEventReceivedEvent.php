<?php

namespace App\Plugin\Events\Remote;

use App\Plugin\Events\HirasawaEvent;

class RemoteHirasawaEventReceivedEvent extends HirasawaEvent
{
    public function __construct(public readonly string $package, public readonly string $class, public readonly array $payload) { }
}