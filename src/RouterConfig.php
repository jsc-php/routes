<?php

namespace JscPhp\Routes;

use http\Exception\InvalidArgumentException;

class RouterConfig
{
    private bool  $useMemcached          = false;
    private array $attribute_directories = [];
    private array $memcached_servers     = [];

    /**
     * RouterConfig constructor.
     */
    public function __construct()
    {

    }

    public function getAttributeDirectories(): array
    {
        return $this->attribute_directories;
    }

    /**
     * @param array $attribute_directories
     *
     * @return void
     */
    public function setAttributeDirectories(array $attribute_directories): void
    {
        $this->attribute_directories = $attribute_directories;
    }

    public function getMemcachedServers(): array
    {
        return $this->memcached_servers;
    }

    public function setMemcachedServers(array $memcached_servers): void
    {
        $this->memcached_servers = $memcached_servers;
    }

    public function isUseMemcached(): bool
    {
        return $this->useMemcached;
    }

    public function setUseMemcached(bool $useMemcached): void
    {
        $this->useMemcached = $useMemcached;
    }

    public function addMemcacheDServer(string $host, int $port = 11211): void
    {
        $this->useMemcached = true;
        $this->memcached_servers[] = [$host, $port];
    }

    /**
     * @param string $directory
     *
     * @return void
     */

    public function addAttributeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException('Supplied directory is not a directory');
        }
        $this->attribute_directories[] = $directory;
    }
}