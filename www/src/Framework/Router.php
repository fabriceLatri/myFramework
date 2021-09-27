<?php

namespace Framework;

use AltoRouter;
use Framework\Router\Route;
use Psr\Http\Message\RequestInterface;

/**
 * Router class
 * Register and match routes
 */
class Router
{
    private $router;

    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    /**
     * @param  mixed $path
     * @param  string|callable $callable
     * @param  mixed $name
     */
    public function get(string $path, $callable, string $name)
    {
        $this->router->map('GET', $path, $callable, $name);
    }

    /**
     * Permet de configurer un type de recherche pour faire matcher certaines routes
     * @param string[] $matcheTypes
     * @return void
     */
    public function addMatchTypes(array $matchTypes): void
    {
        $this->router->addMatchTypes($matchTypes);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(RequestInterface $request): ?Route
    {
        $getUri = $request->getUri()->getPath();
        $getMethod = $request->getMethod();
        $result = $this->router->match($getUri, $getMethod);
        if ($result) {
            return new Route($result['name'], $result['target'], $result['params']);
        }
        return null;
    }

        
    /**
     * generateUri génère une route
     *
     * @param  string $name
     * @param  array $params
     * @return string
     */
    public function generateUri(string $name, array $params): string
    {
        return $this->router->generate($name, $params);
    }
}
