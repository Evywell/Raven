<?php


namespace Raven\Router;


use Raven\Framework\Network\ParameterBag;

class RouteParameter extends ParameterBag
{

    private $parametersFed = [];

    /**
     * @param string $key
     * @param string $value
     */
    public function setParameterValue(string $key, string $value)
    {
        $this->parametersFed[$key] = $value;
    }

    /**
     * @return array
     */
    public function getParametersFed()
    {
        return $this->parametersFed;
    }


}