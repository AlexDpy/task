<?php

namespace App\Infrastructure\Storage;

use App\Domain\Exception\ExchangeRateNotFoundException;
use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateReader;
use Doctrine\DBAL\Connection;

class ExchangeRateStorageReader implements ExchangeRateReader
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function get(Currency $baseCurrency, Currency $currency, \DateTimeInterface $date): ExchangeRate
    {
        $query = <<<SQL
SELECT *
FROM exchange_rates
WHERE base_currency = :base_currency
    AND currency = :currency
    AND rate_date = :rate_date
SQL;

        $result = $this->connection->fetchAssoc(
            $query,
            [
                'base_currency' => $baseCurrency->getSymbol(),
                'currency' => $currency->getSymbol(),
                'rate_date' => $date->format('Y-m-d'),
            ]
        );

        if (!$result) {
            throw new ExchangeRateNotFoundException();
        }

        return new ExchangeRate(
            $baseCurrency,
            $currency,
            $date,
            (float) $result['rate']
        );
    }
}
