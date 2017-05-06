<?php


namespace Test\Raven\Middleware;


use Raven\Framework\Middleware\MiddlewareInterface;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

class BarMiddleware implements MiddlewareInterface
{

    public function __invoke(Request $request, Response $response, $next)
    {
        $response->setContent($response->getContent() . "bar");
        return $next($request, $response);
    }
}