<?php

namespace App\Blog\Actions;

use Framework\Router;
use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\CrudAction;
use Framework\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{
    protected $viewPath = "@blog/admin/posts";

    protected $routePrefix = 'blog.admin';

    private $categoryTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable,
        FlashService $flash,
        CategoryTable $categoryTable
    ) {
        parent::__construct($renderer, $router, $postTable, $flash);
        $this->categoryTable = $categoryTable;
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime();
        return $post;
    }

    /**
     * Extrait les paramètres nécessaires
     *
     * @param  mixed $request
     * @return string[]
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(ServerRequestInterface $request): \Framework\Validator
    {
        return parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->datetime('created_at')
            ->slug('slug');
    }
}
