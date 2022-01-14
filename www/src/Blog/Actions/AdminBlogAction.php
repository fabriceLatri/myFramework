<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use Framework\Router;
use Framework\Validator;
use App\Blog\Table\PostTable;
use Framework\Session\FlashService;
use Psr\Http\Message\ResponseInterface;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
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
        $errors = [];
        $item = $this->postTable->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);

            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->postTable->update($item->id, $params);
                $this->flash->success('L\'article a bien été modifié');
                return $this->redirect('blog.admin.index');
            }

            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;
        }

        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
    }

    /**
     * Crée un nouvel article
     *
     * @param  Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $errors = [];
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->postTable->insert($params);
                $this->flash->success('L\'article a bien été créé');
                return $this->redirect('blog.admin.index');
            }

            $errors = $validator->getErrors();
        }
        $item = new Post();
        $item->created_at = new \DateTime();

        return $this->renderer->render('@blog/admin/create', compact('item', 'errors'));
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
        $params =  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function getValidator(Request $request)
    {
        return (new Validator($request->getParsedBody()))
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->datetime('created_at')
            ->slug('slug');
    }
}
