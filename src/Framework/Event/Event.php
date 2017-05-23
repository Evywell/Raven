<?php


namespace Raven\Framework\Event;


class Event
{

    /**
     * @var bool
     */
    private $propagationStopped = false;

    /**
     * @return Listener
     */
    public function stopPropagation(): Listener
    {
        $this->propagationStopped = true;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

}