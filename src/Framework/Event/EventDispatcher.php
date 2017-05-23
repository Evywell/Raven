<?php


namespace Raven\Framework\Event;


class EventDispatcher
{

    private $listeners;
    const DEFAULT_PRIORITY = 10;

    public function __construct()
    {
        $this->listeners = [];
    }

    public function dispatch(string $event_name, Event $event = null)
    {
        if($event === null) {
            $event = new Event();
        }

        $listeners = $this->getListeners($event_name);
        $this->doDispatch($listeners, $event_name, $event);

        return $event;
    }

    private function doDispatch(array $listeners, string $event_name, Event $event)
    {
        foreach ($listeners as $listener) {
            if($event->isPropagationStopped()) {
                break;
            }

            call_user_func_array($listener['callable'], ['event' => $event, 'name' => $event_name]);
        }
    }

    private function getListeners(string $event_name)
    {
        return $this->hasListener($event_name) ? $this->listeners[$event_name] : [];
    }

    public function on(string $event_name, callable $callable, int $priority = 10): EventDispatcher
    {
        if(!$this->hasListener($event_name)) {
            $this->listeners[$event_name] = [];
        }

        $this->listeners[$event_name][] = ['callable' => $callable, 'priority' => $priority];
        $this->sortListenersByPriority($event_name);

        return $this;
    }

    public function addSubscriber(SubscriberInterface $subscriber): EventDispatcher
    {
        $events = $subscriber->implementedEvents();
        foreach ($events as $event => $listener) {
            if(!isset($listener['callable'])) {
                // TODO: Throw Exception
            }
            $priority = isset($listener['priority']) ? $listener['priority'] : EventDispatcher::DEFAULT_PRIORITY;
            $this->on($event, [$subscriber, $listener['callable']], $priority);
        }

        return $this;
    }

    public function hasListener(string $event_name): bool
    {
        return array_key_exists($event_name, $this->listeners);
    }

    private function sortListenersByPriority(string $event_name)
    {
        uasort($this->listeners[$event_name], function($a, $b) {
            if($a['priority'] === $b['priority']) {
                return 0;
            }
            return ($a['priority'] < $b['priority']) ? -1 : 1;
        });
    }

}