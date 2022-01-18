<?php

namespace Framework\Actions;

use Framework\Router;
use Framework\Validator;
use Framework\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CrudAction
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
     * @var mixed
     */
    private $table;

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

    /**
     * @var string
     */
    protected $viewPath;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var string[]
     */
    protected $messages = [
        'create' => 'L\'élément a bien été créé',
        'edit'   => 'L\'élément a bien été modifié'
    ];

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        $table,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->router = $router;
        $this->flash = $flash;
    }

    public function __invoke(Request $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
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

    /**
     * Affiche la liste des éléments
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findPaginated(12, $params['p'] ?? 1);

        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * Edite un éléménts
     *
     * @param  Request $request
     * @return string|ResponseInterface
     */
    public function edit(Request $request)
    {
        $errors = [];
        $item = $this->table->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);

            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->update($item->id, $params);
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }

            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;
        }

        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->formParams(compact('item', 'errors'))
        );
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
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->insert($params);
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }

            $errors = $validator->getErrors();
        }

        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams(compact('item', 'errors'))
        );
    }
    
    /**
     * delete
     *
     * @param  Request $request
     * @return string
     */
    public function delete(Request $request)
    {
        $this->table->delete($request->getAttribute('id'));

        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Extrait les paramètres nécessaires
     *
     * @param  mixed $request
     * @return string[]
     */
    protected function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Génère le validateur pour valider les données
     * @param Request $request
     * @return \Framework\Validator
     */
    protected function getValidator(Request $request): Validator
    {
        return (new Validator($request->getParsedBody()));
    }

    /**
     * Génère une nouvelle entité
     * @return mixed
     */
    protected function getNewEntity()
    {
        return [];
    }

    /**
     * Permet de traiter les paramètres à envoyer à la vue
     * @param array $params
     *
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
