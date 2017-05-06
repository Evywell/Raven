<?php


namespace Raven\Framework;


interface ApplicationInterface
{

    public function registerBundle(): array;
    public function run();

}