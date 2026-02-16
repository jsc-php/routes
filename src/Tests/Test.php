<?php

namespace JscPhp\Routes\Tests;

use JscPhp\Authorization\Attr\Access;
use JscPhp\Routes\Attributes\Route;

class Test {
    public function __construct() {

    }

    #[Route('/test/{id}', ['GET'], name: 'test')]
    public function test(string $id, ?string $test = null) {
        echo 'Hello', PHP_EOL;
        print_r(func_get_args());
    }

    #[Route('/test/{id}', ['GET'], name: 'test_protected')]
    #[Access('test', 'view', 1)]
    public function test2(string $id, ?string $test = null) {
        echo 'Hello', PHP_EOL;
        print_r(func_get_args());
    }

}