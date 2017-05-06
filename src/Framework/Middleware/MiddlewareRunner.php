<?php


namespace Raven\Framework\Middleware;


use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

class MiddlewareRunner
{

    /**
     * @var MiddlewareQueue
     */
    private $queue;
    /**
     * @var int
     */
    private $index;

    public function run(MiddlewareQueue $middleware, Request $request, Response $response): Response
    {
        $this->queue = $middleware;
        $this->index = 0;
        return $this->__invoke($request, $response);
    }

    public function __invoke(Request $request, Response $response)
    {
        $next = $this->queue->get($this->index);
        if($next && ($next instanceof MiddlewareInterface)) {
            $this->index++;

            return $next($request, $response, $this);
        }

        return $response;
    }

}