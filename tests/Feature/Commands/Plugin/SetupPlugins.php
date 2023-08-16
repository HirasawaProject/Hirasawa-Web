<?php

namespace Tests\Feature\Commands\Plugin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Facades\PluginManager;
use App\Models\Plugin;
use App\Plugin\HirasawaPlugin;
use App\Plugin\PluginDescriptor;

class SetupPlugins extends TestCase
{
    use RefreshDatabase;
    
    function testRunningCommandWithNoPlugins()
    {
        PluginManager::shouldReceive('getPluginsFromDirectory')
            ->once()
            ->andReturn([]);

        $this->artisan('plugins:setup')
            ->expectsOutput('Setting up plugins...')
            ->expectsOutput('Done!')
            ->assertExitCode(0);
    }

    function testRunningCommandWithNewPlugin()
    {
        $testPlugin = new TestPlugin();
        PluginManager::shouldReceive('getPluginsFromDirectory')
            ->once()
            ->andReturn(['test:testPlugin' => $testPlugin]);

        $this->artisan('plugins:setup')
            ->expectsOutput('Setting up plugins...')
            ->expectsOutput('Installing TestPlugin')
            ->expectsOutput('Done!')
            ->assertExitCode(0);
        
        $this->assertDatabaseHas('plugins', [
            'name' => 'TestPlugin',
            'platform' => 'web',
            'version' => '0.0.1',
            'original_version' => '0.0.1',
            'author' => 'Hirasawa',
            'is_enabled' => true
        ]);
    }

    function testRunningCommandWithOlderPlugin()
    {
        $plugin = new Plugin();
        $plugin->name = 'TestPlugin';
        $plugin->platform = 'web';
        $plugin->version = '0.0.0';
        $plugin->original_version = '0.0.0';
        $plugin->author = 'Hirasawa';
        $plugin->is_enabled = true;
        $plugin->save();

        $testPlugin = new TestPlugin();
        PluginManager::shouldReceive('getPluginsFromDirectory')
            ->once()
            ->andReturn(['test:testPlugin' => $testPlugin]);

        $this->artisan('plugins:setup')
            ->expectsOutput('Setting up plugins...')
            ->expectsOutput('Upgrading TestPlugin')
            ->expectsOutput('Done!')
            ->assertExitCode(0);
        
        $this->assertDatabaseHas('plugins', [
            'name' => 'TestPlugin',
            'version' => '0.0.1',
            'original_version' => '0.0.0',
            'author' => 'Hirasawa',
            'is_enabled' => true
        ]);
    }
}

// Basic plugin
class TestPlugin extends HirasawaPlugin
{
    public array $calledFunctions = [];
    function __construct()
    {
        parent::__construct(new PluginDescriptor("TestPlugin", "0.0.1", "Hirasawa", ""));
    }
    public function onEnable()
    {
        $this->calledFunctions[] = 'onEnable';
    }
    public function onDisable()
    {
        $this->calledFunctions[] = 'onDisable';
    }
    public function onInstall()
    {
        $this->calledFunctions[] = 'onInstall';
    }
    public function onUninstall()
    {
        $this->calledFunctions[] = 'onUninstall';
    }
}

