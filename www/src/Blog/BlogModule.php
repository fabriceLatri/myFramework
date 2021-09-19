<?php

namespace App\Blog;

use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule
{
    public function __construct(Router $router)
    {
        $router->addMatchTypes(array('slug' => '[a-z\-]+'));
        $router->get('/blog/[slug:slug]', [$this, 'show'], 'blog.show');
        $router->get('/blog', [$this, 'index'], 'blog.index');
    }

    public function index(Request $request): string
    {
        return '<h1>Bienvenue sur le blog</h1>';
    }


    public function show(Request $request): string
    {
        return '<h1>Bienvenue sur l\'article '. $request->getAttribute('slug') . '</h1>';
    }
}
