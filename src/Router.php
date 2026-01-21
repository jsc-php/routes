<?php

namespace JscPhp\Routes;

use FilesystemIterator;
use JscPhp\Routes\Attributes\Route;
use JscPhp\Routes\Classes\RouteCollection;
use JscPhp\Routes\Utility\File;
use Memcached;
use ReflectionMethod;

class Router
{
    public RouteCollection $route_collection;
    private RouterConfig   $config;
    private Memcached      $m;
    private bool           $sequencing_required = false;

    public function __construct(RouterConfig $config)
    {
        $this->config = $config;
        $routes_loaded = false;
        if ($this->config->isUseMemcached()) {
            $this->initMemcached();
            if ($cache = $this->m->get('route_collection')) {
                $this->route_collection = unserialize(zlib_decode($cache));
                $routes_loaded = true;
            }
        }
        if (empty($this->route_collection)) {
            $this->route_collection = new RouteCollection();
        }
        if (!$routes_loaded) {
            $this->processDirectories();
            if ($this->sequencing_required) {
                $this->route_collection->sequence();
            }
            if ($this->config->isUseMemcached()) {
                $this->m->set('route_collection', zlib_encode(serialize($this->route_collection), ZLIB_ENCODING_DEFLATE), 60);
            }
        }

    }

    public function initMemcached(): void
    {
        $this->m = new Memcached();
        foreach ($this->config->getMemcachedServers() as $server) {
            $this->m->addServer($server[0], $server[1]);
        }
    }

    private function processDirectories(): void
    {
        foreach ($this->config->getAttributeDirectories() as $directory) {
            if (!is_dir($directory)) {
                throw new \InvalidArgumentException('Supplied directory is not a directory');
            }
            $di = new \RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);
            $fi = new \RecursiveIteratorIterator($di);
            foreach ($fi as $file) {
                if ($file->getExtension() === 'php') {
                    if ($class_name = File::getClassNameFromFile($file->getPathname())) {
                        $this->processClass($class_name);
                    }
                }
            }
        }
    }

    private function processClass(string $class_name): void
    {
        //echo 'Processing class: ' . $class_name, PHP_EOL;
        $reflect = new \ReflectionClass($class_name);
        foreach ($reflect->getMethods() as $method) {
            $this->processMethod($method, $class_name);
        }
    }

    private function processMethod(ReflectionMethod $method, string $class_name): void
    {
        foreach ($method->getAttributes(Route::class) as $attribute) {
            $this->processAttribute($attribute, $class_name, $method->getName());
        }
    }

    private function processAttribute(\ReflectionAttribute $attribute, string $class_name, string $method): void
    {
        $arguments = $attribute->getArguments();
        $route = $arguments['route'] ?? $arguments[0];
        $methods = $arguments['http_methods'] ?? $arguments[1];
        $name = $arguments['name'] ?? $arguments[2] ?? '';
        $priority = $arguments['priority'] ?? $arguments[3] ?? 999;
        if ($priority < 999) {
            $this->sequencing_required = true;
        }
        $r = new Classes\Route($route);
        if (strlen($name) > 0) {
            $r->setName($name);
        }
        $r->setClassName($class_name);
        $r->setMethod($method);
        $this->route_collection->addRoute($r, $methods, $priority);
    }

    public function route(?string $uri = null): void
    {
        if (!$uri) {
            $uri = Request::getURI();
        }
        $route = $this->getRoute($uri);
        if (!$route) {
            throw new \Exception('No route found');
        }
        $route->go();
    }

    public function getRoute(?string $uri = null): Classes\Route|false
    {
        if (!$uri) {
            $uri = Request::getURI();
        }
        return $this->route_collection->findMatch($uri);
    }
}