<?php

namespace App\Blog;

use Framework\Module;
use Framework\Router;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostShowAction;
use App\Blog\Actions\PostIndexAction;
use Psr\Container\ContainerInterface;
use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use Framework\Renderer\RendererInterface;

class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    const MIGRATIONS = __DIR__ . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'migrations';

    const SEEDS = __DIR__ . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'seeds';

    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath(
            'blog',
            __DIR__ . DIRECTORY_SEPARATOR .'views'
        );
        $router = $container->get(Router::class);
        $router->addMatchTypes(array('slug' => '[a-z\-0-9]+'));
        $router->get($container->get('blog.prefix'), PostIndexAction::class, 'blog.index');
        $router->get($container->get('blog.prefix') . '/[slug:slug]-[i:id]', PostShowAction::class, 'blog.show');
        $router->get(
            $container->get('blog.prefix') . '/category/[slug:slug]',
            CategoryShowAction::class,
            'blog.category'
        );
        
        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
        }
    }
}
