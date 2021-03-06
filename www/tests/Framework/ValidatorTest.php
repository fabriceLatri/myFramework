<?php

namespace Tests\Framework;

use Framework\Validator;
use Tests\DatabaseTestCase;

class ValidatorTest extends DatabaseTestCase
{
    private function makeValidator(array $params)
    {
        return new Validator($params);
    }

    public function testRequiredIfFail()
    {
        $errors = $this->makeValidator([
            'name' => 'joe'
        ])
            ->required('name', 'content')
            ->getErrors();

            $this->assertCount(1, $errors);
            $this->assertEquals("Le champs content est requis", (string)$errors['content']);
    }

    public function testNotEmpty()
    {
        $errors = $this->makeValidator([
            'name' => 'joe',
            'content' => ''
        ])
            ->notEmpty('content')
            ->getErrors();

            $this->assertCount(1, $errors);
            $this->assertEquals("Le champs content ne peut être vide", (string)$errors['content']);
    }

    public function testRequiredIfSuccess()
    {
        $errors = $this->makeValidator([
            'name' => 'joe',
            'content' => 'content'
        ])
            ->required('name', 'content')
            ->getErrors();

            $this->assertCount(0, $errors);
    }

    public function testSlugSuccess()
    {
        $errors = $this->makeValidator([
            'slug' => 'aze-aze-azeaze34',
            'slug' => 'azeaze'
        ])
            ->slug('slug')
            ->slug('slug2')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testSlugError()
    {
        $errors = $this->makeValidator([
            'slug' => 'aze-aze-azeAze34',
            'slug2' => 'aze-aze_azeaze34',
            'slug4' => 'aze-azeaze-',
            'slug3' => 'aze--aze-aze'
            
        ])
        ->slug('slug')
        ->slug('slug2')
        ->slug('slug3')
        ->slug('slug4')
        ->getErrors();

        $this->assertEquals(['slug', 'slug2', 'slug3', 'slug4'], array_keys($errors));
    }

    public function testLength()
    {
        $params = ['slug' => '123456789'];

        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());
    }

    public function testDateTime()
    {
        $params = ['date' => '2012-12-12 11:12:13'];

        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:13'])->dateTime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-29'])->dateTime('date')->getErrors());
    }

    public function testExists()
    {
        $pdo = $this->getPdo();
        $pdo->exec(
            'CREATE TABLE test (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) 
            )'
        );
        $pdo->exec('INSERT INTO test (name) VALUES ("a1");');
        $pdo->exec('INSERT INTO test (name) VALUES ("a2");');
        $this->assertTrue($this->makeValidator(['category' => '1'])->exists('category', 'test', $pdo)->isValid());
        $this->assertFalse(
            $this->makeValidator(['category' => '1234567'])
                ->exists('category', 'test', $pdo)
                ->isValid()
        );
    }

    public function testUnique()
    {
        $pdo = $this->getPdo();
        $pdo->exec(
            'CREATE TABLE test (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) 
            )'
        );
        $pdo->exec('INSERT INTO test (name) VALUES ("a1");');
        $pdo->exec('INSERT INTO test (name) VALUES ("a2");');
        $this->assertFalse($this->makeValidator(['name' => 'a1'])->unique('name', 'test', $pdo)->isValid());
        $this->assertTrue($this->makeValidator(['name' => 'a111'])->unique('name', 'test', $pdo)->isValid());
        $this->assertTrue($this->makeValidator(['name' => 'a1'])->unique('name', 'test', $pdo, 1)->isValid());
        $this->assertFalse($this->makeValidator(['name' => 'a2'])->unique('name', 'test', $pdo)->isValid());
    }
}
