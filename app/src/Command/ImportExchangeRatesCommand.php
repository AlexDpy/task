<?php

namespace App\Command;

use App\Domain\Exception\ExchangeRateAlreadyExistsException;
use App\Domain\Model\Currency;
use App\Domain\Model\ExchangeRate;
use App\Domain\Repository\ExchangeRateWriter;
use App\Infrastructure\ExchangeRatesApi\ExchangeRatesApiClient;
use App\Infrastructure\Storage\ExchangeRateStorageWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportExchangeRatesCommand extends Command
{
    protected static $defaultName = 'app:import-exchange-rates';

    /**
     * @var ExchangeRatesApiClient
     */
    private $exchangeRatesApiClient;

    /**
     * @var ExchangeRateStorageWriter
     */
    private $exchangeRateWriter;

    public function __construct(
        ExchangeRatesApiClient $exchangeRatesApiClient,
        ExchangeRateWriter $exchangeRateWriter
    ) {
        parent::__construct();
        $this->exchangeRatesApiClient = $exchangeRatesApiClient;
        $this->exchangeRateWriter = $exchangeRateWriter;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import rates from exchangeratesapi.io into the storage')
            ->addArgument('date', InputArgument::OPTIONAL, 'The date at Y-m-d format. Default to today.', date('Y-m-d'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $date = new \DateTimeImmutable($input->getArgument('date'));

        foreach (Currency::SUPPORTED_SYMBOLS as $baseCurrencySymbol) {
            $baseCurrency = new Currency($baseCurrencySymbol);

            $io->writeln(sprintf('Querying exchangeratesapi.io for currency %s', $baseCurrencySymbol));
            $rates = $this->exchangeRatesApiClient->getAll($baseCurrency, $date)['rates'];

            foreach ($rates as $currencySymbol => $rate) {
                $io->writeln(sprintf(
                    'Storing rate from %s to %s on %s. Rate is %s',
                    $baseCurrencySymbol,
                    $currencySymbol,
                    $date->format('Y-m-d'),
                    $rate
                ));

                try {
                    $this->exchangeRateWriter->store(new ExchangeRate(
                        $baseCurrency,
                        new Currency($currencySymbol),
                        $date,
                        (float) $rate
                    ));
                } catch (ExchangeRateAlreadyExistsException $e) {
                    $io->writeln('Exchange rate already exists');
                }
            }
        }

        $io->success('Bye bye :-)');

        return 0;
    }
}
