<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
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
    
    /**
     * session
     *
     * @var FlashService
     */
    private $flash;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $postTable,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postTable->findPaginated(12, $params['p'] ?? 1);

        return $this->renderer->render('@blog/admin/index', compact('items'));
    }

    /**
     * Edite un article
     *
     * @param  Request $request
     * @return string|ResponseInterface
     */
    public function edit(Request $request)
    {
        $item = $this->postTable->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i:s');
            $this->postTable->update($item->id, $params);
            $this->flash->success('L\'article a bien été modifié');
            return $this->redirect('blog.admin.index');
        }

        return $this->renderer->render('@blog/admin/edit', compact('item'));
    }

    /**
     * Crée un nouvel article
     *
     * @param  Request $request
     * @return string
     */
    public function create(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $this->postTable->insert($params);
            return $this->redirect('blog.admin.index');
        }

        return $this->renderer->render('@blog/admin/create');
    }
    
    /**
     * delete
     *
     * @param  Request $request
     * @return string
     */
    public function delete(Request $request)
    {
        $this->postTable->delete($request->getAttribute('id'));

        return $this->redirect('blog.admin.index');
    }

    /**
     * Extrait les paramètres nécessaires
     *
     * @param  mixed $request
     * @return string[]
     */
    private function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
