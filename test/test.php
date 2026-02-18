<?php

use JscPhp\Routes\RouterConfig;

ini_set('display_errors', 1);
include_once dirname(__DIR__, 1) . '/vendor/autoload.php';


$router_config = new RouterConfig([
        'class_directories' => [dirname(__DIR__, 1) . '/src/Test/']
]);

$router = new \JscPhp\Routes\Router($router_config);

$router->go('/test/1', false);


