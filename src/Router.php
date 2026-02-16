<?php

namespace JscPhp\Routes;

use Exception;
use FilesystemIterator;
use JscPhp\Routes\Attributes\Route;
use JscPhp\Routes\Classes\RouteCollection;
use JscPhp\Routes\Utility\File;
use Memcached;
use ReflectionAttribute;
use ReflectionMethod;

/**
 * Router class for handling route configuration and routing.
 */
class Router
{
    public RouteCollection $route_collection;
    private RouterConfig   $config;
    private Memcached      $m;
    private bool           $sequencing_required = false;

    /**
     * Build the router and load the route collection.
     *
     * Scans attribute directories and optionally uses Memcached to cache the
     * serialized collection.
     *
     * @param RouterConfig $config
     */
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

    /**
     * Initialize Memcached connection with configured servers.
     *
     * @return void
     */
    public function initMemcached(): void
    {
        $this->m = new Memcached();
        foreach ($this->config->getMemcachedServers() as $server) {
            $this->m->addServer($server[0], $server[1]);
        }
    }

    /**
     * Process attribute directories and load route classes.
     *
     * @return void
     */
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

    /**
     * Process a single PHP class file and extract public methods..
     *
     * @param string $class_name
     *
     * @return void
     * @throws \ReflectionException
     */
    private function processClass(string $class_name): void
    {
        //echo 'Processing class: ' . $class_name, PHP_EOL;
        $reflect = new \ReflectionClass($class_name);
        foreach ($reflect->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $this->processMethod($method, $class_name);
        }
    }

    /**
     * Process a single method and extract route attributes.
     *
     * @param ReflectionMethod $method
     * @param string           $class_name
     *
     * @return void
     */
    private function processMethod(ReflectionMethod $method, string $class_name): void
    {
        foreach ($method->getAttributes(Route::class) as $attribute) {
            $this->processAttribute($attribute, $class_name, $method->getName());
        }
    }

    /**
     * Process a single route attribute and extract route details.
     *
     * @param ReflectionAttribute $attribute
     * @param string              $class_name
     * @param string              $method
     *
     * @return void
     */

    private function processAttribute(ReflectionAttribute $attribute, string $class_name, string $method): void
    {
        $arguments = $attribute->getArguments();
        $route = $arguments['route'] ?? $arguments[0];
        $methods = $arguments['http_methods'] ?? $arguments[1];
        $name = $arguments['name'] ?? $arguments[2] ?? '';
        $protected = $arguments['protected'] ?? $arguments[3] ?? false;
        $priority = $arguments['priority'] ?? $arguments[4] ?? 999;
        if ($priority < 999) {
            $this->sequencing_required = true;
        }
        $r = new Classes\Route($route, $protected);
        if (strlen($name) > 0) {
            $r->setName($name);
        }
        $r->setClassName($class_name);
        $r->setMethod($method);
        $this->route_collection->addRoute($r, $methods, $priority);
    }

    /**
     * Route the current request URI to the appropriate controller method.
     *
     * @param string|null $uri If not provided, uses the current request URI.
     * @param bool        $protected
     *
     * @return void
     * @throws Exception
     */
    public function route(?string $uri, bool $protected): void
    {
        if (!$uri) {
            $uri = Request::getURI();
        }
        $route = $this->getRoute($uri, $protected);
        if (!$route) {
            throw new Exception('No route found');
        }
        $route->go();
    }

    /**
     * Retrieve a route based on the provided URI.
     *
     * @param string|null $uri If not provided, uses the current request URI.
     *
     * @return Classes\Route|false
     */

    public function getRoute(?string $uri, bool $protected): Classes\Route|false
    {
        if (!$uri) {
            $uri = Request::getURI();
        }
        return $this->route_collection->findMatch($uri, $protected);
    }
}
