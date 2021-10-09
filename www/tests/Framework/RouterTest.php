<?php

namespace Tests\Framework;

use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function () {
            return 'Hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('Hello', call_user_func_array($route->getCallBack(), [$request]));
    }

    public function testGetMethodIfURLDoesNotExists()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blogaze', function () {
            return 'Hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParams()
    {
        $request = new ServerRequest('GET', '/blog/mon-slug-8');
        $this->router->addMatchTypes(array('customSlug' => '[a-z0-9\-]+'));
        $this->router->get('/blog', function () {
            return 'azezea';
        }, 'posts');
        $this->router->get('/blog/[customSlug:slug]-[i:id]', function () {
            return 'Hello';
        }, 'post.show');
        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('Hello', call_user_func_array($route->getCallBack(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
        // Test invalid url
        $route = $this->router->match(new ServerRequest('GET', '/blog/mon_slug-8'));
        $this->assertEquals(null, $route);
    }

    public function testGenerateUri()
    {
        $this->router->addMatchTypes(array('customSlug' => '[a-z0-9\-]+'));
        $this->router->get('/blog', function () {
            return 'azezea';
        }, 'posts');
        $this->router->get('/blog/[customSlug:slug]-[i:id]', function () {
            return 'Hello';
        }, 'post.show');
        $uri = $this->router->generateUri('post.show', ['slug' => 'mon-article', 'id' => 8]);
        $this->assertEquals('/blog/mon-article-8', $uri);
    }

    public function testGenerateUriWithQueryParams()
    {
        $this->router->addMatchTypes(array('customSlug' => '[a-z0-9\-]+'));
        $this->router->get('/blog', function () {
            return 'azezea';
        }, 'posts');
        $this->router->get('/blog/[customSlug:slug]-[i:id]', function () {
            return 'Hello';
        }, 'post.show');
        $uri = $this->router->generateUri(
            'post.show',
            ['slug' => 'mon-article', 'id' => 18],
            ['p' => 2]
        );
        $this->assertEquals('/blog/mon-article-18?p=2', $uri);
    }
}
