<?php

namespace JscPhp\Routes\Attr;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Controller
{
    public function __construct(private string $base_path)
    {
    }

    public function getBasePath(): string
    {
        return $this->base_path;
    }
}
