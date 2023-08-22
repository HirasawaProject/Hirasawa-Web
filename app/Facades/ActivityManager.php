<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PluginManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'activitymanager';
    }
}