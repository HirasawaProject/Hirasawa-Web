<?php

namespace App\Console\Commands\Plugin;

use Illuminate\Console\Command;

use App\Models\Plugin;
use App\Facades\PluginManager;

class SetupPlugins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plugins:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and upgrade plugin data';

    public function __construct(private PluginManager $pluginManager)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $knownPlugins = Plugin::where('platform', 'web')->get();
        $installedPlugins = PluginManager::getPluginsFromDirectory('plugins');

        $this->info('Setting up plugins...');
        foreach ($installedPlugins as $installedPlugin) {
            $knownPlugin = $knownPlugins->where('name', $installedPlugin->getName())->where('author', $installedPlugin->getAuthor())->first();
            if ($knownPlugin->is_disabled) {
                continue;
            }
            if ($knownPlugin) {
                if ($knownPlugin->version === $installedPlugin->getVersion()) {
                    continue;
                }
                $this->info('Upgrading ' . $installedPlugin->getName());
                $installedPlugin->onUpgrade($knownPlugin->version);
                $knownPlugin->version = $installedPlugin->getVersion();
                $knownPlugin->save();
            } else {
                $this->info('Installing ' . $installedPlugin->getName());
                $installedPlugin->onInstall();
                $plugin = new Plugin();

                $plugin->platform = 'web';
                $plugin->name = $installedPlugin->getName();
                $plugin->author = $installedPlugin->getAuthor();
                $plugin->version = $installedPlugin->getVersion();
                $plugin->original_version = $installedPlugin->getVersion();
                $plugin->save();
            }
        }

        $this->info('Done!');
    }
}
