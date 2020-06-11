<?php

namespace App\Infrastructure\Storage;

use App\Domain\Event\ExchangeRateHasChanged;
use App\Domain\Exception\ExchangeRateAlreadyExistsException;
use App\Domain\Exception\ExchangeRateNotFoundException;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateWriter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Swarrot\Broker\Message;
use Swarrot\SwarrotBundle\Broker\Publisher;

class ExchangeRateStorageWriter implements ExchangeRateWriter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ExchangeRateStorageReader
     */
    private $exchangeRateStorageReader;

    /**
     * @var Publisher
     */
    private $publisher;

    public function __construct(
        Connection $connection,
        ExchangeRateStorageReader $exchangeRateStorageReader,
        Publisher $publisher
    ) {
        $this->connection = $connection;
        $this->exchangeRateStorageReader = $exchangeRateStorageReader;
        $this->publisher = $publisher;
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

        try {
            $previousRate = $this->exchangeRateStorageReader->get(
                $exchangeRate->getBaseCurrency(),
                $exchangeRate->getCurrency(),
                (new \DateTimeImmutable($exchangeRate->getDate()->format('Y-m-d')))->modify('-1 day')
            );

            if ($previousRate->getRate() !== $exchangeRate->getRate()) {
                $event = new ExchangeRateHasChanged($previousRate, $exchangeRate);
                $this->publisher->publish(
                    'exchange_rate_has_changed',
                    new Message(json_encode($event)),
                    [
                        'routing_key' => 'exchange_rate_has_changed.'
                            . $exchangeRate->getBaseCurrency()->getSymbol()
                            . '.'
                            . $exchangeRate->getCurrency()->getSymbol()
                    ]
                );
            }
        } catch (ExchangeRateNotFoundException $e) {
            // Don't publish
        }
    }
}
