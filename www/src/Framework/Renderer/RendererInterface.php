<?php

namespace Framework\Renderer;

interface RendererInterface
{
    /**
     * addGlobal
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void;
    
    /**
     * render
     *
     * @param  mixed $view
     * @param  mixed $params
     * @return string
     */
    public function render(string $view, array $params = []): string;
    
    /**
     * addPath
     *
     * @param  mixed $namespace
     * @param  mixed $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;
}
