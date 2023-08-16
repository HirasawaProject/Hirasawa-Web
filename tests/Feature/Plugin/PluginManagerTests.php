<?php

namespace Tests\Feature\Plugin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Plugin\HirasawaPlugin;
use App\Plugin\PluginDescriptor;
use App\Models\Plugin;
use App\Facades\PluginManager;

class PluginManagerTests extends TestCase
{
    use RefreshDatabase;

    function testPluginWontBeLoadedIfDoesntExistInDatabase()
    {
        PluginManager::loadPlugin(new TestPlugin());

        $this->assertArrayNotHasKey('TestPlugin', PluginManager::getPlugins());
    }

    function testPluginWontBeLoadedIfDisabled()
    {
        $plugin = new Plugin();
        $plugin->name = 'TestPlugin';
        $plugin->platform = 'web';
        $plugin->author = 'Hirasawa';
        $plugin->version = '0.0.1';
        $plugin->original_version = '0.0.1';
        $plugin->is_enabled = false;

        $plugin->save();

        PluginManager::loadPlugin(new TestPlugin());

        $this->assertArrayNotHasKey('TestPlugin', PluginManager::getPlugins());
    }

    function testPluginWontBeLoadedIfVersionMismatch()
    {
        $plugin = new Plugin();
        $plugin->name = 'TestPlugin';
        $plugin->platform = 'web';
        $plugin->author = 'Hirasawa';
        $plugin->version = '0.0.2';
        $plugin->original_version = '0.0.1';
        $plugin->is_enabled = true;

        $plugin->save();

        PluginManager::loadPlugin(new TestPlugin());

        $this->assertArrayNotHasKey('TestPlugin', PluginManager::getPlugins());
    }

    function testPluginWillBeLoadedIfInformationMatchesDatabase()
    {
        $plugin = new Plugin();
        $plugin->name = 'TestPlugin';
        $plugin->platform = 'web';
        $plugin->author = 'Hirasawa';
        $plugin->version = '0.0.1';
        $plugin->original_version = '0.0.1';
        $plugin->is_enabled = true;

        $plugin->save();

        PluginManager::loadPlugin(new TestPlugin());

        $this->assertArrayHasKey('TestPlugin', PluginManager::getPlugins());
    }
}

// Basic plugin
class TestPlugin extends HirasawaPlugin
{
    function __construct()
    {
        parent::__construct(new PluginDescriptor("TestPlugin", "0.0.1", "Hirasawa", ""));
    }
    public function onEnable() {}
    public function onDisable() {}
}
