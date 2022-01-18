<?php

namespace Tests;

use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseTestCase extends TestCase
{
    /**
     * pdo
     *
     * @var PDO
     */
    protected $pdo;

    public function getPdo()
    {
        return new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ
        ]);
    }

    public function getManager(\PDO $pdo)
    {
        $configArray = require('phinx.php');
        $configArray['environments']['testing'] = [
            'adapter' => 'sqlite',
            'connection' => $pdo
        ];

        $config = new Config($configArray);
        return new Manager($config, new StringInput(' '), new NullOutput());
    }

    public function seedDatabase(\PDO $pdo): void
    {
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
        $this->getManager($pdo)->migrate('testing');
        $this->getManager($pdo)->seed('testing');
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    public function migrateDatabase(\PDO $pdo): void
    {
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
        $this->getManager($pdo)->migrate('testing');
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }
}
