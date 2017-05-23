<?php


namespace Raven\Framework\Controller;


use Raven\Framework\Container\ContainerAwareInterface;
use Raven\Framework\Container\ContainerTrait;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

abstract class BaseController implements ContainerAwareInterface
{

    use ContainerTrait;
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function initialize() {}

}