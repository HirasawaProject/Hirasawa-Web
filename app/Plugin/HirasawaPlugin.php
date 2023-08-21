<?php

namespace App\Plugin;

abstract class HirasawaPlugin implements IHirasawaPlugin
{
    private PluginDescriptor $pluginDescriptor;
    // TODO add support for registering events or commands

    public function __construct(PluginDescriptor $pluginDescriptor)
    {
        $this->pluginDescriptor = $pluginDescriptor;
    }

    public function getName(): string
    {
        return $this->pluginDescriptor->getName();
    }

    public function getVersion(): string
    {
        return $this->pluginDescriptor->getVersion();
    }

    public function getAuthor(): string
    {
        return $this->pluginDescriptor->getAuthor();
    }

    public function onInstall() {}
    public function onUpgrade(string $lastVersion) {}
    public function onUninstall() {}
}