<?php

namespace App\Controller\XmlRpc;

use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;

class XmlRpcApi
{
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
        $exchangeRate = new ExchangeRate(
            new Currency('EUR'),
            new Currency('USD'),
            new \DateTimeImmutable(),
            1.18
        );

        return [
            'baseCurrency' => $exchangeRate->getBaseCurrency()->getSymbol(),
            'currency' => $exchangeRate->getCurrency()->getSymbol(),
            'date' => $exchangeRate->getDate()->format('Y-m-d'),
            'rate' => $exchangeRate->getRate(),
        ];
    }
}
