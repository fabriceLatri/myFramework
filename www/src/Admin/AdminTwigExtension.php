<?php

namespace App\Admin;

use Twig\TwigFunction;
use App\Admin\AdminWidgetInterface;
use Twig\Extension\AbstractExtension;

class AdminTwigExtension extends AbstractExtension
{
    public function __construct(
        private array $widgets
    ) {
        $this->widgets = $widgets;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('admin_menu', [$this, 'renderMenu'], ['is_safe' => ['html']])
        ];
    }

    public function renderMenu(): string
    {
        return array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->renderMenu();
        }, '');
    }
}
