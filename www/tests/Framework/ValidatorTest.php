<?php

namespace Tests\Framework;

use Framework\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testRequired()
    {
        $errors = (new Validator([
            'name' => 'joe'
        ]))
            ->required('name', 'content')
            ->getErrors();

            $this->assertCount(1, $errors);
            $this->assertEquals("Le champs content est requis", (string)$errors['content']);
    }
}
