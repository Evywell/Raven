<?php


namespace Raven\Framework\Container;


interface ContainerAwareInterface
{
    public function setContainer(Container $container = null);
}