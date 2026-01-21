<?php

namespace JscPhp\Routes\Tests;

use JscPhp\Routes\Attributes\Route;

class Test
{
    public function __construct()
    {
    }

    #[Route('/test/{id?}', ['GET'], name: 'test')]
    public function test(string $id)
    {
        echo 'Hello', PHP_EOL;
        print_r(func_get_args());
    }

}