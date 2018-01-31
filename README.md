## silverstripe-currencyconversion

A free currency conversion module for SilverStripe. This modules takes advantage of [https://currencylayer.com/](https://currencylayer.com/) by allowing you to use the free membership, to convert currency from any source to another using simple mathematics

## Installation
```bash
composer require vulcandigital/silverstripe-currencyconversion
```

1. After installing you should immediately define your `api_key` and `base_currency` as shown in the configuration below
2. Run `dev/build`
3. Run `dev/tasks/Vulcan-CurrencyConversion-Tasks-SyncRatesTask`

## Keeping up to date
This module requires [silverstripe/crontask](https://github.com/silverstripe/silverstripe-crontask), please ensure you have followed the configuration [instructions](https://github.com/silverstripe/silverstripe-crontask#server-configuration) if you wish to have exchange rates updated periodically

By default, exchange rates are scheduled to update every 5 minutes: `*/5 * * * *`, if you would like to change this please see the configuration options below

## Configuration
```yml
Vulcan\CurrencyConversion\CurrencyConversion:
  api_key: "YOUR-API-KEY" # Get yours from currencylayer.com
  base_currency: "NZD" # The default currency to be used in conversions
  cron_schedule: "*/5 * * * *" # How often exchange rates will be updated
```

## Usage
```php
\Vulcan\CurrencyConversion\CurrencyConversion::convert(1, 'USD'); // will convert 1 USD to the base_currency
\Vulcan\CurrencyConversion\CurrencyConversion::convert(1, 'USD', 'GBP'); // will convert 1 USD to GBP
```
## License
[BSD-3-Clause](LICENSE.md)