<?php

namespace Test\Blog\Table;

use PDO;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    private $postTable;

    public function setUp(): void
    {
        parent::setUp();
        $this->postTable = new PostTable($this->pdo);
    }

    public function testFind()
    {
        $this->seedDatabase();
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord()
    {
        $post = $this->postTable->find(1000000);
        $this->assertNull($post);
    }

    public function testUpdateField()
    {
        $this->seedDatabase();
        $this->postTable->update(1, ['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testInsert()
    {
        $this->postTable->insert([
            'name' => 'Salut',
            'slug' => 'demo',
            'content' => 'salut',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $post = $this->postTable->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testDelete()
    {
        $this->postTable->insert([
            'name' => 'Salut',
            'slug' => 'demo',
            'content' => 'salut',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $this->postTable->insert([
            'name' => 'Salut',
            'slug' => 'demo',
            'content' => 'salut',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $count = $this->pdo->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($this->pdo->lastInsertId());
        $count = $this->pdo->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }
}
