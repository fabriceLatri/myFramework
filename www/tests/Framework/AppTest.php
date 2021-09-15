<?php

namespace Tests\Framework;

use Framework\App;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;

class AppTest extends TestCase
{

  public function testRedirectTrailingSlash()
  {
    $app = new App();
    $request = new ServerRequest('GET', '/demoslash/');
    $response = $app->run($request);
    $this->assertContains('/demoslash', $response->getHeader('Location'));
    $this->assertEquals(301, $response->getStatusCode());
  }

  public function testBlog()
  {
    $app = new App();
    $request = new ServerRequest('GET', '/blog');
    $response = $app->run($request);
    $this->assertEquals('<h1>Bienvenue sur mon blog</h1>', (string)$response->getBody());
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function testError404()
  {
    $app = new App();
    $request = new ServerRequest('GET', '/adr');
    $response = $app->run($request);
    $this->assertEquals('<h1>Erreur 404</h1>', (string)$response->getBody());
    $this->assertEquals(404, $response->getStatusCode());
  }
}
