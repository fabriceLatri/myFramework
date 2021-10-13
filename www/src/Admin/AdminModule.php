<?php

namespace App\Admin;

use Framework\Module;
use Framework\Renderer\RendererInterface;

class AdminModule extends Module
{
    const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    public function __construct(RendererInterface $renderer)
    {
        $renderer->addPath('admin', __DIR__ . '/views');
    }
}
