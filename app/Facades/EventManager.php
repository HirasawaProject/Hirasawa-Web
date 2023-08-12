<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class EventManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'eventmanager';
    }
}