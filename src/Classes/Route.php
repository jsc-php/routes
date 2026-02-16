<?php

namespace JscPhp\Routes\Classes;

use JscPhp\Routes\Request;

class Route {
    private string $route;
    private string $pattern;
    private string $class;
    private string $method;
    private array  $parameters = [];
    private string $name;
    private bool   $protected  = false;

    public function __construct(\JscPhp\Routes\Attributes\Route|string $route) {
        if ($route instanceof \JscPhp\Routes\Attributes\Route) {
            $this->route = $route->getRoute();
        }
        if (is_string($route)) {
            $this->route = '/' . trim($route, '/');
        }

        $this->buildRegexPattern();
    }

    public function buildRegexPattern(): void {
        $pattern = $this->route;
        $pattern = str_replace('/', '\/', $pattern);
        preg_match_all('/{(.*?)}/', $this->route, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $parts = [];
            if (str_contains($match[1], '|')) {
                $pos = strpos($match[1], '|');
                $parts[] = substr($match[1], 0, $pos);
                $parts[] = substr($match[1], $pos + 1);
            } else {
                $parts[] = $match[1];
            }

            $optional = str_ends_with($parts[0], '?');
            $this->parameters[] = trim($parts[0], '?');
            if (count($parts) == 1) {
                $r = '([^\/=&?]' . (($optional) ? '*':'+') . ')';
            } else if (strlen($parts[1]) > 1) {
                $p = trim($parts[1], '()');
                $r = '(' . $p . ')';
            } else {
                if (in_array($parts[1], ['d', 'f'])) {
                    $r = '(\d+\.{0,1}\d*)';
                } else {
                    $r = match ($parts[1]) {
                        'i'     => '(\d',
                        'a'     => '([a-zA-Z]',
                        default => '([^\/=?])'
                    };
                    $r .= (($optional) ? '*':'+') . '?)';
                }
            }
            $pattern = str_replace($match[0], $r, $pattern);
        }
        $this->pattern = '/^' . $pattern . '$/';
    }

    public function isProtected(): bool {
        return $this->protected;
    }

    public function setProtected(bool $protected): void {
        $this->protected = $protected;
    }

    public function getClass(): string {
        return $this->class;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function setMethod(string $method): void {
        $this->method = $method;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setClassName(string $class): void {
        $this->class = $class;
    }

    public function match(?string $uri = null, array $options = []): bool {
        if (!$uri) {
            $uri = Request::getURI();
        }

        if (isset($options['protected']) && $options['protected']) {
            if ($this->protected) {
                if (preg_match($this->pattern, $uri)) {
                    $this->parameters = $this->getParametersFromURI($uri);
                    return true;
                }
            }
            return false;
        }
        if (preg_match($this->pattern, $uri)) {
            $this->parameters = $this->getParametersFromURI($uri);
            return true;
        }

        return false;
    }

    public function getParametersFromURI(?string $uri = null): array {
        if (!$uri) {
            $uri = Request::getURI();
        }
        preg_match($this->pattern, $uri, $matches);
        $matches = array_slice($matches, 1);
        $params = [];
        foreach ($matches as $key => $value) {
            $params[$this->parameters[$key]] = $value;
        }
        return $params;
    }

    public function getPattern(): string {
        return $this->pattern;
    }

    public function go(): void {
        $class = new $this->class;
        $class->{$this->method}(...$this->parameters);
    }

}