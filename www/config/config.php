<?php

use Framework\Router;
use Framework\Session\PHPSession;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;
use Framework\Twig\FlashExtension;
use Psr\Container\ContainerInterface;
use Framework\Session\SessionInterface;
use Framework\Twig\PagerFantaExtension;
use Framework\Renderer\RendererInterface;
use Framework\Router\RouterTwigExtension;
use Framework\Renderer\TwigRendererFactory;

return [
    'database.host' => 'database',
    'database.username' => 'docker',
    'database.password' => 'docker',
    'database.name' => 'docker',
    'views.path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views',
    'twig.extensions' => [
        DI\get(RouterTwigExtension::class),
        DI\get(TextExtension::class),
        DI\get(TimeExtension::class),
        DI\get(PagerFantaExtension::class),
        DI\get(FlashExtension::class)
    ],
    SessionInterface::class => \DI\autowire(PHPSession::class),
    Router::class => \DI\autowire(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    \PDO::class => function (ContainerInterface $c) {
        return new Pdo(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
];
