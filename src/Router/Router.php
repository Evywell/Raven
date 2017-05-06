<?php


namespace Raven\Router;

class Router
{

    /**
     * @var string
     */
    private $url;
    /**
     * @var RouterCollection
     */
    private $routes;

    /**
     * Initialize the Router
     */
    public function initialize()
    {
        $this->routes = new RouterCollection();
    }

    /**
     * @return \Raven\Router\Route|null
     */
    public function run(): ?Route
    {
        foreach ($this->routes as $route) {
            if($route->match($this->url)) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Plug the url match to the $url parameter
     * @param string $url
     */
    public function listen(string $url)
    {
        $this->url = $url;
    }

    /**
     * Set Routes
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes->setRoutes($routes);
    }

    /**
     * Add a route
     * @param Route $route
     */
    public function addRoute(Route $route)
    {
        $this->routes->addRoute($route);
    }

    /**
     * @return RouterCollection
     */
    public function getRoutes(): RouterCollection
    {
        return $this->routes;
    }

    /**
     * @param RouterCollection $collection
     */
    public function setCollectionRoutes(RouterCollection $collection)
    {
        $this->routes = $collection;
    }

}