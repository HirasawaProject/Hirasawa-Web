<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Facades\PluginManager;
use App\Plugin\Internal\InternalPlugin;
use App\Plugin\PluginDescriptor;

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
        PluginManager::loadPlugin(new InternalPlugin(new PluginDescriptor("Internal Web", "0.0.1", "Hirasawa Contributors", ""))); // Todo pull version from project
    }
}
