<?php

namespace Test\Blog\Actions;

use Framework\Router;
use Prophecy\Argument;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use Prophecy\PhpUnit\ProphecyTrait;
use App\Blog\Actions\PostShowAction;
use Framework\Renderer\RendererInterface;

class BlogActionTest extends TestCase
{
    /**
     * action
     *
     * @var BlogAction
     */
    private $action;
    
    /**
     * renderer
     *
     * @var Renderer
     */
    private $renderer;
    
    /**
     * pdo
     *
     * @var PDO
     */
    private $postTable;
    
    /**
     * router
     *
     * @var Router
     */
    private $router;

    use ProphecyTrait;
    
    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render(Argument::any())->willReturn('');
        $this->postTable = $this->prophesize(PostTable::class);

        // Router
        $this->router = $this->prophesize(Router::class);
        $this->action = new PostShowAction(
            $this->renderer->reveal(),
            $this->router->reveal(),
            $this->postTable->reveal(),
        );
    }

    public function makePost(int $id, string $slug): Post
    {
        $post = new Post();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }


    public function testShowRedirect()
    {
        $post = $this->makePost(9, 'zaezae-azeaze');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');
        
        $this->router->generateUri('blog.show', ['id' => $post->id, 'slug' => $post->slug])->willReturn('/demo2');

        $this->postTable->find($post->id)->willReturn($post);
        
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(9, 'zaezae-azeaze');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);

        $this->postTable->find($post->id)->willReturn($post);
        
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');
        
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }
}
