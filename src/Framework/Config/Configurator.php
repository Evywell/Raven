<?php


namespace Raven\Framework\Config;


class Configurator
{

    private $globals = [];
    private $configuration = [];

    public function __construct(array $globals = [])
    {
        $this->globals = $globals;
    }

    public function get(string $key, $default = [])
    {
        return $this->has($key) ? $this->configuration[$key] : $default;
    }

    public function add(string $key, callable $value, array $extends = [])
    {
        $result = [];
        foreach ((array) $extends as $extend) {
            $result = array_merge($result, $this->get($extend));
        }
        $result = call_user_func_array($value, ['globals' => $this->globals]);
        if(!is_array($result)) {
            throw new ConfiguratorException(sprintf("The callback MUST return an array, %s given", gettype($value)));
        }

        $this->configuration[$key] = $result;
    }

    public function has(string $key)
    {
        return array_key_exists($key, $this->configuration);
    }

}