<?php


namespace Raven\Framework\Container;


class Service implements ServiceInterface
{

    private $service;
    private $name;
    private $locked = false;

    public function __construct($service, string $name)
    {
        $this->service = $service;
        $this->name = $name;
    }

    public function newInstance(...$args)
    {
        $instance = new \ReflectionClass($this->service);
        return $instance->newInstanceArgs($args);
    }

    public function isLocked()
    {
        return $this->locked;
    }

    public function lock()
    {
        $this->locked = true;
    }

    public function getService()
    {
        return $this->service;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}