<?php


namespace Raven\Framework\Container;


interface ServiceInterface
{

    public function getService();
    public function getName();
    public function newInstance(...$args);
    public function isLocked();
    public function lock();

}