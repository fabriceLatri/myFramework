<?php

namespace Framework\Renderer;

use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
       * addGlobal
       *
       * @param  mixed $key
       * @param  mixed $value
       * @return void
       */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * render
     *
     * @param  mixed $view
     * @param  mixed $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }
    /**
     * addPath
     *
     * @param  mixed $namespace
     * @param  mixed $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->twig->getLoader()->addPath($path, $namespace);
    }

    public function getPaths(): array
    {
        return $this->loader->getPaths();
    }
}
