<?php

namespace JscPhp\Routes\Attr;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Route {
    private string $route;
    private array  $methods;
    private int    $priority;

    /**
     * @param string $route
     * @param array  $methods
     * @param int    $priority
     */
    public function __construct(string $route, array $methods = ['ALL'], int $priority = 0) {
        $this->methods = array_map('strtoupper', $methods);
        $this->priority = $priority;
        $this->route = $route;
    }

    public function getRoute(): string {
        return $this->route;
    }

    public function getMethods(): array {
        return $this->methods;
    }

    public function getPriority(): int {
        return $this->priority;
    }
}