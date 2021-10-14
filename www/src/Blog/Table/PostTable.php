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
     * @return Post|null
     */
    public function find(int $id): ?Post
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id= ?');
        $query->execute([$id]);
        $query->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $query->fetch() ?: null;
    }
    
    /**
     * Met à jour un enregistrement au niveu de la base de données
     *
     * @param  int $id
     * @param  string[] $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        
        $params['id'] = $id;

        $statement = $this->pdo->prepare("UPDATE posts SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }
    
        
    /**
     * insert
     *
     * @param  mixed $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = array_map(function ($field) {
            return ':' . $field;
        }, $fields);


        $statement = $this->pdo
        ->prepare("INSERT INTO posts (" . join(',', $fields) . ") VALUES (" . join(',', $values) . ")");
        return $statement->execute($params);
    }
    
    /**
     * buildFieldQuery
     *
     * @param  mixed $params
     * @return string
     */
    private function buildFieldQuery($params): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }
}
