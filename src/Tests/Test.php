<?php

namespace JscPhp\Routes\Tests;

use JscPhp\Routes\Attributes\Route;

class Test
{
    public function __construct()
    {
    }

    #[Route('/test/{id}', ['GET'], name: 'test')]
    #[Route(route: '/test/{id|i}', http_methods: ['GET'], priority: 5, name: 'sample')]
    public function test(string $id)
    {
        echo 'Hello', PHP_EOL;
        print_r(func_get_args());
    }

    #[Route('/call/{apple}/{id|\d{3}}', ['GET'])]
    public function callToArms(string $id, string $apple)
    {
        echo 'Hello', PHP_EOL;
        print_r($id);
    }
}