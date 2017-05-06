<?php


namespace Raven\Framework\Cache;


class Fragment
{

    /**
     * @var mixed
     */
    private $data;
    /**
     * @var int
     */
    private $expiration;
    /**
     * @var string
     */
    private $key;

    /**
     * @var bool
     */
    private $valid;

    public function __construct(string $key, $data, int $expiration)
    {
        $this->data = $data;
        $this->expiration = $expiration;
        $this->key = $key;
        $this->valid = true;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getExpiration(): int
    {
        return $this->expiration;
    }

    /**
     * @return boolean
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param boolean $valid
     */
    public function setValid(bool $valid)
    {
        $this->valid = $valid;
    }

}