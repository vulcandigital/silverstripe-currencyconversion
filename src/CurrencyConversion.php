<?php

namespace Vulcan\CurrencyConversion;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extensible;
use SilverStripe\Core\Injector\Injectable;
use Vulcan\CurrencyConversion\Models\ConversionRate;

/**
 * Class CurrencyConversion
 * @package Vulcan\CurrencyConversion
 *
 * A free currency conversion module for SilverStripe. This modules takes advantage of https://currencylayer.com/
 * by allowing you to use the free membership, to convert currency from any source to another using simple mathematics
 */
class CurrencyConversion
{
    use Injectable, Configurable, Extensible;

    /**
     * @config
     * @var string The default currency used when converting to another currency
     */
    private static $base_currency = 'NZD';

    /**
     * @config
     * @var string Get yours free at https://currencylayer.com/
     */
    private static $api_key = false;

    /**
     * @config
     * @var string
     */
    private static $endpoint = 'http://www.apilayer.net/api/live';

    /**
     * @config
     * @var string How often exchange rates are updated
     */
    private static $cron_schedule = '*/5 * * * *';

    /**
     * @param float       $amount
     * @param string      $to
     * @param string|null $from
     *
     * @return float|int
     */
    public static function convert($amount, $to, $from = null)
    {
        if (!$from) {
            $from = static::config()->get('base_currency');
        }

        $toObj = ConversionRate::getByCode($to);
        $fromObj = ConversionRate::getByCode($from);

        if (!$toObj) {
            throw new \InvalidArgumentException("$to was not found to be a valid currency, or no data has been synced for it");
        }

        if (!$fromObj) {
            throw new \InvalidArgumentException("$from was not found to be a valid currency, or no data has been synced for it");
        }

        return $amount * ($fromObj->Dollar / $toObj->Dollar);
    }

    /**
     * @return array|bool
     */
    public static function getRates()
    {
        $guzzle = new \GuzzleHttp\Client();
        $response = $guzzle->get(static::getEndpoint(), [
            'query' => [
                'access_key' => static::getApiKey(),
                'format'     => 1
            ]
        ]);

        $body = (string)$response->getBody();
        $data = \GuzzleHttp\json_decode($body, true);

        return (isset($data['quotes'])) ? $data['quotes'] : false;
    }

    /**
     * @return string
     */
    public static function getEndpoint()
    {
        return static::config()->get('endpoint');
    }

    /**
     * @return mixed
     */
    public static function getApiKey()
    {
        $key = static::config()->get('api_key');

        if (!$key) {
            throw new \RuntimeException('api_key must be defined.');
        }

        return $key;
    }

    /**
     * @return string
     */
    public function getBaseCurrency()
    {
        $currency = $this->config()->get('base_currency');

        $this->extend('updateBaseCurrency', $currency);

        return $currency;
    }
}