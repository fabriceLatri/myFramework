<?php

namespace Tests\Framework;

use GuzzleHttp\Psr7\Request;
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
        $request = new Request('GET', '/blog');
        $this->router->get('/blog', function () {
            return 'Hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('Hello', call_user_func_array($route->getCallBack(), [$request]));
    }

    public function testGetMethodIfURLDoesNotExists()
    {
        $request = new Request('GET', '/blog');
        $this->router->get('/blogaze', function () {
            return 'Hello';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParams()
    {
        $request = new Request('GET', '/blog/mon-slug-8');
        $this->router->get('/blog', function () {
            return 'azezea';
        }, 'posts');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
            return 'Hello';
        }, 'post.show');
        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('Hello', call_user_func_array($route->getCallBack(), [$request]));
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
    }
}
