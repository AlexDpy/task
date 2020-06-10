<?php

namespace App\Domain\Model;

class Currency
{
    private const SUPPORTED_SYMBOLS = [
        'CAD',
        'HKD',
        'ISK',
        'PHP',
        'DKK',
        'HUF',
        'CZK',
        'AUD',
        'RON',
        'SEK',
        'IDR',
        'INR',
        'BRL',
        'RUB',
        'HRK',
        'JPY',
        'THB',
        'CHF',
        'SGD',
        'PLN',
        'BGN',
        'TRY',
        'CNY',
        'NOK',
        'NZD',
        'ZAR',
        'USD',
        'MXN',
        'ILS',
        'GBP',
        'KRW',
        'MYR',
        'EUR',
    ];

    /**
     * @var string
     */
    private $symbol;

    public function __construct(string $symbol)
    {
        if (!in_array($symbol, self::SUPPORTED_SYMBOLS)) {
            throw new \DomainException(sprintf('Symbol %s is not supported', $symbol));
        }

        $this->symbol = $symbol;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }
}
