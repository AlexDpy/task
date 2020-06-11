<?php

namespace App\Controller\XmlRpc;

use App\Domain\Model\Currency;
use App\Domain\Repository\ExchangeRateReader;

class XmlRpcApi
{
    /**
     * @var ExchangeRateReader
     */
    private $exchangeRateReader;

    public function __construct(ExchangeRateReader $exchangeRateReader)
    {
        $this->exchangeRateReader = $exchangeRateReader;
    }

    /**
     * Get the Exchange Rate from a currency to an other currency on a specific day
     *
     * @param string $baseCurrency
     * @param string $currency
     * @param string $date
     *
     * @return array
     */
    public function get($baseCurrency, $currency, $date = null)
    {
        $exchangeRate = $this->exchangeRateReader->get(
            new Currency($baseCurrency),
            new Currency($currency),
            new \DateTimeImmutable($date ?? 'now')
        );

        return [
            'baseCurrency' => $exchangeRate->getBaseCurrency()->getSymbol(),
            'currency' => $exchangeRate->getCurrency()->getSymbol(),
            'date' => $exchangeRate->getDate()->format('Y-m-d'),
            'rate' => $exchangeRate->getRate(),
        ];
    }
}
