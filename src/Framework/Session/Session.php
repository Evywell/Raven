<?php


namespace Raven\Framework\Session;


use Raven\Framework\Session\Storage\NativeSessionStorage;
use Raven\Framework\Session\Storage\SessionStorageInterface;

class Session implements SessionInterface
{

    private $storage;

    public function __construct(SessionStorageInterface $storage = null)
    {
        $this->storage = $storage ?: new NativeSessionStorage();
        $this->initialize();
    }

    protected function initialize()
    {
        $this->storage->start();
    }

    public function get($key, $default = null)
    {
        if(!$this->storage->getBag()->has($key)) {
            return $default;
        }
        return $this->storage->getBag()->get($key);
    }

    public function set($key, $value): SessionInterface
    {
        $this->storage->getBag()->set($key, $value);
        return $this;
    }
}