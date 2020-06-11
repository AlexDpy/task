<?php

namespace App\Tests\Application;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApplicationTest extends WebTestCase
{
    private const CREATE_TABLE = <<<SQL
CREATE TABLE IF NOT EXISTS exchange_rates (
    base_currency CHAR(3) NOT NULL,
    currency CHAR(3) NOT NULL,
    rate_date DATE NOT NULL,
    rate DOUBLE NOT NULL,
    PRIMARY KEY (base_currency, currency, rate_date)
)
SQL;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected $client;
    /**
     * @var Connection
     */
    protected $connection;

    protected function setUp()
    {
        $this->client = self::createClient([], [
            'HTTP_AUTHORIZATION' => 'Basic ' . base64_encode('admin:admin'),
        ]);
        $this->connection = static::$container->get('doctrine.dbal.default_connection');
        $this->connection->exec(self::CREATE_TABLE);
    }
}
