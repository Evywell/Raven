<?php


namespace Raven\Framework\Network;


class Request
{

    private $query;
    private $request;
    private $server;
    private $cookies;
    private $attributes;
    private $file;
    private $headers;
    private $content;

    public static $methodsAvailable = ['GET', 'POST', 'PUT', 'DELETE', 'ANY'];

    public function __construct(array $query = [], array $request = [], array $server = [], array $cookies = [], array $attributes = [], array $file = [], $content = null)
    {
        $this->initialize($query, $request, $server, $cookies, $attributes, $file, $content);
    }

    public function initialize(array $query = [], array $request = [], array $server = [], array $cookies = [], array $attributes = [], array $file = [], $content = null)
    {
        $this->query = new ParameterBag($query);
        $this->request = new ParameterBag($request);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());
        $this->cookies = new ParameterBag($cookies);
        $this->attributes = new ParameterBag($attributes);
        $this->file = new ParameterBag($file);
        $this->content = $content;
    }

    public static function createFromGlobals()
    {
        return new self($_GET, $_POST, $_SERVER, $_COOKIE, [], $_FILES);
    }

    public function isMethod(string $method): bool
    {
        $method = strtoupper($method);
        return $this->checkMethod($method);
    }

    private function checkMethod(string $method): bool
    {
        if(!in_array($method, self::$methodsAvailable)) {
            return false;
        }
        if($method === "ANY" ||
            $method === $this->server->get('REQUEST_METHOD') ||
            $method === "PUT" && $this->server->get('REQUEST_METHOD') === "POST" && $this->hasInputMethod($method) ||
            $method === "DELETE" && $this->server->get('REQUEST_METHOD') === "POST" && $this->hasInputMethod($method)
        ) {
            return true;
        }
        return false;
    }

    private function hasInputMethod(string $method)
    {
        return isset($_POST['_method']) && strtoupper($_POST['_method']) === $method;
    }

    /**
     * @return ParameterBag
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return ParameterBag
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ServerBag
     */
    public function getServer()
    {
        return $this->server;
    }

}