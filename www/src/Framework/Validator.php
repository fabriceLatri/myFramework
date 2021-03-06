<?php

namespace Framework;

use Framework\Database\Table;
use Framework\Validator\ValidatorError;

class Validator
{
    /**
     * @var array
     */
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @var string[]
     */
    private $errors = [];

    /**
     * Vérifie que les champs sont présents dans le tableau
     * @params string[] ...$keys
     * @return Validator
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            if (is_null($this->getValue($key))) {
                $this->addError($key, 'required');
            }
        }

        return $this;
    }

    /**
     * Vérifie que le champs n'est pas vide
     * @param string[] keys
     * @return Validator
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            if (is_null($this->getValue($key)) || empty($this->getValue($key))) {
                $this->addError($key, 'empty');
            }
        }

        return $this;
    }

    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'between', [$min, $max]);
            return $this;
        }
        if (!is_null($min) &&
            $length < $min
        ) {
            $this->addError($key, 'minlength', [$min]);
            return $this;
        }
        if (!is_null($max) &&
            $length > $max
        ) {
            $this->addError($key, 'maxlength', [$max]);
            return $this;
        }
        return $this;
    }

    /**
     * Vérifie que l'élément est un slug
     * @param string $key
     * @return Validator
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    /**
     * Vérifie que la clé est un dateTime
     * @param string $key
     * @param string $format
     * @return self
     */
    public function dateTime(string $key, string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || !$date) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }

    /**
     * Vérifie que la clef existe dans la table donnée
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @return self
     */
    public function exists(string $key, string $table, \PDO $pdo): self
    {
        
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM {$table} WHERE id = ?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }

        return $this;
    }

    /**
     * Vérifie que la clé est unique dans la base de données.
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @param int|null $exclude
     * @return self
     */
    public function unique(string $key, string $table, \PDO $pdo, ?int $exclude = null): self
    {
        $value = $this->getValue($key);
        $query = "SELECT id FROM {$table} WHERE $key = ?";
        $params = [$value];
        if (!is_null($exclude)) {
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }

        return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Récupère les erreurs
     * @return ValidatorError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Ajoute une erreur
     * @param string $key
     * @param string $rule
     * @param array $attributes
     * @return void
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidatorError($key, $rule, $attributes);
    }

    private function getValue(string $key)
    {
        return array_key_exists($key, $this->params) ? $this->params[$key] : null;
    }
}
