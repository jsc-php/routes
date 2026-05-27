<?php

namespace JscPhp\Routes;

use JscPhp\Routes\Bin\RouteObject;

class RouteCollection {

    private array $public_routes = [];
    private bool  $sequenced     = false;

    public function __construct() {
    }

    public function addRoute(RouteObject $route): void {
        if ($this->checkForDuplicate($route)) {
            throw new \Exception("Duplicate route object");
        }
        $this->public_routes[$route->getPriority()][] = $route;
        $this->sequenced = false;
    }

    public function checkForDuplicate(RouteObject $route): bool {
        foreach ($this->public_routes as $priority) {
            foreach ($priority as $existing_route) {
                if ($this->isSameRoute($existing_route, $route)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isSameRoute(RouteObject $route1, RouteObject $route2): bool {
        return $route1->getRegexPattern() === $route2->getRegexPattern()
                && $route1->getMethods() === $route2->getMethods()
                && $route1->getPriority() === $route2->getPriority();
    }

    public function findRoute(string $uri = ''): false|RouteObject {
        if (empty($uri)) {
            $uri = Request::getUri();
        }
        $this->sequenceRoutes();
        return $this->findRoute2($uri, $this->public_routes);
    }

    private function sequenceRoutes(): void {
        if ($this->sequenced) {
            return;
        }
        ksort($this->public_routes);
        $this->sequenced = true;
    }

    private function findRoute2(string $uri, array $routes): false|RouteObject {
        foreach ($routes as $priority) {
            foreach ($priority as $route_object) {
                if ($route_object->match($uri)) {
                    return $route_object;
                }
            }
        }
        return false;
    }
}