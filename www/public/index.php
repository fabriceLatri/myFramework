<?php

require "../vendor/autoload.php";

$app = new \Framework\App([
  BlogModule::class
]);

$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());
\Http\Response\send($response);