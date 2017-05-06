<?php


namespace Raven\Framework\Network;


class HeaderBag extends ParameterBag
{

    private $unFormatedHeaders = [];

    public function set(string $key, $value)
    {
        $this->unFormatedHeaders[$key] = $value;
        $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
        parent::set($key, $value);
    }

}