# jsc-php/routes

This package provides a router for handling HTTP requests in PHP applications. It allows you to define routes and
associate them with specific controller actions, making it easier to manage and organize your application's routing
logic.

## Installation

```aiignore
$ composer require jsc-php/routes
```

### Features

- **Attribute-based routing** – Define routes using modern PHP attributes
- **Access control** - Built-in support for protected/public routes
- **Priority Based Routing** – Define routes with priority levels for better control
- **Memcached support** – Optional route caching for improved performance
- **URI parameter extraction** – Automatically extract and pass parameters to your methods
- **Automatic discovery** – Scans directories to find and register routes
- **Method filtering** – Restrict routes to specific HTTP methods (GET, POST, etc.)

## Requirements

- PHP >= 8.5
- ext-zlib
- ext-http
- ext-uri
- ext-memcached

## Usage

Create a 'RouterCOnfig' instance and pass if to the Router Constructor

```
$router_config = new \JscPhp\Routes\RouterConfig();
$router_config->addDirectory('/path/to/controllers');
$router = new Router($router_config)
$router->go();
```

> [!NOTE]
> If you want to use Memcached for caching, ensure Memcached is installed and configured properly
>
> $router_config->addMemcachedServer(host: 'localhost', port: 11211);
>
> Host is optional if using localhost
> Port number is optional if using default port
>
> You can also use setUseMemcached(true);
> This will add a default memcached server using host 'localhost' and port '11211'
>
> For testing, you can also use setUseMemcached(false) after using addMemcachedServer to disable using Memcached

### Route Attribute

Add a \JscPhp\Routes\Attr\Route attribute ot the controller method you want to handle the request

```
use JscPhp\Routes\Attributes\Route;
class Controller {
    #[Route('/post')]
    function post() {...}
}
```

You can define multiple routes by adding addition Route attributes to the same method

```
use JscPhp\Routes\Attributes\Route;
...
#[Route('/post')]
#[Route('/read')]
function getPost() {...}
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

| Type          | Symbol     | Description                        | Example      |
|---------------|------------|------------------------------------|--------------|
| Integer       | `i`        | Matches only integers              | `{id\|i}`    |
| Alpha         | `a`        | Matches only alphabetic characters | `{name\|a}`  |
| Decimal/Float | `d` or `f` | Matches decimal numbers            | `{price\|d}` |

```
 use JscPhp\Routes\Attributes\Route;

 #[Route('/hello/{id}')] - Matches /hello/abc123  
 #[Route('/hello/{id|i}')] - Matches /hello/123 but not /hello/abc123
 ```

For custom types, you can use also use a regex expression after the pipe. Do not include parentheses.

```  
#[Route('/hello/{id|\d{3}}')] - Matches /hello/123 but not /hello/1234
```

### Access Attribute

Adding an `\JscPhp\Routes\Attr\Access` attribute flags the route as protected. To find a matching route, in the
`getRoute()` or `go()`
function, you need to set the `search_private` parameter to `true`.

```
const('IS_LOGGED_IN', isset($_SESSION, 'uid');
$router->go(search_private: IS_LOGGED_IN);
```

