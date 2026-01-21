# JSC Router

A simple PHP router for handling HTTP requests and routing them to appropriate controller methods.

## Installation

```
$ composer require jsc-php/router
```

## Router Class

The `Router` class (`\JscPhp\Routes\Router`) is the core component that automatically discovers routes from PHP
attributes and handles request routing.

### Features

- **Attribute-based routing**: Automatically scans directories for PHP classes with `#[Route]` attributes
- **Caching support**: Optional Memcached integration for performance optimization
- **Priority-based routing**: Control route matching order with priority values
- **Named routes**: Assign names to routes for easier reference
- **Pattern matching**: Supports dynamic URL patterns with parameter extraction

## Usage

Create a 'RouterConfig' instance and pass it to the Router constructor.

```
$router_config = new \JscPhp\Routes\RouterConfig();
$router_config->addDirectory(__DIR__ . '/controllers');
$router = new Router($router_config);
```

> [!NOTE]
> If you want to use Memcached for caching, ensure Memcached is installed and configured properly.
> ```
> $router_config->addMemcacheDServer('localhost', '11211');
> ```
> Port number is optional if using default Memcached port.



