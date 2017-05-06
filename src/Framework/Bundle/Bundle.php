<?php


namespace Raven\Framework\Bundle;


use Raven\Framework\Container\Container;

abstract class Bundle
{

    private $container;

    public function boot() {}

    public function setContainer(Container $container) {
        $this->container = $container;
    }

    public function getName()
    {
        $namespace = $this->getNamespace();
        $parts = explode('\\', $namespace);
        return end($parts);
    }

    public function getNamespace()
    {
        return (new \ReflectionClass($this))->getName();
    }

}