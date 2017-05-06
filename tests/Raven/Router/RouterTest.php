<?php
namespace Test\Raven\Router;

use Raven\Router\Route;
use Raven\Router\Router;
use Test\Raven\RavenTestCase;

class RouterTest extends RavenTestCase
{

    /**
     * @var Router
     */
    private $router;
    /**
     * @var array
     */
    private $methods = ['ANY', 'GET', 'POST', 'PUT', 'DELETE'];

    protected function setUp()
    {
        parent::setUp();
        $router = new Router();
        $router->initialize();
        $routes = [
            ['name' => 'home', 'path' => '/', 'controller' => 'SimpleBundle:Simple:index', 'args' => []],
            ['name' => 'view', 'path' => '/view/{id}', 'controller' => 'SimpleBundle:Simple:view', 'args' => ['id' => '[0-9]+']],
            ['name' => 'create', 'path' => '/create', 'controller' => 'SimpleBundle:Simple:create', 'args' => []],
            ['name' => 'edit', 'path' => '/edit/{id}', 'controller' => 'SimpleBundle:Simple:edit', 'args' => ['id' => '[0-9]+']],
            ['name' => 'delete', 'path' => '/delete/{id}', 'controller' => 'SimpleBundle:Simple:delete', 'args' => ['id' => '[0-9]+']],
        ];
        foreach ($this->methods as $key => $method) {
            $router->addRoute(new Route($routes[$key]['name'], $routes[$key]['path'], $routes[$key]['args'], $method, ['controller' => $routes[$key]['controller']]));
        }

        $this->router = $router;
    }

    public function testRouteWithCorrectRegexArgs()
    {
        $path = "/view/3365659";
        $this->router->listen($path);
        $route = $this->router->run();
        $this->assertInstanceOf(Route::class, $route, sprintf("Route %s Not Found", $path));

        $path = "/view/first-article-with-4-slug-1561";
        $this->router->addRoute(new Route('route_with_slug', '/view/{slug}-{id}', ['id' => '[0-9]+', 'slug' => '[a-z0-9\-]+']));
        $this->router->listen($path);
        $route = $this->router->run();
        $this->assertInstanceOf(Route::class, $route, sprintf("Route %s Not Found", $path));
    }

    public function testRouteWithIncorrectRegexArgs()
    {
        $path = "/Rview/first-Article-with-4-slug-1561";
        $this->routeWithIncorrectRegex($path);

        $path = "/Rview/first-article-wit_h-4-slug-1561";
        $this->routeWithIncorrectRegex($path);

        $path = "/Rview/first-article-with-4-slug.1561";
        $this->routeWithIncorrectRegex($path);

        $path = "/Rview/first-article-with-4-slug";
        $this->routeWithIncorrectRegex($path);

        $path = "/Rview/156165";
        $this->routeWithIncorrectRegex($path);
    }

    private function routeWithIncorrectRegex($path)
    {
        $this->router->addRoute(new Route('route_with_invalid_slug', '/Rview/{slug}-{id}', ['id' => '[0-9]+', 'slug' => '[a-z0-9\-]+']));
        $this->router->listen($path);
        $route = $this->router->run();
        $this->assertNull($route);
    }

    public function testMatchCorrectRoute()
    {
        $path = "/";
        $info = ['method' => 'ANY', 'controller' => 'SimpleBundle:Simple:index', 'name' => 'home'];
        $this->matchCorrectRoute($path, $info);

        $path = "/view/25";
        $info = ['method' => 'GET', 'controller' => 'SimpleBundle:Simple:view', 'name' => 'view'];
        $this->matchCorrectRoute($path, $info);

        $path = "/edit/25";
        $info = ['method' => 'PUT', 'controller' => 'SimpleBundle:Simple:edit', 'name' => 'edit'];
        $this->matchCorrectRoute($path, $info);

        $path = "/create";
        $info = ['method' => 'POST', 'controller' => 'SimpleBundle:Simple:create', 'name' => 'create'];
        $this->matchCorrectRoute($path, $info);

        $path = "/delete/25";
        $info = ['method' => 'DELETE', 'controller' => 'SimpleBundle:Simple:delete', 'name' => 'delete'];
        $this->matchCorrectRoute($path, $info);
    }

    private function matchCorrectRoute($path, $info)
    {
        $this->router->listen($path);
        $route = $this->router->run();
        $this->assertInstanceOf(Route::class, $route, sprintf("Route %s Not Found", $path));
        $this->assertEquals($info, ['method' => $route->getMethod(), 'controller' => $route->getOptions()['controller'], 'name' => $route->getName()]);
    }

}