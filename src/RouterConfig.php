<?php

namespace JscPhp\Routes;

use InvalidArgumentException;

class RouterConfig {
    private array $memcached_servers = [];
    private array $class_directories = [];
    private bool  $use_memcached     = false;

    /**
     * @param array{
     *     memcached_servers?: array,
     *     class_directories?: array
     * } $options
     */
    public function __construct(array $options = []) {
        if (isset($options['memcached_servers'])) {
            foreach ($options['memcached_servers'] as $memcached_server) {
                if (is_string($memcached_server)) {
                    $this->addMemCachedServer($memcached_server);
                }
                if (is_array($memcached_server)) {
                    if (array_is_list($memcached_server)) {
                        if (count($memcached_server) === 1) {
                            $this->addMemCachedServer($memcached_server[0]);
                        }
                        if (count($memcached_server) === 2) {
                            $this->addMemCachedServer($memcached_server[0], $memcached_server[1]);
                        }
                    } else {
                        if (array_key_exists('host', $memcached_server)) {
                            if (array_key_exists('port', $memcached_server)) {
                                $this->addMemCachedServer($memcached_server['host'], $memcached_server['port']);
                            } else {
                                $this->addMemCachedServer($memcached_server['host']);
                            }
                        }
                    }
                }
            }
        }
        if (isset($options['class_directories'])) {
            foreach ($options['class_directories'] as $class_directory) {
                $this->addClassDirectory($class_directory);
            }
        }
    }

    public function addMemCachedServer(string $host = 'localhost', int $port = 11211): self {
        $this->memcached_servers[] = [$host, $port];
        $this->use_memcached = true;
        return $this;
    }

    public function addClassDirectory(string $directory): self {
        $directory = '/' . trim($directory, '/') . '/';
        if (!is_dir($directory)) {
            throw new InvalidArgumentException('Supplied directory is not a directory');
        }
        $this->class_directories[] = $directory;
        return $this;
    }

    public function isUseMemcached(): bool {
        return $this->use_memcached;
    }

    public function setUseMemcached(bool $use_memcached): RouterConfig {
        if (empty($this->memcached_servers)) {
            $this->addMemCachedServer();
        }
        $this->use_memcached = $use_memcached;
        return $this;
    }

    public function useMemcached(): bool {
        return $this->use_memcached;
    }

    public function getMemcachedServers(): false|array {
        if (count($this->memcached_servers) === 0) {
            return false;
        }
        return $this->memcached_servers;
    }

    public function setMemcachedServers(array $memcached_servers): RouterConfig {
        $this->memcached_servers = $memcached_servers;
        return $this;
    }

    public function getClassDirectories(): array {
        return $this->class_directories;
    }

    public function setClassDirectories(array $class_directories): RouterConfig {
        $this->class_directories = $class_directories;
        return $this;
    }
}