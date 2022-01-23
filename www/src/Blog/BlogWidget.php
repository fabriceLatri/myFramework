<?php

namespace App\Blog;

use App\Blog\Table\PostTable;
use App\Admin\AdminWidgetInterface;
use Framework\Renderer\RendererInterface;

class BlogWidget implements AdminWidgetInterface
{
    public function __construct(
        private RendererInterface $renderer,
        private PostTable $postTable
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
    }

    public function render(): string
    {
        return $this->renderer->render('@blog/admin/widget');
    }
}
