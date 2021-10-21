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
    public function get(string $path, $callable, ?string $name = null)
    {
        $this->router->map('GET', $path, $callable, $name);
    }

    /**
     * @param  mixed $path
     * @param  string|callable $callable
     * @param  mixed $name
     */
    public function post(string $path, $callable, ?string $name = null)
    {
        $this->router->map('POST', $path, $callable, $name);
    }

    /**
     * @param  mixed $path
     * @param  string|callable $callable
     * @param  mixed $name
     */
    public function delete(string $path, $callable, ?string $name = null)
    {
        $this->router->map('DELETE', $path, $callable, $name);
    }
    
    /**
     * crud
     *
     * @param  string $prefixPath
     * @param  string $callable
     * @param  string $prefixName
     * @return void
     */
    public function crud(string $prefixPath, string $callable, string $prefixName)
    {
        $this->get("$prefixPath", $callable, "$prefixName.index");
        $this->get("$prefixPath/new", $callable, "$prefixName.create");
        $this->post("$prefixPath/new", $callable);
        $this->get("$prefixPath/[i:id]", $callable, "$prefixName.edit");
        $this->post("$prefixPath/[i:id]", $callable);
        $this->delete("$prefixPath/[i:id]", $callable, "$prefixName.delete");
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
    public function generateUri(string $name, array $params = [], $queryParams = []): string
    {
        $uri = $this->router->generate($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }

        return $uri;
    }
}
