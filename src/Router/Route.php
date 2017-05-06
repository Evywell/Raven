<?php


namespace Raven\Router;


use Raven\Framework\Network\ParameterBag;

class Route
{

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $path;
    /**
     * @var ParameterBag
     */
    private $params;
    /**
     * @var string
     */
    private $method;
    /**
     * @var array
     */
    private $options;

    public function __construct(string $name, string $path, array $params = [], string $method = 'ANY', array $options = [])
    {
        $this->name = $name;
        $this->path = $this->clearPath($path);
        $this->params = new RouteParameter($params);
        $this->method = $method;
        $this->options = $options;
    }

    /**
     * Méthode que ne sert plus. Elle permet de bind un paramètre du format {foo} par sa valeur {foo} -> bar
     */
    public function parseParameters()
    {
        $value = preg_replace_callback('#\{[a-zA-Z]+\}#', function ($matches) {
            foreach ($matches as $match) {
                $match = str_replace(['{', '}'], '', $match);
                if($this->params->has($match)) {
                    return '(' . $this->params->get($match) . ')';
                }
                return $match;
            }
        }, $this->path);
        $this->path = $value;
    }

    public function match(string $url): bool
    {
        $values = preg_replace_callback('#\{[a-zA-Z]+\}#', function ($matches) {
            // Parameter detected -> {id} for example
            $parameter = str_replace(['{', '}'], '', $matches[0]);
            if($this->params->has($parameter)) {
                return '(' . $this->params->get($parameter) . ')';
            }
            return $parameter;
        }, $this->path);
        $this->path = $values;

        if(preg_match('#^' . $this->path . '$#', $url, $matches)) {
            array_shift($matches);
            $keys = array_keys($this->params->all());
            foreach ($keys as $key => $value) {
                $this->params->setParameterValue($value, $matches[$key]);
            }
            return true;
        }
        return false;
    }

    private function clearPath(string $path): string
    {
        $path = str_replace('/', '\/', $path);
        $path = str_replace('$', '\$', $path);
        $path = str_replace('.', '\.', $path);
        $path = str_replace('?', '\?', $path);
        $path = str_replace('(', '\(', $path);
        $path = str_replace(')', '\)', $path);
        $path = str_replace('-', '\-', $path);
        return $path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Route
     */
    public function setName(string $name): Route
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Route
     */
    public function setPath(string $path): Route
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return ParameterBag
     */
    public function getParams(): ParameterBag
    {
        return $this->params;
    }

    /**
     * @param ParameterBag $params
     * @return $this
     */
    public function setParams(ParameterBag $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParamsArray(array $params)
    {
        $this->params = new ParameterBag($params);
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

}