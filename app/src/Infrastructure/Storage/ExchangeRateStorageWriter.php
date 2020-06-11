<?php

namespace App\Infrastructure\Storage;

use App\Domain\Exception\ExchangeRateAlreadyExistsException;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateWriter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class ExchangeRateStorageWriter implements ExchangeRateWriter
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function store(ExchangeRate $exchangeRate): void
    {
        try {
            $this->connection->insert(
                'exchange_rates',
                [
                    'base_currency' => $exchangeRate->getBaseCurrency()->getSymbol(),
                    'currency' => $exchangeRate->getCurrency()->getSymbol(),
                    'rate_date' => $exchangeRate->getDate()->format('Y-m-d'),
                    'rate' => $exchangeRate->getRate()
                ]
            );
        } catch (UniqueConstraintViolationException $e) {
            throw new ExchangeRateAlreadyExistsException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
