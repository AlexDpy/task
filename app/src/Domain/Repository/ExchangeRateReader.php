<?php

namespace App\Domain\Repository;

use App\Domain\Exception\ExchangeRateNotFoundException;
use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;

interface ExchangeRateReader
{
    /**
     * @param Currency $baseCurrency
     * @param Currency $currency
     * @param \DateTimeInterface $date
     *
     * @return ExchangeRate
     *
     * @throws ExchangeRateNotFoundException
     */
    public function get(Currency $baseCurrency, Currency $currency, \DateTimeInterface $date): ExchangeRate;
}
