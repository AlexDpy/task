<?php

namespace App\Tests\Application;

class RestApiTest extends ApplicationTest
{
    /**
     * @test
     */
    public function test()
    {
        // Arrange
        $this->connection->insert(
            'exchange_rates',
            [
                'base_currency' => 'EUR',
                'currency' => 'USD',
                'rate_date' => '2020-06-07',
                'rate' => 1.1818
            ]
        );

        // Act
        $this->client->request('GET', '/exchange-rates/EUR/USD/2020-06-07');
        $response = $this->client->getResponse();

        // Assert
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('{"baseCurrency":"EUR","currency":"USD","date":"2020-06-07","rate":1.1818}', $response->getContent());
    }
}
