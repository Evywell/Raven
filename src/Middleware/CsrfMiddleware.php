<?php


namespace Raven\Middleware;


use Raven\Framework\Middleware\MiddlewareInterface;
use Raven\Framework\Network\Exception\InternalServerErrorException;
use Raven\Framework\Network\Exception\NotFoundException;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;
use Raven\Framework\Session\Session;

class CsrfMiddleware implements MiddlewareInterface
{

    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        if(!$this->verifyCsrf($request)) {
            throw new InternalServerErrorException("Le token de vérification Csrf est invalide");
        }
        return $next($request, $response);
    }

    private function verifyCsrf(Request $request): bool
    {
        $token = $this->session->get('_csrf');
        if(
            ($request->getQuery()->has('_csrf') && $token === $request->getQuery()->get('_csrf')) ||
            ($request->getRequest()->has('_csrf') && $token === $request->getRequest()->get('_csrf'))
        ) {
            return true;
        }

        return false;
    }
}