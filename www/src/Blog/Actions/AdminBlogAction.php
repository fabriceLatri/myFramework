<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminBlogAction
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
        return $this->index($request);
    }

    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postTable->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/admin/index', compact('items'));
    }
}
