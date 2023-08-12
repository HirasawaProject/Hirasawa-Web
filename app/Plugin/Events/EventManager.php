<?php

namespace App\Plugin\Events;

use App\Plugin\HirasawaPlugin;

class EventManager
{
    private $registeredEvents = [];

    function callEvent(HirasawaEvent $hirasawaEvent)
    {
        if (isset($this->registeredEvents[get_class($hirasawaEvent)])) {
            foreach (EventPriority::cases() as $priority)
            {
                if (isset($this->registeredEvents[get_class($hirasawaEvent)][$priority->value])) {
                    foreach ($this->registeredEvents[get_class($hirasawaEvent)][$priority->value] as $registeredFunction) {
                        if (method_exists($hirasawaEvent, 'isCancelled')) {
                            if ($hirasawaEvent->isCancelled() && !$registeredFunction->eventHandler->bypassCancelled) {
                                continue;
                            }
                        }
                        $registeredFunction->call($hirasawaEvent);
                    }
                }
            }
        }
    }

    function registerEvents(EventListener $eventListener, HirasawaPlugin $plugin)
    {
        $reflection = new \ReflectionObject($eventListener);
        $methods = $reflection->getMethods();
        foreach ($methods as $method) {
            $parameters = $method->getParameters();
            if (count($parameters) == 0) {
                continue;
            }
            $parameter = $parameters[0];
            $parameterClassName = $parameter->getType()->getName();
            $attributes = $method->getAttributes(EventHandler::class);

            if (count($attributes) == 0) {
                continue;
            }

            $eventHandler = $attributes[0]->newInstance();

            if (!isset($this->registeredEvents[$parameterClassName])) {
                $this->registeredEvents[$parameterClassName] = [];
            }
            if (!isset($this->registeredEvents[$parameterClassName][$eventHandler->priority->value])) {
                $this->registeredEvents[$parameterClassName][$eventHandler->priority->value] = [];
            }
            $this->registeredEvents[$parameterClassName][$eventHandler->priority->value][] = new RegisteredListenerFunction($eventListener, $method, $eventHandler, $plugin);
        }
    }

    public function removeEvents(HirasawaPlugin $plugin)
    {
        foreach ($this->registeredEvents as $event => $eventPriority) {
            foreach ($eventPriority as $priority => $registeredFunctions) {
                foreach ($registeredFunctions as $key => $registeredFunction) {
                    if ($registeredFunction->plugin == $plugin) {
                        unset($this->registeredEvents[$event][$key]);
                    }
                }
            }
        }
    }
}