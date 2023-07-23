<?php

namespace App\Plugin;

class PluginDescriptor
{
    private string $name;
    private string $version;
    private string $author;
    private string $main;

    public function __construct(string $name, string $version, string $author, string $main)
    {
        $this->name = $name;
        $this->version = $version;
        $this->author = $author;
        $this->main = $main;
    }

    public static function fromArray(array $pluginDescriptorArray) {
        return new PluginDescriptor($pluginDescriptorArray['name'], $pluginDescriptorArray['version'], $pluginDescriptorArray['author'], $pluginDescriptorArray['main']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
    
    public function getAuthor(): string
    {
        return $this->author;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function getMain(): string
    {
        return $this->main;
    }
}