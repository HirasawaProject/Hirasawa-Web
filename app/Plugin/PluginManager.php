<?php

namespace App\Plugin;

class PluginManager
{
    private array $loadedPlugins = [];

    function loadPlugin(HirasawaPlugin $plugin)
    {
        $this->loadedPlugins[$plugin->getName()] = $plugin;
        $plugin->onEnable();
    }

    function loadPluginsFromDirectory(string $directory)
    {
        $files = scandir($directory);
        foreach ($files as $file) {
            $pluginDirectory = $directory . '/' . $file;
            if (is_dir($pluginDirectory)) {
                if (file_exists($pluginDirectory . '/plugin.json')) {
                    $pluginDescriptor = PluginDescriptor::fromArray(json_decode(file_get_contents($pluginDirectory . '/plugin.json'), true));
                    require_once($pluginDirectory . '/' . $pluginDescriptor->getMain() . '.php');
                    $plugin = new ("plugins\\" . $file . '\\' . $pluginDescriptor->getMain())($pluginDescriptor);
                    $this->loadPlugin($plugin);
                }
            }
        }
    }

    function getPlugins(): array
    {
        return $this->loadedPlugins;
    }
}