<?php


namespace App\CMSBundle\Middleware;


use Raven\Framework\Middleware\MiddlewareInterface;
use Raven\Framework\Network\Exception\ForbiddenException;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

class IpMiddleware implements MiddlewareInterface
{

    public function __invoke(Request $request, Response $response, $next)
    {
        $ip = $this->getIp($request);
        if(!in_array($ip, ['192.168.56.1', '127.0.0.1', '::1'])) {
            throw new ForbiddenException($request->getServer()->get('REQUEST_URI'));
        }
        return $next($request, $response);
    }

    private function getIp(Request $request)
    {
        $server = $request->getServer();
        if($server->has('HTTP_CLIENT_IP')) {
            $ip = $server->get('HTTP_CLIENT_IP');
        } elseif($server->has('HTTP_X_FORWARDED_FOR')) {
            $ip = $server->get('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = $server->has('REMOTE_ADDR') ? $server->get('REMOTE_ADDR') : '';
        }
        return $ip;
    }
}