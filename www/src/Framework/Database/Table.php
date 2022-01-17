<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;

class Table
{
    /**
     * pdo
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Nom de la table en BDD
     * @var string
     */
    protected $table;

    /**
     * Entité à utiliser
     * @var string|null
     */
    protected $entity;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Pagine les éléments
     *
     * @param int $perPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );

        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Requête sur la pagination.
     * @return string
     */
    protected function paginationQuery(): string
    {
        return "SELECT * FROM " . $this->table;
    }
    
    /**
     * Récupère un élément à partir de son id
     *
     * @param  integer $id
     * @return mixed
     */
    public function find(int $id)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id= ?");
        $query->execute([$id]);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
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

        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }
    
        
    /**
     * Ajoute un enregistrement
     *
     * @param  mixed $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(',', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(',', $fields);

        $statement = $this->pdo
        ->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $statement->execute($params);
    }
    
    /**
     * Supprime un enregistrement
     *
     * @param  int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");

        return $statement->execute([$id]);
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

    /**
     * Get the value of table
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get the value of entity
     * @return string\null
     */
    public function getEntity(): ?string
    {
        return $this->entity;
    }

    /**
     * Get the value of pdo
     */
    public function getPdo()
    {
        return $this->pdo;
    }
}
