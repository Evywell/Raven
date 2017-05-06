<?php


namespace Raven\Framework\Network;


use Traversable;

class ParameterBag implements \Countable, \IteratorAggregate
{

    protected $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Return all elements
     * @return array
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * Get the parameter with a $key
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if(!$this->has($key)) {
            throw new \OutOfBoundsException(sprintf("The key %s does not exist", $key));
        }
        return $this->parameters[$key];
    }

    /**
     * Set a parameter
     * @param string $key
     * @param $value
     */
    public function set(string $key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->parameters);
    }
}