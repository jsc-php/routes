<?php

namespace JscPhp\Routes;

use FilesystemIterator;
use JscPhp\Routes\Attr\Access;
use JscPhp\Routes\Attr\Route;
use JscPhp\Routes\Bin\RouteObject;
use JscPhp\Routes\Utility\File;
use Memcached;

class Router {

    private RouterConfig    $config;
    private \Memcached      $memcached;
    private RouteCollection $route_collection;
    private RouteObject     $route_object;

    public function __construct(RouterConfig $config) {
        $this->config = $config;
        $this->route_collection = new RouteCollection();
        if ($config->useMemcached()) {
            $this->initMemCachedServers();
            if ($this->loadRouteCollectionFromMemCached()) {
                return;
            }
        }
        $this->processDirectories();
        if ($config->useMemcached()) {
            $this->memcached->set('route_collection', zlib_encode(serialize($this->route_collection), ZLIB_ENCODING_DEFLATE));
        }
    }

    private function initMemCachedServers(): void {
        $this->memcached = new Memcached();
        $servers = $this->config->getMemcachedServers();
        foreach ($servers as $server) {
            $this->memcached->addServer($server[0], $server[1]);
        }
    }

    private function loadRouteCollectionFromMemCached(): bool {

        if ($rc = $this->memcached->get('route_collection')) {
            $this->route_collection = unserialize(zlib_decode($rc));
            return true;
        }
        return false;

    }

    private function processDirectories(): void {
        if (empty($this->route_collection)) {
            $this->route_collection = new RouteCollection();
        }
        foreach ($this->config->getClassDirectories() as $class_directory) {
            if (!is_dir($class_directory)) {
                throw new \Exception("Class directory {$class_directory} does not exist");
            }
            $di = new \RecursiveDirectoryIterator($class_directory, FilesystemIterator::SKIP_DOTS);
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

    private function processClass(string $class_name): void {
        $reflect = new \ReflectionClass($class_name);
        foreach ($reflect->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(Route::class) as $attr_route) {
                //@var Route $route
                $route = $attr_route->newInstance();
                $rte = new RouteObject($route->getRoute(),
                        $route->getMethods(),
                        $class_name,
                        $method->getName());
                foreach ($method->getAttributes(Access::class) as $attr_access) {
                    /** @var Access $access */
                    $access = $attr_access->newInstance();
                    $rte->setAccess($access);
                }
                $this->route_collection->addRoute($rte);
            }
        }
    }

    public function getRoute(string $uri = '', bool $search_private = false): false|RouteObject {
        if (empty($uri)) {
            $uri = Request::getUri();
        }
        if ($route = $this->route_collection->findRoute($uri, $search_private)) {
            $this->route_object = $route;
        }
        return $route;
    }

    public function go(string $uri = '', bool $search_private = false): void {
        $this->getRoute($uri, $search_private);
        $class = new $this->route_object->class_name();
        $class->{$this->route_object->method_name}(...$this->route_object->getFunctionParameters());
    }
}