<?php

class RouterConfig
{
    private array $paths        = [];
    private bool  $dev          = false {
        get {
            return $this->dev;
        }
        set {
            $this->dev = $value;
        }
    }
    private bool  $useMemcached = false {
        get {
            return $this->useMemcached;
        }
        set {
            $this->useMemcached = $value;
        }
    }


    public function __construct()
    {
    }

    public function addPath(string $file_path): void
    {
        $this->paths[] = $file_path;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }
}