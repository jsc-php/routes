<?php

use JscPhp\Routes\Router;
use JscPhp\Routes\RouterConfig;

ini_set('display_errors', 1);
include_once dirname(__DIR__, 1) . '/vendor/autoload.php';


$router_config = new RouterConfig();
$router_config->addAttributeDirectory(dirname(__DIR__, 1) . '/src/');
//$router_config->addMemcacheDServer('localhost');
$router = new Router($router_config);
print_r($router->route_collection);
$router->route('/call/alpha/321');
//$route->go();
