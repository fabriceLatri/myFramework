<?php

namespace App\Blog\Actions;

use Framework\Router;
use App\Blog\Table\CategoryTable;
use Framework\Actions\CrudAction;
use Framework\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryCrudAction extends CrudAction
{
    protected $viewPath = "@blog/admin/categories";

    protected $routePrefix = 'blog.category.admin';

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        CategoryTable $categoryTable,
        FlashService $flash
    ) {
        parent::__construct($renderer, $router, $categoryTable, $flash);
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
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(ServerRequestInterface $request)
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug');
    }
}
