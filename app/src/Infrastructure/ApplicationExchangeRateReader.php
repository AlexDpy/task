<?php

namespace App\Infrastructure;

use App\Domain\Exception\ExchangeRateNotFoundException;
use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateReader;

class ApplicationExchangeRateReader implements ExchangeRateReader
{
    /**
     * @var ExchangeRateReader
     */
    private $exchangeRateStorageReader;

    /**
     * @var ExchangeRateReader
     */
    private $exchangeRatesApiClient;

    public function __construct(
        ExchangeRateReader $exchangeRateStorageReader,
        ExchangeRateReader $exchangeRatesApiClient
    ){
        $this->exchangeRateStorageReader = $exchangeRateStorageReader;
        $this->exchangeRatesApiClient = $exchangeRatesApiClient;
    }

    public function get(Currency $baseCurrency, Currency $currency, \DateTimeInterface $date): ExchangeRate
    {
        try {
            return $this->exchangeRateStorageReader->get($baseCurrency, $currency, $date);
        } catch (ExchangeRateNotFoundException $e) {
            return $this->exchangeRatesApiClient->get($baseCurrency, $currency, $date);
        }
    }
}
