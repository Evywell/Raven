<?php


namespace Raven\Framework\Middleware;


use Countable;
use Raven\Framework\Container\Container;

class MiddlewareQueue implements Countable
{

    private $middleware = [];
    private $resolved = [];
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    public function add(string $middleware): MiddlewareQueue
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function get(int $index)
    {
        if(!$this->has($index)) {
            return null;
        }
        if(isset($this->resolved[$index])) {
            return $this->resolved[$index];
        }

        return $this->resolve($index);
    }

    public function has(int $index)
    {
        return array_key_exists($index, $this->middleware);
    }

    private function resolve(int $index)
    {
        $class = $this->middleware[$index];
        $instance = $this->container->resolve($class);
        $this->resolved[$index] = $instance;
        return $this->resolved[$index];
    }

    public function unset(int $index)
    {
        if(!$this->has($index)) {
            if($this->count() > 0) {
                throw new \OutOfBoundsException(sprintf("%s is not in range (%d, %d)", $index, 0, $this->count() - 1));
            }
            throw new \OutOfBoundsException("The middleware queue is empty");
        }
        unset($this->middleware[$index]);
        unset($this->resolved[$index]);
        $this->middleware = array_values($this->middleware);
        $this->resolved = array_values($this->resolved);
        return $this;
    }

    public function setQueue(array $middleware)
    {
        $this->middleware = $middleware;
    }

    public function clear()
    {
        $this->middleware = [];
        return $this;
    }

    public function addFirst(string $middleware)
    {
        $this->middleware = array_merge([$middleware], $this->middleware);
        return $this;
    }

    public function addAt(int $index, string $middleware)
    {
        if($index < 0 || $index > $this->count()) {
            throw new \InvalidArgumentException(sprintf("%d is not valid", $index));
        }
        if($index === 0) {
            // Add at first position
            return $this->addFirst($middleware);
        } else if($index === $this->count() - 2) {
            // Add at the end
            return $this->add($middleware);
        }
        $slice = array_slice($this->middleware, 0, $index);
        $endSlice = array_slice($this->middleware, $index, $this->count() - 1);
        $this->middleware = array_merge($slice, [$middleware], $endSlice);
        return $this;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->middleware);
    }
}