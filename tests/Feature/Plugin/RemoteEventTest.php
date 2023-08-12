<?php

namespace Tests\Feature\Plugin;

use Tests\TestCase;
use App\Plugin\Events\Remote\RemoteMessageReceivedEvent;
use Mockery;
use Illuminate\Support\Facades\Redis;
use App\Facades\EventManager;

class RemoteEventTest extends TestCase
{
    function testRemoteMessagesArePromotedToAHirasawaEvent()
    {
        Redis::shouldReceive('subscribe')
            ->once()
            ->with(['*'], \Mockery::type('callable'))
            ->andReturnUsing(function ($channels, $callback) {
                // Simulate a message received from Redis
                $callback('some random data', 'namespace:key');
            });
        
        EventManager::shouldReceive('callEvent')
            ->once()
            ->with(Mockery::type(RemoteMessageReceivedEvent::class))
            ->andReturnUsing(function ($event) {
                $this->assertEquals('namespace', $event->namespace);
                $this->assertEquals('key', $event->key);
                $this->assertEquals('some random data', $event->payload);
            });
        
        $this->artisan('cs:event:listen');
    }

    function testRemoteHirasawaEventsArePromotedToAHirasawaEvent()
    {
        Redis::shouldReceive('subscribe')
            ->once()
            ->with(['*'], \Mockery::type('callable'))
            ->andReturnUsing(function ($channels, $callback) {
                // Simulate a message received from Redis
                $callback(json_encode([
                    'some' => 'data',
                    'to' => 'test',
                ]), 'event:some.fully.qualified.package.with.Class');
            });

        EventManager::shouldReceive('callEvent')->once(); // RemoteMessageReceivedEvent
        
        EventManager::shouldReceive('callEvent') // RemoteHirasawaEventReceivedEvent - Generic
            ->once()
            ->with(Mockery::type(RemoteHirasawaEventReceivedEvent::class))
            ->andReturnUsing(function ($event) {
                $this->assertEquals('some.fully.qualified.package.with', $event->package);
                $this->assertEquals('Class', $event->class);
                $this->assertEquals([
                    'some' => 'data',
                    'to' => 'test'
                ], $event->payload);
            });
        
        $this->artisan('cs:event:listen');
    }
}
