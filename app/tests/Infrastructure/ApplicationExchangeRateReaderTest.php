<?php

namespace App\Tests\Infrastructure;

use App\Domain\Exception\ExchangeRateNotFoundException;
use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateReader;
use App\Infrastructure\ApplicationExchangeRateReader;
use PHPUnit\Framework\TestCase;

class ApplicationExchangeRateReaderTest extends TestCase
{
    /**
     * @var ApplicationExchangeRateReader
     */
    private $SUT;

    private $exchangeRateStorageReader;
    private $exchangeRatesApiClient;

    protected function setUp()
    {
        $this->exchangeRateStorageReader = $this->prophesize(ExchangeRateReader::class);
        $this->exchangeRatesApiClient = $this->prophesize(ExchangeRateReader::class);

        $this->SUT = new ApplicationExchangeRateReader(
            $this->exchangeRateStorageReader->reveal(),
            $this->exchangeRatesApiClient->reveal()
        );
    }

    /**
     * @test
     */
    public function itGetsTheExchangeRateFromTheStorage()
    {
        // Arrange
        $baseCurrency = new Currency('EUR');
        $currency = new Currency('USD');
        $date = new \DateTimeImmutable('2020-06-10');
        $rate = 1.18;

        $expectedResult = new ExchangeRate($baseCurrency, $currency, $date, $rate);

        $this->exchangeRateStorageReader->get($baseCurrency, $currency, $date)->willReturn($expectedResult);

        // Act
        $result = $this->SUT->get($baseCurrency, $currency, $date);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itGetsTheExchangeRateFromTheApiIfNotFoundInTheStorage()
    {
        // Arrange
        $baseCurrency = new Currency('EUR');
        $currency = new Currency('USD');
        $date = new \DateTimeImmutable('2020-06-10');
        $rate = 1.18;

        $expectedResult = new ExchangeRate($baseCurrency, $currency, $date, $rate);

        $this->exchangeRateStorageReader->get($baseCurrency, $currency, $date)->willThrow(ExchangeRateNotFoundException::class);
        $this->exchangeRatesApiClient->get($baseCurrency, $currency, $date)->willReturn($expectedResult);

        // Act
        $result = $this->SUT->get($baseCurrency, $currency, $date);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function itThrowsANotFoundException()
    {
        // Arrange
        $baseCurrency = new Currency('EUR');
        $currency = new Currency('USD');
        $date = new \DateTimeImmutable('2020-06-10');

        $this->exchangeRateStorageReader->get($baseCurrency, $currency, $date)->willThrow(ExchangeRateNotFoundException::class);
        $this->exchangeRatesApiClient->get($baseCurrency, $currency, $date)->willThrow(ExchangeRateNotFoundException::class);

        // Assert
        $this->expectException(ExchangeRateNotFoundException::class);

        // Act
        $this->SUT->get($baseCurrency, $currency, $date);
    }
}
