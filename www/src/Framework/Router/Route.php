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
    
    /**
     * __construct
     *
     * @param  mixed $name
     * @param  callable|string $callback
     * @param  mixed $params
     * @return void
     */
    public function __construct(string $name, $callback, array $params)
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
     * @return string|callable
     */
    public function getCallback()
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
