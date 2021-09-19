<?php

namespace Tests\Framework\Modules;

use Framework\Router;
use stdClass;

class ErroredModule
{
    public function __construct(Router $router)
    {
        $router->get('/demo', function () {
            return new stdClass();
        }, 'demo');
    }
}
