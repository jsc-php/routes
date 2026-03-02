<?php

namespace JscPhp\Routes\Test;

use JscPhp\Routes\Attr\Route;

class Test {
    #[Route(route: '/test/{id}')]
    public function test_a(string $id): void {
        echo "Private test $id\n";
    }

    #[Route('/test/{id}')]
    public function test_b($id): void {
        echo "Public test b - $id\n";
    }
}