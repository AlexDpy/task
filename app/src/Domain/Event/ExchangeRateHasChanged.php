<?php

namespace App\Domain\Event;

use App\Domain\Model\ExchangeRate;

class ExchangeRateHasChanged implements \JsonSerializable
{
    /**
     * @var ExchangeRate
     */
    private $previous;

    /**
     * @var ExchangeRate
     */
    private $new;

    public function __construct(
        ExchangeRate $previous,
        ExchangeRate $new
    ) {
        $this->previous = $previous;
        $this->new = $new;
    }

    public function jsonSerialize()
    {
        return [
            'previous' => [
                'baseCurrency' => $this->previous->getBaseCurrency()->getSymbol(),
                'currency' => $this->previous->getCurrency()->getSymbol(),
                'date' => $this->previous->getDate()->format('Y-m-d'),
                'rate' => $this->previous->getRate()
            ],
            'new' => [
                'baseCurrency' => $this->new->getBaseCurrency()->getSymbol(),
                'currency' => $this->new->getCurrency()->getSymbol(),
                'date' => $this->new->getDate()->format('Y-m-d'),
                'rate' => $this->new->getRate()
            ]
        ];
    }
}
