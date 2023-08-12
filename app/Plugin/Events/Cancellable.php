<?php

namespace App\Plugin\Events;

trait Cancellable {
    private bool $cancelled = false;

    public function isCancelled(): bool {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled)
    {
        $this->cancelled = $cancelled;
    }
}