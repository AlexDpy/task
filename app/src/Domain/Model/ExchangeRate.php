<?php

namespace App\Domain\Model;

class ExchangeRate
{
    /**
     * @var Currency
     */
    private $baseCurrency;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var \DateTimeInterface
     */
    private $date;

    /**
     * @var float
     */
    private $rate;

    public function __construct(Currency $baseCurrency, Currency $currency, \DateTimeInterface $date, float $rate)
    {
        $this->baseCurrency = $baseCurrency;
        $this->currency = $currency;
        $this->date = $date;
        $this->rate = $rate;
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}
