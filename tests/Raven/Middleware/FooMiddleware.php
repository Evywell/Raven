<?php


namespace Test\Raven\Middleware;


use Raven\Framework\Middleware\MiddlewareInterface;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

class FooMiddleware implements MiddlewareInterface
{

    public function __invoke(Request $request, Response $response, $next)
    {
        $response->setContent("foo");
        return $next($request, $response);
    }
}