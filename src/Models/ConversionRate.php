<?php

namespace Vulcan\CurrencyConversion\Models;

use SilverStripe\ORM\DataObject;

/**
 * Class ConversionRate
 * @package Vulcan\CurrencyConversion\Models
 *
 * @property string Code
 * @property double Dollar
 */
class ConversionRate extends DataObject
{
    private static $table_name = 'ConversionRate';

    private static $db = [
        'Code'   => 'Varchar(3)',
        'Dollar' => 'Decimal'
    ];

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
     * @param $code
     *
     * @return DataObject|static
     */
    public static function getByCode($code)
    {
        return static::get()->filter('Code', $code)->first();
    }
}

?>