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
     * @param  mixed $callable
     * @param  mixed $name
     */
    public function get(string $path, callable $callable, string $name)
    {
        $this->router->map('GET', $path, $callable, $name);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(RequestInterface $request): ?Route
    {
        $getUri = $request->getUri()->getPath();
        $getMethod = $request->getMethod();
        die;
        $result = $this->router->match($getUri, $getMethod);
        if ($result) {
            new Route($result['name'], $result['target'], $result['params']);
        }
        return null;
    }
}
