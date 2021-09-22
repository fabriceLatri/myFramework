<?php

use App\Blog\BlogModule;
use Framework\Renderer\TwigRenderer;

require "../vendor/autoload.php";

$renderer = new TwigRenderer(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views');

$app = new \Framework\App([
  BlogModule::class
], [
  'renderer' => $renderer
]);

$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
\Http\Response\send($response);
