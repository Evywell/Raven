<?php


namespace Raven\Framework\Session;


interface SessionInterface
{

    public function get($key);
    public function set($key, $value): SessionInterface;

}