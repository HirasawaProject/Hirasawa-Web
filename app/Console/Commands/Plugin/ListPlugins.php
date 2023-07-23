<?php

namespace App\Console\Commands\Plugin;

use Illuminate\Console\Command;
use App\Facades\PluginManager;

class ListPlugins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugins:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all installed plugins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Listing all installed plugins:');
        $this->info('Name | Version | Author');
        foreach (PluginManager::getPlugins() as $plugin) {
            $this->info($plugin->getName().' | '.$plugin->getVersion().' | '.$plugin->getAuthor());
        }
    }
}
