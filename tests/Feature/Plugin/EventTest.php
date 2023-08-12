<?php

namespace Tests\Feature\Plugin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Plugin\Events\HirasawaEvent;
use App\Plugin\HirasawaPlugin;
use App\Facades\EventManager;
use App\Plugin\Events\EventListener;
use App\Plugin\PluginDescriptor;
use App\Plugin\Events\Cancellable;
use App\Plugin\Events\EventHandler;
use App\Plugin\Events\EventPriority;

class EventTest extends TestCase
{
    function testCanRegisterAndCallEvent()
    {
        $plugin = new TestPlugin();
        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler()]
            public function onTestEvent (TestEvent $event)
            {
                $event->called = true;
            }
        }, $plugin);

        $event = new TestEvent();
        $event->call();
        $this->assertTrue($event->called);

        EventManager::removeEvents($plugin);
    }

    function testCanRegisterCallAndCancelEvent()
    {
        $plugin = new TestPlugin();
        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler()]
            public function onTestEvent (CancellableTestEvent $event)
            {
                $event->setCancelled(true);
            }
        }, $plugin);

        $event = new CancellableTestEvent();
        $event->call();
        $this->assertTrue($event->isCancelled());

        EventManager::removeEvents($plugin);
    }

    function testCanRegisterAndCallEventWithThen()
    {
        $plugin = new TestPlugin();
        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler()]
            public function onTestEvent(CancellableTestEvent $event) {}
        }, $plugin);

        $event = new CancellableTestEvent();
        $event->call()->then(function (CancellableTestEvent $event) {
            $this->assertTrue(true);
        })->cancelled(function() {
            $this->assertTrue(false);
        });

        EventManager::removeEvents($plugin);
    }

    function testCanRegisterAndCallEventWithCancelled()
    {
        $plugin = new TestPlugin();
        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler()]
            public function onTestEvent(CancellableTestEvent $event) {
                $event->setCancelled(true);
            }
        }, $plugin);

        $event = new CancellableTestEvent();
        $event->call()->then(function (CancellableTestEvent $event) {
            $this->assertTrue(false);
        })->cancelled(function (CancellableTestEvent $event) {
            $this->assertTrue(true);
        });

        EventManager::removeEvents($plugin);
    }

    function testTwoEventListenersWillExectuteInOrder()
    {
        $plugin = new TestPlugin();
        $plugin2 = new TestPlugin();
        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler(EventPriority::HIGHEST)]
            public function onTestEvent(IndexedCancellableTestEvent $event) {
                $event->index = 1;
            }
        }, $plugin);

        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler(EventPriority::NORMAL)]
            public function onTestEvent(IndexedCancellableTestEvent $event) {
                if ($event->index == 1) {
                    $event->index = 2;
                }
            }
        }, $plugin2);

        $event = new IndexedCancellableTestEvent();
        $event->call()->then(function (IndexedCancellableTestEvent $event) {
            $this->assertTrue(true);
            $this->assertEquals(2, $event->index);
        })->cancelled(function (IndexedCancellableTestEvent $event) {
            $this->assertTrue(false);
        });

        EventManager::removeEvents($plugin);
    }

    function testTwoEventListenersWillStopPropagatingAfterCancellation()
    {
        $plugin = new TestPlugin();
        $plugin2 = new TestPlugin();
        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler()]
            public function onTestEvent(IndexedCancellableTestEvent $event) {
                $event->index = 1;
                $event->setCancelled(true);
            }
        }, $plugin);

        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler(priority: EventPriority::LOW)]
            public function onTestEvent(IndexedCancellableTestEvent $event) {
                if ($event->index == 1) {
                    $event->index = 2;
                }
            }
        }, $plugin2);

        $event = new IndexedCancellableTestEvent();
        $event->call()->then(function (IndexedCancellableTestEvent $event) {
            $this->assertTrue(false);
        })->cancelled(function (IndexedCancellableTestEvent $event) {
            $this->assertTrue(true);
            $this->assertEquals(1, $event->index);
        });

        EventManager::removeEvents($plugin);
    }

    function testTwoEventListenersWillStillPropagateAfterCancellationIfRequested()
    {
        $plugin = new TestPlugin();
        $plugin2 = new TestPlugin();
        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler()]
            public function onTestEvent(IndexedCancellableTestEvent $event) {
                $event->index = 1;
                $event->setCancelled(true);
            }
        }, $plugin);

        EventManager::registerEvents(new class() implements EventListener
        {
            #[EventHandler(priority: EventPriority::LOW, bypassCancelled: true)]
            public function onTestEvent(IndexedCancellableTestEvent $event) {
                if ($event->index == 1) {
                    $event->index = 2;
                }
            }
        }, $plugin2);

        $event = new IndexedCancellableTestEvent();
        $event->call()->then(function (IndexedCancellableTestEvent $event) {
            $this->assertTrue(false);
        })->cancelled(function (IndexedCancellableTestEvent $event) {
            $this->assertTrue(true);
            $this->assertEquals(2, $event->index);
        });

        EventManager::removeEvents($plugin);
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

// Test events
class TestEvent extends HirasawaEvent
{
    public $called = false;
}

class CancellableTestEvent extends HirasawaEvent
{
    use Cancellable;
}

class IndexedCancellableTestEvent extends HirasawaEvent
{
    use Cancellable;

    public $index = 0;
}
