<?php

namespace App\Controller\Rest;

use App\Domain\Exception\ExchangeRateNotFoundException;
use App\Domain\Model\Currency;
use App\Domain\Repository\ExchangeRateReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ExchangeRateController
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
     * @Route(
     *     "/exchange-rates/{baseCurrency}/{currency}/{date}",
     *     name="exchange_rate_get"
     * )
     */
    public function get($baseCurrency, $currency, $date = null)
    {
        try {
            $exchangeRate = $this->exchangeRateReader->get(
                new Currency($baseCurrency),
                new Currency($currency),
                new \DateTimeImmutable($date ?? 'now')
            );
        } catch (ExchangeRateNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        return new JsonResponse([
            'baseCurrency' => $exchangeRate->getBaseCurrency()->getSymbol(),
            'currency' => $exchangeRate->getCurrency()->getSymbol(),
            'date' => $exchangeRate->getDate()->format('Y-m-d'),
            'rate' => $exchangeRate->getRate(),
        ]);
    }
}
