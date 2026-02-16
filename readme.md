# JSC Router

A simple PHP router for handling HTTP requests and routing them to appropriate controller methods using PHP attributes.

## Installation

```
$ composer require jsc-php/routes
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

Create a 'RouterConfig' instance and pass it to the Router constructor:

```
$router_config = new \JscPhp\Routes\RouterConfig();
$router_config->addDirectory('/path/to/controllers');
$router = new Router($router_config);
$router->route();
```

> [!NOTE]
> If you want to use Memcached for caching, ensure Memcached is installed and configured properly.
> ```
> $router_config->addMemcachedServer('localhost', '11211');
> ```
> Port number is optional if using the default Memcached port.

### Route Attribute

Add a JscPhp\Routes\Attributes\Route attribute to the controller method you want to handle the request.

```
use JscPhp\Routes\Attributes\Route;
class Controller {
    #[Route('/post')]
    function post() {...}
}

```

You can also add multiple attributes to the same method.

```
use JscPhp\Routes\Attributes\Route;
...
#[Route('/post')]
#[Route('/read')]
function post() {...}
```

Parameters can be defined it the route path by wrapping them in curly braces.

```
use JscPhp\Routes\Attributes\Route;
...
#[Route('/post/{id}/{page}')]
public function post($id, $page) {...}
```

Adding a question mark to the parameter name makes it optional.

```
#[Route('/post/{id?}')]
```

Adding a pipe <|> to the parameter is optional but can limit the type of values that can be accepted for that
parameter.

| Type          | Symbol     | Description                        | Example                      |
|---------------|------------|------------------------------------|------------------------------|
| Integer       | `i`        | Matches only integers              | `{id\|i}` matches `123`      |
| Alpha         | `a`        | Matches only alphabetic characters | `{name\|a}` matches `john`   |
| Decimal/Float | `d` or `f` | Matches decimal numbers            | `{price\|d}` matches `19.99` |

```
 use JscPhp\Routes\Attributes\Route;

 #[Route('/hello/{id}')] - Matches /hello/abc123  
 #[Route('/hello/{id|i}')] - Matches /hello/123 but not /hello/abc123
 ```

For custom types, you can use also use a regex expression after the pipe. Do not include parentheses.

```  
#[Route('/hello/{id|\d{3}}')] - Matches /hello/123 but not /hello/1234
```




