<?php

use App\Blog\BlogModule;
use Framework\Renderer;

require "../vendor/autoload.php";

$renderer = new Renderer();

$renderer->addPath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views');

$app = new \Framework\App([
  BlogModule::class
], [
  'renderer' => $renderer
]);

$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
\Http\Response\send($response);
