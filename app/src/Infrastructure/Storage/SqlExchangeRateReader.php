<?php

namespace App\Infrastructure\Storage;

use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateReader;

class SqlExchangeRateReader implements ExchangeRateReader
{
    public function get(Currency $baseCurrency, Currency $currency, \DateTimeInterface $date): ExchangeRate
    {
        return new ExchangeRate();
    }
}
