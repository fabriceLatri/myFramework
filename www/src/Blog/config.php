<?php

use App\Blog\BlogModule;
use App\Blog\BlogWidget;
use App\Blog\DemoExtension as BlogDemoExtension;
use Blog\DemoExtension;

use function \DI\autowire;
use function \DI\get;

return [
    'blog.prefix' => '/blog',
    'admin.widgets' =>\DI\add([
        get(BlogWidget::class)
    ])
];
