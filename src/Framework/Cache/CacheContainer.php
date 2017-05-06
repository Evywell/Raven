<?php


namespace Raven\Framework\Cache;


use Raven\Framework\Container\Container;

class CacheContainer extends Container
{

    private $cache;

    public function __construct(array $globals)
    {
        $this->cache = new FileSystemCache($globals['cache_dir'], 'container');
        parent::__construct($globals);
    }

    public function get(string $key)
    {
        $value = parent::get($key);
        return $this->cache->get($value);
    }

    public function set(string $key, $value)
    {
        $manager = parent::set($key, $value);
        $this->cache->set($key, $manager);
        return $manager;
    }


}