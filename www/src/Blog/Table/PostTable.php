<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Framework\Database\Table;
use Framework\Database\PaginatedQuery;

class PostTable extends Table
{

    /**
     * @var string
     */
    protected $entity = Post::class;

    /**
     * @var string
     */
    protected $table = 'posts';

    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM posts AS p 
            LEFT JOIN categories AS c ON c.id = p.category_id 
            ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM posts AS p 
            LEFT JOIN categories AS c ON c.id = p.category_id 
            WHERE p.category_id = :category
            ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM {$this->table} WHERE category_id = :category",
            $this->entity,
            ['category' => $categoryId]
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail('SELECT p.*, c.name category_name, c.slug category_slug
            FROM posts AS p
            LEFT JOIN categories AS c ON c.id = p.category_id
            WHERE p.id = ? 
        ', [$id]);
    }

    protected function paginationQuery(): string
    {
        return "SELECT p.id, p.name, c.name category_name
        FROM {$this->table} as p
        LEFT JOIN categories as c ON p.category_id = c.id
        ORDER BY created_at DESC";
    }
}
