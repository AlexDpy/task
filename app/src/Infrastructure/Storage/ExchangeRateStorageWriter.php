<?php

namespace App\Infrastructure\Storage;

use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateWriter;

class ExchangeRateStorageWriter implements ExchangeRateWriter
{
    public function store(ExchangeRate $exchangeRate): void
    {
        // TODO: Implement store() method.
    }
}
