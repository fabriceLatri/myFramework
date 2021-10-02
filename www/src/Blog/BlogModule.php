<?php

namespace App\Blog;

use App\Blog\Actions\BlogAction;
use Framework\Module;
use Framework\Router;
use Framework\Renderer\RendererInterface;

class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    const MIGRATIONS = __DIR__ . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'migrations';

    const SEEDS = __DIR__ . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'seeds';

    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath(
            'blog',
            __DIR__ . DIRECTORY_SEPARATOR .'views'
        );
        $router->addMatchTypes(array('slug' => '[a-z\-0-9]+'));
        $router->get($prefix . '/[slug:slug]-[i:id]', BlogAction::class, 'blog.show');
        $router->get($prefix, BlogAction::class, 'blog.index');
    }
}
