<?php


namespace Raven\Router;


use Traversable;

class RouterCollection implements \IteratorAggregate, \Countable
{

    /**
     * @var Route[]
     */
    private $routes;

    public function __construct(array $routes = [])
    {
        $this->routes = $routes;
    }

    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
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
        return count($this->routes);
    }
}