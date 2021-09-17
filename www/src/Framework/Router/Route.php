<?php

namespace Framework\Router;

/**
 * Route class
 * Represent a matched route
 */
class Route
{
    private $name;

    private $callback;

    private $params;

    public function __construct(string $name, callable $callback, array $params)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->params = $params;
    }

    /**
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * Retrieve URL parameters
     *
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
