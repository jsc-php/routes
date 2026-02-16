<?php

namespace JscPhp\Routes\Classes;

use JscPhp\Routes\Request;

class RouteCollection
{
    private array $collection = [];

    public function addRoute(Route $route, array $http_methods, string $name = '', int $priority = 999): void
    {
        $http_methods = array_map('strtoupper', $http_methods);
        foreach ($http_methods as $method) {
            $this->collection[$method][$priority][] = $route;
        }
    }

    public function sequence(): void
    {
        $new = [];
        foreach ($this->collection as $method => $routes) {
            ksort($routes);
            $new[$method] = $routes;
        }
        $this->collection = $new;
    }

    public function findMatch(?string $uri = null, bool $public = true): Route|false
    {
        if (!$uri) {
            $uri = $_SERVER['REQUEST_URI'];
        }
        $method = Request::getMethod() ?? 'GET';
        foreach ($this->collection[$method] as $priority) {
            foreach ($priority as $route) {
                if ($route->match($uri, $public)) {
                    return $route;
                }
            }
        }
        return false;
    }

}