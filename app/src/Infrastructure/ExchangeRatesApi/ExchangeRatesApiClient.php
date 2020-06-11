<?php

namespace App\Infrastructure\ExchangeRatesApi;

use App\Domain\Exception\ExchangeRateNotFoundException;
use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateReader;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRatesApiClient implements ExchangeRateReader
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function get(Currency $baseCurrency, Currency $currency, \DateTimeInterface $date): ExchangeRate
    {
        $rates = $this->getAll($baseCurrency, $date)['rates'];

        // TODO check exists $content[$currency->getSymbol()]

        return new ExchangeRate(
            $baseCurrency,
            $currency,
            $date,
            $rates[$currency->getSymbol()]
        );
    }

    /**
     * @param Currency $baseCurrency
     * @param \DateTimeInterface $date
     *
     * @return array The result from exchangeratesapi.io
     */
    public function getAll(Currency $baseCurrency, \DateTimeInterface $date): array
    {
        $url = sprintf(
            'https://api.exchangeratesapi.io/%s?base=%s',
            $date->format('Y-m-d'),
            $baseCurrency->getSymbol()
        );

        $response = $this->httpClient->request('GET', $url);

        try {
            $content = json_decode($response->getContent(), true);

            if ($date->format('Y-m-d') !== $content['date']) {
                throw new ExchangeRateNotFoundException(sprintf('Exchange rate for date %s has not been found on exchangeratesapi.io', $date->format('Y-m-d')));
            }

            return $content;
        } catch (HttpExceptionInterface $e) {
            throw new ExchangeRateNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
