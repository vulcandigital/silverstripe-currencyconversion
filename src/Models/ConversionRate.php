<?php

namespace Vulcan\CurrencyConversion\Models;

use SilverStripe\ORM\DataObject;

/**
 * Class ConversionRate
 * @package Vulcan\CurrencyConversion\Models
 *
 * @property string Code The currency code. eg AUD, NZD, USD etc
 * @property double Dollar The dollar exchange rate. Multiply by this value to achieve the currency conversion required
 */
class ConversionRate extends DataObject
{
    private static $table_name = 'ConversionRate';

    private static $db = [
        'Code'   => 'Varchar(3)',
        'Dollar' => 'Float'
    ];

    /**
     * Add or update
     *
     * @param string $code
     * @param float  $dollarAmount
     *
     * @return DataObject|static
     */
    public static function addOrUpdate($code, $dollarAmount)
    {
        $record = static::get()->filter('Code', $code)->first();

        if (!$record) {
            $record = static::create();
            $record->Code = strtoupper($code);
        }

        $record->Dollar = $dollarAmount;
        $record->write();

        return $record;
    }

    /**
     * Fetch record by code
     *
     * @param string $code The currency code. eg AUD, NZD, USD etc
     *
     * @return DataObject|static
     */
    public static function getByCode($code)
    {
        return static::get()->filter('Code', $code)->first();
    }
}
