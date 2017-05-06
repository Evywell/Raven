<?php


namespace Raven\Framework\Network;


class FrozenBag extends ParameterBag
{

    public function set(string $key, $value)
    {
        throw new \LogicException("You cannot use the set() method on a frozen ParameterBag");
    }

}