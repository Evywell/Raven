<?php


namespace Raven\Framework\Router;


use Raven\Framework\Cache\CacheInterface;
use Raven\Framework\Container\Container;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;
use Raven\Router\Exception\RouterException;
use Raven\Router\Route;
use Raven\Router\Router;
use Raven\Router\RouterCollection;

class RavenRouter extends Router
{

    /**
     * @var Container
     */
    private $container;
    /**
     * @var string
     */
    private $cache_dir;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var bool
     */
    private $active_cache = false;
    /**
     * @var bool
     */
    private $initialized = false;
    /**
     * @var string
     */
    private $routes_resources;
    /**
     * @var string
     */
    private $resource_dir;
    /**
     * @var array
     */
    private $methods = ['ANY', 'GET', 'POST', 'PUT', 'DELETE'];

    public function __construct(Container $container, string $routes_resources)
    {
        $this->container = $container;
        // Cache
        $this->cache_dir = $this->container->getGlobal('cache_dir');
        $this->cache = $this->container->getManager('cache')->newInstance($this->cache_dir, 'routes.cache');

        // Routes directory
        $this->routes_resources = $routes_resources;
        $this->resource_dir = dirname(dirname($this->routes_resources));
    }

    /**
     * Initialize the router
     */
    public function initialize()
    {
        if($this->initialized) {
            return;
        }

        if($this->isActiveCache() && $this->cache->contains('routes')){
            $this->setCollectionRoutes($this->cache->get('routes'));
            return;
        }

        parent::initialize();
        $this->registerRoutes();

        if($this->isActiveCache()){
            $this->cache->set('routes', $this->getRoutes(), 3600);
        }
        $this->initialized = true;
    }

    /**
     * Register routes
     * @throws RouterException
     */
    private function registerRoutes()
    {
        if(!file_exists($this->routes_resources)) {
            throw new RouterException(sprintf("Unable to load the routing file, %s does not exist", $this->routes_resources));
        }
        $resources = require_once $this->routes_resources;
        if(!is_array($resources)) {
            throw new RouterException(sprintf("A resource routing file MUST return an array. %s given in %s", gettype($resources), $this->routes_resources));
        }

        foreach($resources as $resource) {
            // Load the resource
            $this->loadResource($resource);
        }
    }

    /**
     * Load resources
     * @param array $resource
     * @throws RouterException
     */
    private function loadResource(array $resource)
    {
        $resource_name = array_key_exists('resource', $resource) ? $resource['resource'] : null;
        if(strpos($resource_name, '@') === 0) {
            $resource_name = str_replace('@', rtrim($this->resource_dir, '/'). '/', $resource_name);
        }
        if(!file_exists($resource_name)) {
            throw new RouterException(sprintf("Unable to load the resource, %s does not exist", $resource_name));
        }
        $prefix = array_key_exists('prefix', $resource) ? $resource['prefix'] : '/';
        $middleware = array_key_exists('middleware', $resource) ? $resource['middleware'] : [];
        $this->importRoutes($resource_name, $prefix, $middleware);
    }

    /**
     * Import routes
     * @param string $resource_name
     * @param string $prefix
     * @param array $middleware
     * @throws RouterException
     */
    private function importRoutes(string $resource_name, string $prefix, array $middleware = [])
    {
        $routes = require_once $resource_name;
        if(!is_array($routes)) {
            throw new RouterException(sprintf("A routing file MUST return an array. %s given in %s", gettype($routes), $resource_name));
        }
        foreach ($routes as $name => $route) {
            $path = array_key_exists('path', $route) ? $route['path'] : '/';
            $path = $prefix . trim($path, '/');
            $parameters = array_key_exists('parameters', $route) ? (array) $route['parameters'] : [];
            $method = array_key_exists('method', $route) && in_array($route['method'], $this->methods) ? strtoupper($route['method']) : 'ANY';
            $options = [];
            // Controller
            $options['controller'] = array_key_exists('_controller', $route) ? $route['_controller'] : null;
            $options['middleware'] = array_key_exists('_middleware', $route) ? array_merge($middleware, $route['_middleware']) : $middleware;
            $theRoute = new Route($name, $path, $parameters, $method, $options);
            $this->addRoute($theRoute);
        }
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isActiveCache(): bool
    {
        return $this->active_cache;
    }

    /**
     * @param bool $active_cache
     */
    public function setActiveCache(bool $active_cache)
    {
        $this->active_cache = $active_cache;
    }

    /**
     * @param string $resource_dir
     */
    public function setResourceDir(string $resource_dir)
    {
        $this->resource_dir = $resource_dir;
    }

}