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
        $baseDirectory = base_path() . '/' . $directory;
        if (!is_dir($baseDirectory)) {
            mkdir($baseDirectory);
        }
        $files = scandir($baseDirectory);
        foreach ($files as $file) {
            $pluginDirectory = $directory . '/' . $file;
            if (is_dir($pluginDirectory)) {
                if (file_exists($pluginDirectory . '/plugin.json')) {
                    // Require all files from the plugin
                    $this->requireAllFilesRecursively($pluginDirectory);

                    $pluginDescriptor = PluginDescriptor::fromArray(json_decode(file_get_contents($pluginDirectory . '/plugin.json'), true));
                    $plugin = new ("plugins\\" . $file . '\\' . $pluginDescriptor->getMain())($pluginDescriptor);
                    $this->loadPlugin($plugin);
                }
            }
        }
    }

    private function requireAllFilesRecursively(string $baseDirectory)
    {
        $files = array_diff(scandir($baseDirectory), [], ['.', '..']);

        foreach ($files as $file) {
            $pluginDirectory = $baseDirectory . '/' . $file;
            if (is_dir($pluginDirectory)) {
                $this->requireAllFilesRecursively($pluginDirectory);
            } else {
                if (substr($pluginDirectory, -4) == '.php') {
                    require_once($pluginDirectory);
                }
            }
        }
    }

    function getPlugins(): array
    {
        return $this->loadedPlugins;
    }
}