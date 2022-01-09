<?php

namespace Framework\Validator;

class ValidatorError
{

    private $key;
    private $rule;

    private $messages = [
        'required' => 'Le champs %s est requis'
    ];

    public function __construct(string $key, string $rule)
    {
        $this->key = $key;
        $this->rule = $rule;
    }

    public function __toString()
    {
        return sprintf($this->messages[$this->rule], $this->key);
    }
}
