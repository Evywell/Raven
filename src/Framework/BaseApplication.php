<?php


namespace Raven\Framework;

use Raven\Framework\Bundle\Bundle;
use Raven\Framework\Cache\CacheContainer;
use Raven\Framework\Cache\FileSystemCache;
use Raven\Framework\Config\Configurator;
use Raven\Framework\Container\Container;
use Raven\Framework\Container\ContainerAwareInterface;
use Raven\Framework\Controller\BaseController;
use Raven\Framework\Middleware\MiddlewareQueue;
use Raven\Framework\Middleware\MiddlewareRunner;
use Raven\Framework\Network\Exception\NetworkException;
use Raven\Framework\Network\Exception\NotFoundException;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

abstract class BaseApplication implements ApplicationInterface
{

    /**
     * @var Configurator
     */
    private $configuration;
    /**
     * @var string
     */
    private $environment;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var FileSystemCache
     */
    private $cache;
    /**
     * @var string
     */
    protected $_rootDir;
    /**
     * @var string
     */
    protected $_cacheDir;
    /**
     * @var MiddlewareRunner
     */
    private $runner;
    /**
     * @var array
     */
    private $routeQueue;
    /**
     * @var array
     */
    private $globalQueue;

    const DS = DIRECTORY_SEPARATOR;

    public function __construct()
    {
        $this->startUp();
    }

    /**
     * Run the application
     * @throws RavenFrameworkException
     */
    public function run()
    {
        // Initialize the request
        $request = Request::createFromGlobals();
        try{
            $response = $this->handleRequest($request);
        }catch(NetworkException $e){
            $response = new Response($e->getMessage(), $e->getCode());
        }
        if(!$response instanceof Response) {
            throw new RavenFrameworkException(sprintf("The Application MUST return a %s", Response::class));
        }
        $response->send();
        var_dump($_SERVER);
    }

    /**
     * Generate the Response
     * @param Request $request
     * @return Response
     * @throws NotFoundException
     * @throws RavenFrameworkException
     */
    private function handleRequest(Request $request) : Response
    {
        // TODO: Remplacer ça par une classe qui gère l'HTTP
        $response = new Response();
        // Load global middleware
        $response = $this->loadMiddleware($request, $response);

        $router = $this->container->get('router');
        $url = ($request->getQuery()->has('router')) ? '/' . trim($request->getQuery()->get('router'), '/') : '/';
        $router->listen($url);
        // Get the Controller / Action / Params
        if(($route = $router->run()) === null || !$request->isMethod($route->getMethod())) {
            // Not Route found
            throw new NotFoundException();
        }

        $parameters = $route->getParams()->getParametersFed();
        foreach ($parameters as $key => $parameter) {
            $request->getAttributes()->set($key, $parameter);
        }

        // Route Middleware
        $middleware_name = $route->getOptions()['middleware'];
        $queue = new MiddlewareQueue($this->container);

        foreach ($middleware_name as $item) {
            if(array_key_exists($item, $this->routeQueue)) {
                // Is routeQueue a group ?
                if(is_array($this->routeQueue[$item])) {
                    foreach ($this->routeQueue[$item] as $value) {
                        $queue->add($value);
                    }
                }else{
                    $queue->add($this->routeQueue[$item]);
                }
            }
        }

        $response = $this->runner->run($queue, $request, $response);

        if(($_controller = $route->getOptions()['controller']) !== null) {
            $BundleControllerAction = $this->parseController($_controller);
            $bundle = $BundleControllerAction['bundle'];
            $controller = $BundleControllerAction['controller'];
            $action = $BundleControllerAction['action'];
            // Call the controller and send a Response
            $response = $this->call($bundle, $controller, $action, $request, $parameters, $response);
        }

        return $response;
    }

    private function parseController(string $controller)
    {
        $parts = explode(':', $controller);
        if(count($parts) > 3 || count($parts) < 2) {
            throw new RavenFrameworkException(sprintf("The parameter _controller MUST have 3 parts : Bundle:Controller:Action"));
        } else if(count($parts) === 2) {
            $action = "index";
        } else {
            $action = $parts[2];
        }
        $bundle = $parts[0];
        $controller = $parts[1];
        return ['bundle' => $bundle, 'controller' => $controller, 'action' => $action];
    }

    /**
     * Initialize the application
     * @throws RavenFrameworkException
     */
    private function startUp()
    {
        // Set middleware runner
        $this->runner = new MiddlewareRunner();
        $this->routeQueue = $this->registerRouteMiddleware();
        $this->globalQueue = $this->registerMiddleware();
        // Generate cache directory
        $cacheDir = dirname($this->getRootDir()) . self::DS . 'var' . self::DS . 'cache';
        $this->_cacheDir = $cacheDir;
        if(!is_dir($cacheDir)){
            if(false === mkdir($cacheDir, 0777, true)){
                throw new RavenFrameworkException(sprintf("Unable to create %s directory(ies)", $cacheDir));
            }
        }

        $this->cache = new FileSystemCache($cacheDir, 'container.cache');

        $this->loadConfiguration();
        $this->loadContainer();
        $this->loadBundles();
    }

