<?php

namespace App\Admin;

use Framework\Module;
use Framework\Router;
use App\Admin\DashboardAction;
use Framework\Renderer\RendererInterface;

class AdminModule extends Module
{
    const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    public function __construct(RendererInterface $renderer, Router $router, string $prefix)
    {
        $renderer->addPath('admin', __DIR__ . '/views');
        $router->get($prefix, DashboardAction::class, 'admin');
    }
}
