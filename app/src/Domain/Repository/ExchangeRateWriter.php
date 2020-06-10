<?php

namespace App\Domain\Repository;

use App\Domain\Model\ExchangeRate;

interface ExchangeRateWriter
{
    public function store(ExchangeRate $exchangeRate): void;
}
