<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{
    /**
     * renderer
     *
     * @var Framework\Renderer\RendererInterface
     */
    private $renderer;

    /**
     * postTable
     *
     * @var App\Blog\Table\PostTable
     */
    private $postTable;
    
    /**
     * router
     *
     * @var Framework\Router
     */
    private $router;

    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, Router $router, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
    }

    public function __invoke(Request $request)
    {
        return $request->getAttribute('id') ? $this->show($request) : $this->index();
    }

    public function index(): string
    {
        $posts = $this->postTable->findPaginated();
        return $this->renderer->render('@blog/index', compact('posts'));
    }

    
    /**
     * Rend une vue d'un post
     *
     * @param  mixed $request
     * @return ResponseInterface|string
     */
    public function show(Request $request)
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->find($request->getAttribute('id'));

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        
        return $this->renderer->render('@blog/show', ['post' => $post]);
    }
}
