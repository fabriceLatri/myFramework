<?php

namespace App\Admin;

use Framework\Renderer\RendererInterface;

class DashboardAction
{

    /**
     * @param RendererInterface $renderer
     * @param AdminWidgetInterface[] $widgets
     */
    public function __construct(
        private RendererInterface $renderer,
        private array $widgets
    ) {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    public function __invoke()
    {
        $widgets = array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->render();
        }, '');
        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }
}
