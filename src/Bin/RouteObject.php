<?php

namespace JscPhp\Routes\Bin;

use JscPhp\Routes\Attr\Access;
use JscPhp\Routes\Request;

class RouteObject {
    public string  $class_name {
        get {
            return $this->class_name;
        }
    }
    public string  $method_name {
        get {
            return $this->method_name;
        }
    }
    private string $route;
    private string $regex_pattern;
    private array  $methods;
    private array  $parameters          = [];
    private int    $priority;
    private bool   $protected           = false;
    private string $matched_uri;
    private array  $function_parameters = [];
    private Access $access;

    public function __construct(string $route,
                                array  $methods,
                                string $class_name,
                                string $method_name,
                                int    $priority = 999,) {
        $this->route = $route;
        $this->methods = $methods;
        $this->class_name = $class_name;
        $this->method_name = $method_name;
        $this->priority = $priority;
        $this->buildRegexPattern();
    }

    private function buildRegexPattern(): void {
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
        $this->regex_pattern = '/^' . $pattern . '$/';
    }

    public function getFunctionParameters(): array {
        return $this->function_parameters;
    }

    public function match(string $uri): bool {
        if (preg_match($this->regex_pattern, $uri) === 1) {
            $request_method = Request::getMethod();
            if (empty($request_method) ||
                    in_array($request_method, $this->methods) ||
                    in_array('ALL', $this->methods)) {
                $this->matched_uri = $uri;
                $this->buildFunctionParameters();
                return true;
            }
        }
        return false;
    }

    private function buildFunctionParameters(): void {
        preg_match($this->regex_pattern, $this->matched_uri, $matches);
        $matches = array_slice($matches, 1);
        print_r($matches);
        foreach ($matches as $key => $value) {
            $this->function_parameters[$this->parameters[$key]] = $value;
        }

    }

    public function isProtected(): bool {
        return $this->protected;
    }

    public function setProtected(bool $protected): RouteObject {
        $this->protected = $protected;
        return $this;
    }

    public function getPriority(): int {
        return $this->priority;
    }

    public function getRoute(): string {
        return $this->route;
    }

    public function getRegexPattern(): string {
        return $this->regex_pattern;
    }

    public function getMethods(): array {
        return $this->methods;
    }

    public function getParameters(): array {
        return $this->parameters;
    }

    public function getAccess(): Access {
        if (empty($this->access)) {
            // If the access attribute does not exist, it is assumed that it is not protected
            $this->access = new Access(false);
        }
        return $this->access;
    }

    public function setAccess(Access $access): RouteObject {
        $this->access = $access;
        return $this;
    }
}