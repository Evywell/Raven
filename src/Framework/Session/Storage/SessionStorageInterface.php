<?php


namespace Raven\Framework\Session\Storage;


interface SessionStorageInterface
{

    public function start(): bool;
    public function isStarted(): bool;
    public function setId($id): SessionStorageInterface;
    public function getId(): string;

}