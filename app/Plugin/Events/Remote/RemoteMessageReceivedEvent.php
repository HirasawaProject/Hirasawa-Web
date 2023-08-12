<?php

namespace App\Plugin\Events\Remote;

use App\Plugin\Events\HirasawaEvent;

class RemoteMessageReceivedEvent extends HirasawaEvent
{
    public function __construct(public readonly string $namespace, public readonly string $key, public readonly string $payload) { }
}