<?php

namespace JscPhp\Routes\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    public function __construct(string $route, array $http_methods, int $priority = 999, string $name = '')
    {
    }
}