<?php
namespace Raven\Framework\Middleware;

use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

interface MiddlewareInterface
{

    public function __invoke(Request $request, Response $response, $next);

}