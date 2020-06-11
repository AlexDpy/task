<?php

namespace App\Tests\Application;

class XmlRpcTest extends ApplicationTest
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

        $this->markTestSkipped('TODO implement this test');
    }
}
