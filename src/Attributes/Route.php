<?php

namespace JscPhp\Routes\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    private string $route;
    private array  $http_methods;
    private int    $priority;
    private string $name;
    private bool   $protected;

    public function __construct(string $route, array $http_methods, bool $protected = true, int $priority = 999, string $name = '')
    {
        $this->route = $route;
        $this->http_methods = $http_methods;
        $this->priority = $priority;
        $this->name = $name;
        $this->protected = $protected;
    }

    public function isProtected(): bool
    {
        return $this->protected;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getHttpMethods(): array
    {
        return $this->http_methods;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getName(): string
    {
        return $this->name;
    }
}