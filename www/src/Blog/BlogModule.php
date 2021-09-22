<?php

namespace App\Blog;

use Framework\Router;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule
{
    private $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath(
            'blog',
            __DIR__ . DIRECTORY_SEPARATOR .'views'
        );
        $router->addMatchTypes(array('slug' => '[a-z\-0-9]+'));
        $router->get('/blog/[slug:slug]', [$this, 'show'], 'blog.show');
        $router->get('/blog', [$this, 'index'], 'blog.index');
    }

    public function index(Request $request): string
    {
        return $this->renderer->render('@blog/index');
    }


    public function show(Request $request): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }
}
