<?php

namespace Framework\Twig;

use Framework\Router;
use Twig\TwigFunction;
use Pagerfanta\Pagerfanta;
use Twig\Extension\AbstractExtension;
use Pagerfanta\View\TwitterBootstrap4View;

class PagerFantaExtension extends AbstractExtension {

    /**
     * @var Router
     */
    private $router;


    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
        ];
    }

    public function paginate(Pagerfanta $paginatedResults, string $route, array $queryArgs = [])
    {

        $view = new TwitterBootstrap4View();
        return $view->render($paginatedResults, function ($page) use ($route, $queryArgs) {
            if ($page > 1) {
                $queryArgs['p'] = $page;
            }
            $this->router->generateUri($route, [], $queryArgs);
        });
    }
}