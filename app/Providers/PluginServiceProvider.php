<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Facades\PluginManager;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        PluginManager::loadPluginsFromDirectory("plugins");
    }
}
