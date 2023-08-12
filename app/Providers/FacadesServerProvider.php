<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class FacadesServerProvider extends ServiceProvider
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
        App::bind('pluginmanager', function () {
            return new \App\Plugin\PluginManager();
        });
        
        App::bind('eventmanager', function () {
            return new \App\Plugin\Events\EventManager();
        });
    }
}
