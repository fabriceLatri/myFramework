<?php

use App\Blog\BlogModule;
use App\Blog\DemoExtension as BlogDemoExtension;
use Blog\DemoExtension;

use function \DI\autowire;
use function \DI\get;

return [
    'blog.prefix' => '/blog',
    BlogModule::class => autowire()->constructorParameter('prefix', get('blog.prefix'))
];
