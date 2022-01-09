<?php

namespace Framework;

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

    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->params)) {
                $this->addError($key, 'required');
            }
        }

        return $this;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    private function addError(string $key, string $rule) {
        $this->errors[$key] = new ValidatorError($key, $rule);
    }
}
