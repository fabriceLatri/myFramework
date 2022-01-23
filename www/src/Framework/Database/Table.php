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
    protected $pdo;

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
     * Récupère une liste clef valeur de nos enregistrements
     * @return string[]
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(\PDO::FETCH_NUM);

        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }

        return $list;
    }

    /**
     * Récupère tous nos enregistrements
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->pdo
            ->query("SELECT * FROM {$this->table}");

        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(\PDO::FETCH_OBJ);
        }

        return $query->fetchAll();
    }

    /**
     * Récupère une ligne par rapport à un champ
     * @param string $field
     * @param string $value
     * @return mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
    }

    /**
     * Récupère un élément à partir de son id
     *
     * @param  integer $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id= ?", [$id]);
    }

    /**
     * Récupère le nombre d'enregistrement
     * @return int
     */
    public function count(): int
    {
        return $this->fetchColumn("SELECT COUNT(id) FROM {$this->table};");
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

        $query = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $query->execute($params);
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

        $query = $this->pdo
        ->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $query->execute($params);
    }
    
    /**
     * Supprime un enregistrement
     *
     * @param  int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");

        return $query->execute([$id]);
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
     * Vérifie qu'un enregistrement existe
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
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

    /**
     * Exécute une requête et de récuperer le premier résultat
     * @param string $query
     * @param array $params
     * @throws NoRecordException
     * @return mixed
     */
    protected function fetchOrFail(string $query, array $params = []): mixed
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }

        $record = $query->fetch();

        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * Récupère la première colonne.
     * @param string $query
     * @param array $params
     * @return mixed
     */
    private function fetchColumn(string $query, array $params = []): mixed
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }

        return $query->fetchColumn();
    }
}
