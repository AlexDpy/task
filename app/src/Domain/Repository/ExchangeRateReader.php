<?php

namespace App\Domain\Repository;

use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;

interface ExchangeRateReader
{
    public function get(Currency $baseCurrency, Currency $currency, \DateTimeInterface $date): ExchangeRate;
}
