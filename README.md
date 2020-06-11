# task-devops

Create a service to take care of currency exchange rates provided by the https://exchangeratesapi.io/.
Store data of currency rates and provide it via XML-RPC and REST API. Notify other services if exchange rates for USD to EUR is changed using Rabbit MQ.


## Goals

### Fetch and store currency rates

The command `bin/console app:import-exchange-rates [date]` will import rates from exchangeratesapi.io into the storage for a given date or the current day

It must be executed every day (cron job)


### Provide XML-RPC & REST API to provide exchange rates from X to Y currency for current day or given date

### Rest API

For a given date:
```bash
curl  http://admin:admin@localhost:8181/exchange-rates/EUR/USD/2020-06-01
# {"baseCurrency":"EUR","currency":"USD","date":"2020-06-01","rate":1.1116}
```

For the current day :
```bash
curl  http://admin:admin@localhost:8181/exchange-rates/EUR/USD
# {"baseCurrency":"EUR","currency":"USD","date":"2020-06-11","rate":1.1348}
```

### XML-RPC API

```php
$client = new \Laminas\XmlRpc\Client('http://admin:admin@localhost:8181/xml-rpc-api');

$result = $client->call('exchangeRate.get', [
    'baseCurrency' => 'EUR',
    'currency' => 'USD',
    'date' => '2020-06-10', // The date is optional. Default is the current day
]);

var_dump($result);
/*
array(4) {
  ["baseCurrency"]=>
  string(3) "EUR"
  ["currency"]=>
  string(3) "USD"
  ["date"]=>
  string(10) "2020-06-10"
  ["rate"]=>
  float(1.1375)
}
*/
```

### REST API and XML-RPC should be protected with Basic auth
The credentials are:
```
user: admin
password: admin
```

### Notify other services via Rabbit MQ that currency rate for USD-to-EUR changed (post a message in to the queue)

Each time an exchange rate changed, a message will be published in the topic exchange `exchange_rate.events` with the routing key `exchange_rate_has_changed.{base_currency}.{currency}`

So other services can bind a queue to this exchange with the routing_key they need (i.e: `exchange_rate_has_changed.EUR.USD`)


### Provide tests for given functionality

Tests can be executed with `bin/phpunit`
