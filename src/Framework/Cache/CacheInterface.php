<?php


namespace Raven\Framework\Cache;


interface CacheInterface
{

    public function set(string $key, $value, $expiration = 3600);
    public function get(string $key);
    public function contains(string $key);

}