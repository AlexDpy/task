<?php

namespace App\Controller\Rest;

use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExchangeRateController
{
//    /**
//     * @var ExchangeRateReader
//     */
//    private $exchangeRateReader;
//
//    public function __construct(ExchangeRateReader $exchangeRateReader)
//    {
//        $this->exchangeRateReader = $exchangeRateReader;
//    }

    /**
     * @Route(
     *     "/exchange-rates/{baseCurrency}/{currency}/{date}",
     *     name="exchange_rate_get"
     * )
     */
    public function get($baseCurrency, $currency, $date = null)
    {
//        $exchangeRate = $this->exchangeRateReader->get(
//            new Currency($baseCurrency),
//            new Currency($currency),
//            new \DateTimeImmutable($date ?? 'now')
//        );

        $exchangeRate = new ExchangeRate(
            new Currency('EUR'),
            new Currency('USD'),
            new \DateTimeImmutable(),
            1.18
        );

        return new JsonResponse([
            'baseCurrency' => $exchangeRate->getBaseCurrency()->getSymbol(),
            'currency' => $exchangeRate->getCurrency()->getSymbol(),
            'date' => $exchangeRate->getDate()->format('Y-m-d'),
            'rate' => $exchangeRate->getRate(),
        ]);

        return new Response(sprintf('Asking exchange rate from %s to %s on day %s', $baseCurrency, $currency, $date));
    }
}
