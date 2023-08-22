<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ActivityManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'activitymanager';
    }
}