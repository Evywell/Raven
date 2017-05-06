<?php


namespace Raven\Framework\Session\Storage;


use Raven\Framework\Network\ParameterBag;

class NativeSessionStorage implements SessionStorageInterface
{
    /**
     * @var bool
     */
    private $started = false;
    /**
     * @var ParameterBag
     */
    private $bag;

    /**
     * Start the session
     * @return bool
     */
    public function start(): bool
    {
        if($this->isStarted()) {
            return true;
        }

        if(session_status() === PHP_SESSION_ACTIVE) {
            throw new \RuntimeException(sprintf("The session is already started"));
        }

        if(!session_start()) {
            return false;
        }

        $this->loadSession();
        return true;
    }

    protected function loadSession()
    {
        $this->bag = new ParameterBag($_SESSION);
        $this->started = true;
    }

    /**
     * Is session already started ?
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * Set the session id
     * @param $id
     * @return SessionStorageInterface
     */
    public function setId($id): SessionStorageInterface
    {
        session_id($id);
        return $this;
    }

    /**
     * Get the session id
     * @return string
     */
    public function getId(): string
    {
        return session_id();
    }

    /**
     * @return ParameterBag
     */
    public function getBag(): ParameterBag
    {
        return $this->bag;
    }
}