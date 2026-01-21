<?php

namespace JscPhp\Routes\Classes;

use JscPhp\Routes\Request;

class Route
{

    private string $route;
    private string $pattern;
    private string $class;
    private string $method;
    private array  $parameters = [];
    private string $name;

    public function __construct(string $route)
    {
        $route = trim($route, '/');
        $route = '/' . $route;
        $this->route = $route;
        $this->buildRegexPattern();
    }

    public function buildRegexPattern(): void
    {
        $pattern = $this->route;
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = str_replace('?', '\?', $pattern);
        preg_match_all('/{([^\/=&?]*)}/', $this->route, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $parts = [];
            if (str_contains($match[1], '|')) {
                $pos = strpos($match[1], '|');
                $parts[] = substr($match[1], 0, $pos);
                $parts[] = substr($match[1], $pos + 1);
            } else {
                $parts[] = $match[1];
            }
            $this->parameters[] = $parts[0];

            if (count($parts) == 1) {
                $r = '([^\/=?]+)';
            } else if (strlen($parts[1]) > 1) {
                $r = '(' . $parts[1] . ')';
            } else {
                $r = match ($parts[1]) {
                    'i'       => '(\d+)',
                    'a'       => '([a-zA-Z]+)',
                    'd', 'f', => '(\d+\.{0,1}\d*)',
                    default   => '([^\/=?]+)'
                };
            }
            $pattern = str_replace($match[0], $r, $pattern);
        }
        $this->pattern = '/^' . $pattern . '$/';
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setClassName(string $class): void
    {
        $this->class = $class;
    }

    public function match(?string $uri = null): bool
    {
        if (!$uri) {
            $uri = Request::getURI();
        }
        $uri = '/' . trim($uri, '/');
        if (preg_match($this->pattern, $uri)) {
            $this->parameters = $this->getParametersFromURI($uri);
            return true;
        }
        return false;
    }

    public function getParametersFromURI(?string $uri = null): array
    {
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

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function go()
    {
        $class = new $this->class;
        $class->{$this->method}(...$this->parameters);
    }

}