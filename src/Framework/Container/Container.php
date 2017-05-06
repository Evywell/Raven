<?php


namespace Raven\Framework\Container;


use Raven\Framework\Container\Exception\ContainerException;

class Container
{

    /**
     * @var ServiceInterface[]
     */
    private $services = [];
    private $globals = [];

    public function __construct(array $globals = [])
    {
        $this->globals = $globals;
    }

    public function get(string $key)
    {
        $key = strtolower($key);
        if(!$this->hasService($key)) {
            throw new \OutOfBoundsException(sprintf("%s does not exist", $key));
        }
        return $this->services[$key]->getService();
    }

    public function set(string $key, $value)
    {
        $key = strtolower($key);
        if($this->hasService($key) && $this->getManager($key)->isLocked()) {
            throw new ContainerException(sprintf("The service %s is locked, you cannot set a new service with this name", $key));
        }
        $service = $value;
        if(is_callable($value)) {
            $service = call_user_func_array($value, ['globals' => $this->globals, 'container' => $this]);
        }
        if(is_string($value) && class_exists($value)) {
            $service = new $value();
        }
        $manager = new Service($service, get_class($service));
        $this->services[$key] = $manager;
        return $manager;
    }

    public function resolve(string $class_name)
    {
        $instance = new \ReflectionClass($class_name);
        $resolvedParameters = [];
        $constructor = $instance->getConstructor();
        if($constructor) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                $parameter_class_name = $parameter->getClass()->name;
                foreach ($this->services as $service) {
                    if($service->getName() === $parameter_class_name) {
                        $resolvedParameters[] = $service->getService();
                        break;
                    }
                }
            }
        }

        return $instance->newInstanceArgs($resolvedParameters);
    }

    /**
     * @param $key
     * @return bool|ServiceInterface
     */
    public function getManager($key)
    {
        $key = strtolower($key);
        if($this->hasService($key)){
            return $this->services[$key];
        }
        return false;
    }

    public function hasService(string $key)
    {
        return array_key_exists(strtolower($key), $this->services);
    }

    public function getGlobal(string $key)
    {
        if(!array_key_exists($key, $this->globals)){
            throw new \OutOfBoundsException("The key %s does not exist in globals", $key);
        }
        return $this->globals[$key];
    }

    public function getGlobals()
    {
        return (array) $this->globals;
    }

}