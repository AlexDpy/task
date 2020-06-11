<?php

namespace App\Tests\Infrastructure\ExchangeRatesApi;

use App\Domain\Exception\ExchangeRateNotFoundException;
use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;
use App\Infrastructure\ExchangeRatesApi\ExchangeRatesApiClient;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRatesApiClientTest extends TestCase
{
    /**
     * @var ExchangeRatesApiClient
     */
    private $SUT;

    private $httpClient;

    protected function setUp()
    {
        $this->httpClient = $this->prophesize(HttpClientInterface::class);

        $this->SUT = new ExchangeRatesApiClient($this->httpClient->reveal());
    }

    /**
     * @test
     */
    public function itGetsTheRateFromTheApi()
    {
        // Arrange
        $baseCurrency = new Currency('EUR');
        $currency = new Currency('USD');
        $date = new \DateTimeImmutable('2020-06-02');

        $url = 'https://api.exchangeratesapi.io/2020-06-02?base=EUR';

        $response = $this->prophesize(ResponseInterface::class);
        $response->getContent()->willReturn(json_encode([
            'rates' => ['USD' => 1.1174],
            'base' => 'EUR',
            'date' => '2020-06-02',
        ]));

        $this->httpClient->request('GET', $url, Argument::any())->willReturn($response->reveal());
        $expectedResult = new ExchangeRate($baseCurrency, $currency, $date, 1.1174);

        // Act
        $result = $this->SUT->get($baseCurrency, $currency, $date);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itThrowsANotFoundExceptionIfTheApiCallHasFailed()
    {
        // Arrange
        $baseCurrency = new Currency('EUR');
        $currency = new Currency('USD');
        $date = new \DateTimeImmutable('2020-06-02');

        $url = 'https://api.exchangeratesapi.io/2020-06-02?base=EUR';

        $response = $this->prophesize(ResponseInterface::class);
        $response->getContent()->willThrow($this->prophesize(HttpExceptionInterface::class)->reveal());

        $this->httpClient->request('GET', $url, Argument::any())->willReturn($response->reveal());

        // Assert
        $this->expectException(ExchangeRateNotFoundException::class);

        // Act
        $this->SUT->get($baseCurrency, $currency, $date);
    }

    /**
     * @test
     */
    public function itThrowsANotFoundExceptionIfTheDateIsNotTheSame()
    {
        // Arrange
        $baseCurrency = new Currency('EUR');
        $currency = new Currency('USD');
        $date = new \DateTimeImmutable('2020-06-02');

        $url = 'https://api.exchangeratesapi.io/2020-06-02?base=EUR';

        $response = $this->prophesize(ResponseInterface::class);
        $response->getContent()->willReturn(json_encode([
            'rates' => ['USD' => 1.1174],
            'base' => 'EUR',
            'date' => '2020-06-03',
        ]));

        $this->httpClient->request('GET', $url, Argument::any())->willReturn($response->reveal());

        // Assert
        $this->expectException(ExchangeRateNotFoundException::class);

        // Act
        $this->SUT->get($baseCurrency, $currency, $date);
    }
}

