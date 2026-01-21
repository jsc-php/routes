<?php

namespace JscPhp\Routes;

use http\Exception\InvalidArgumentException;

class RouterConfig
{
    private bool  $useMemcacheD          = false;
    private array $attribute_directories = [];
    private array $memcached_servers = [];

    public function __construct()
    {

    }

    public function getAttributeDirectories(): array
    {
        return $this->attribute_directories;
    }

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

    public function isUseMemcacheD(): bool
    {
        return $this->useMemcacheD;
    }

    public function setUseMemcacheD(bool $useMemcacheD): void
    {
        $this->useMemcacheD = $useMemcacheD;
    }

    public function addMemcacheDServer(string $host, int $port = 11211): void
    {
        $this->useMemcacheD = true;
        $this->memcached_servers[] = [$host, $port];
    }

    public function addAttributeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException('Supplied directory is not a directory');
        }
        $this->attribute_directories[] = $directory;
    }
}