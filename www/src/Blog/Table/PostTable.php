<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Framework\Database\PaginatedQuery;

class PostTable
{
    /**
     * pdo
     *
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Pagine les articles
     *
     * @param int $perPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT * FROM posts",
            "SELECT COUNT(id) FROM posts ORDER BY created_at DESC",
            Post::class
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }
    
    /**
     * Récupère un article à partir de son id
     *
     * @param  integer $id
     * @return Post
     */
    public function find(int $id): Post
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id= ?');
        $query->execute([$id]);
        $query->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $query->fetch();
    }
}
