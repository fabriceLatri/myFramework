<?php

namespace Test\Blog\Table;

use PDO;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Framework\Database\NoRecordException;
use Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    private $postTable;

    public function setUp(): void
    {
        parent::setUp();
        $pdo = $this->getPdo();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }

    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord()
    {
        $this->expectException(NoRecordException::class);
        $this->postTable->find(1);
    }

    public function testUpdateField()
    {
        $this->seedDatabase($this->postTable->getPdo());
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
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }
}
