<?php

namespace Vulcan\CurrencyConversion\Tasks;

use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\CronTask\Interfaces\CronTask;
use SilverStripe\Dev\BuildTask;
use Vulcan\CurrencyConversion\CurrencyConversion;
use Vulcan\CurrencyConversion\Models\ConversionRate;

/**
 * Class SyncRatesTask
 * @package Vulcan\CurrencyConversion\Tasks
 */
class SyncRatesTask extends BuildTask implements CronTask
{
    protected $title = 'Sync currency exchange rates';

    protected $description = 'Get the latest exchange rates from the API provided by currencylayer.com';

    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     *
     * @param HTTPRequest $request
     *
     * @return void
     */
    public function run($request)
    {
        $this->execute();
    }

    /**
     * Return a string for a CRON expression
     *
     * @return string
     */
    public function getSchedule()
    {
        return CurrencyConversion::config()->get('cron_schedule');
    }

    /**
     * When this script is supposed to run the CronTaskController will execute
     * process().
     *
     * @return void
     */
    public function process()
    {
        $this->execute();
    }

    private function execute()
    {
        $rates = CurrencyConversion::getRates();
        $baseCurrencyRate = $this->findBaseCurrencyRate($rates, $baseCurrency = CurrencyConversion::config()->get('base_currency'));

        if (!$baseCurrencyRate) {
            throw new \RuntimeException("base_currency $baseCurrency not found in rates");
        }

        foreach ($rates as $code => $rate) {
            $source = substr($code, 0, 3);
            $target = str_replace($source, '', $code);

            $convertedRate = $rate / $baseCurrencyRate;

            if ($code == 'USDUSD') {
                $target = 'USD';
            }

            ConversionRate::addOrUpdate($target, $convertedRate);
            $results[$target] = $convertedRate;
            echo sprintf("1 %s = %s %s", $baseCurrency, $convertedRate, $target) . ((Director::is_cli()) ? PHP_EOL : '<br/>');
        }
    }

    /**
     * Finds the base currency in the array of rates
     *
     * @param array $rates
     * @param string $baseCurrency
     *
     * @return float|bool
     */
    private function findBaseCurrencyRate($rates, $baseCurrency)
    {
        return isset($rates['USD' . $baseCurrency]) ? $rates['USD' . $baseCurrency] : false;
    }
}