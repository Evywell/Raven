<?php


namespace Raven\Framework\Cache;


class FileSystemCache implements \Serializable, CacheInterface
{

    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var Fragment[]
     */
    private $data;

    public function __construct(string $directory, string $filename)
    {
        $this->directory = $directory;
        $this->filename = $filename;
        $this->data = [];
        $this->load();
    }

    public function set(string $key, $value, $expiration = 3600)
    {
        $expiration_time = time() + $expiration;
        $fragment = new Fragment($key, $value, $expiration_time);
        $this->data[$key] = $fragment;
        $this->update();
    }

    public function get(string $key)
    {
        if(!$this->contains($key)) {
            // TODO: throw new Exception
            return;
        }
        $fragment = $this->data[$key];
        $expiration = $fragment->getExpiration();
        if($expiration < time()) {
            $fragment->setValid(false);
            unset($this->data[$key]);
            $this->update();
            return false;
        }

        return $fragment->getData();
    }

    /**
     * This method updates the cache
     */
    private function update()
    {
        $file = $this->directory . DIRECTORY_SEPARATOR . $this->filename;
        $f = fopen($file, 'w+');
        fwrite($f, $this->serialize());
        fclose($f);
    }

    private function load()
    {
        $file = $this->directory . DIRECTORY_SEPARATOR . $this->filename;
        if(file_exists($file)) {
            $this->unserialize(file_get_contents($file));
        }
    }

    public function contains(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
    }
}