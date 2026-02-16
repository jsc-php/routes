<?php

namespace JscPhp\Routes\Tests;

use JscPhp\Routes\Attributes\Route;

class Test
{
    public function __construct()
    {

    }

    #[Route('/test/{id}', ['GET'], protected: false, name: 'test')]
    public function test(string $id, ?string $test = null)
    {
        echo 'Hello', PHP_EOL;
        print_r(func_get_args());
    }

    #[Route('/test/{id}', ['GET'], protected: true, name: 'test_private')]
    public function test_private(string $id, ?string $test = null)
    {
        echo 'Hello', PHP_EOL;
        print_r(func_get_args());
    }

}