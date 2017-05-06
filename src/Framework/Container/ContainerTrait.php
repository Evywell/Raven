<?php


namespace Raven\Framework\Container;


trait ContainerTrait
{

    /**
     * @var Container
     */
    protected $container;

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

}