    /**
     * Call a controller action
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param Request $request
     * @param array $parameters
     * @param Response $response
     * @return Response
     * @throws RavenFrameworkException
     */
    private function call(string $bundle, string $controller, string $action, Request $request, array $parameters, Response $response): Response
    {
        // Controller
        $class_name = $this->className($bundle) . "\\Controller\\" . $controller . "Controller";
        if(!class_exists($class_name)) {
            throw new RavenFrameworkException(sprintf("The Controller %s is not found at location %s", ucfirst($controller), $class_name));
        }

        $controller = new $class_name($request, $response);
        // Set the Container
        if($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        // Controller initialization
        if($controller instanceof BaseController) {
            $controller->initialize();
        }

        // Action
        if(!method_exists($controller, $action)) {
            throw new RavenFrameworkException(sprintf("The action %s does not exist in %s", $action, $class_name));
        }

        // Response
        $response = call_user_func_array([$controller, $action], array_merge(['request' => $request], $parameters));
        if(!$response instanceof Response) {
            throw new RavenFrameworkException(sprintf("Controller MUST return an instance of %s", Response::class));
        }
        return $response;
    }

    /**
     * Get the class name
     * @param string $bundleName
     * @return string
     */
    private function className(string $bundleName)
    {
        return "App\\" . $bundleName;
    }

    private function loadMiddleware(Request $request, Response $response)
    {
        $middleware = new MiddlewareQueue($this->container);
        foreach ($this->globalQueue as $item) {
            $middleware->add($item);
        }
        return $this->runner->run($middleware, $request, $response);
    }

    /**
     * Load the bundles
     * @throws RavenFrameworkException
     */
    private function loadBundles()
    {
        $bundles = $this->registerBundle();
        foreach ($bundles as $bundle) {
            if(!$bundle instanceof Bundle){
                throw new RavenFrameworkException(sprintf("A bundle MUST extends %s class", Bundle::class));
            }
            $this->container->set('bundle.' . $bundle->getName(), $bundle);
            $this->registerServices($bundle->getName());
            $bundle->setContainer($this->container);
            $bundle->boot();
        }
    }

    /**
     * Load the configuration
     */
    private function loadConfiguration()
    {
        $configurator = new Configurator($this->getParameters());
        require_once $this->getConfigDir() . self::DS . "config.php";
        $this->configuration = $configurator;
    }

    /**
     * Register Bundle Services
     * @param string $bundle
     */
    private function registerServices(string $bundle)
    {
        $path = $this->getRootDir() . self::DS . $bundle . self::DS . "config" . self::DS . "services.php";
        if(file_exists($path)) {
            $container = $this->container;
            require_once $path;
        }
    }

    /**
     * Load the container
     */
    private function loadContainer()
    {
        if($this->isCacheActive() && $this->cache->contains('container')) {
            $this->container = $this->cache->get('container');
            if($this->container) {
                return;
            }
        }

        $container = $this->registerContainer();
        $this->container = $container;

        if($this->isCacheActive()) {
            $this->cache->set('container', $container, 86400); // 86400 = 1 day
        }
    }

    /**
     * Generate the container
     * @return Container
     * @throws RavenFrameworkException
     */
    private function registerContainer() : Container
    {
        $container = new Container($this->getParameters());
        $container->set('cache', $this->cache);

        $services_filename = $this->getConfigDir() . self::DS . "services_" . $this->getEnvironment() . ".php";
        if(!file_exists($services_filename)){
            throw new RavenFrameworkException(sprintf("The file %s does not exist", $services_filename));
        }

        require_once $services_filename;
        return $container;
    }

    /**
     * Get the environment
     * @return string
     */
    public function getEnvironment(): string
    {
        if($this->environment === null){
            $this->environment = $this->configuration->get('framework')['environment'];
        }
        return $this->environment;
    }

    /**
     * Get the Root Directory
     * @return string
     */
    public function getRootDir(): string
    {
        if($this->_rootDir === null) {
            $this->_rootDir = dirname((new \ReflectionClass($this))->getFileName());
        }
        return $this->_rootDir;
    }

    /**
     * Get the configuration directory
     * @return string
     */
    private function getConfigDir()
    {
        return $this->getRootDir() . self::DS . "config";
    }

    private function getCacheContainerClass()
    {
        return CacheContainer::class;
    }

    private function getContainerClass()
    {
        if ($this->isCacheActive()) {
            return $this->getCacheContainerClass();
        }
        return Container::class;
    }

    /**
     * Is the cache active
     * @return bool
     */
    public function isCacheActive()
    {
        return !true;
    }

    /**
     * Get the Application parameters
     * @return array
     */
    public function getParameters()
    {
        return [
            'root_dir' => $this->getRootDir(),
            'cache_dir' => $this->_cacheDir
        ];
    }

}