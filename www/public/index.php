<?php

use DI\ContainerBuilder;

require dirname(__DIR__) . "/vendor/autoload.php";

$modules = [
    App\Admin\AdminModule::class,
    App\Blog\BlogModule::class
];

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$builder->addDefinitions(dirname(__DIR__) . '/config.php');
$container = $builder->build();

$renderer = $container->get(Framework\Renderer\RendererInterface::class);

$app = new \Framework\App($container, $modules);

if (php_sapi_name() !== 'cli') {
    $response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
