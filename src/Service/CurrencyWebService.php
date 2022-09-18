<?php
declare(strict_types=1);

namespace App\Service;

use App\Enum\Currency;
use App\Exception\ExchangeRateCurrencyNotValidException;

class CurrencyWebService
{

    private const EXCHANGE_RATE = [
        "EUR" => [
            "USD" => 1.002870362275,
            "GBP" => 1.1436729355669
        ]
    ];

    public function getExchangeRate(Currency $currencyFrom, Currency $currencyTo): float
    {
        if ($currencyFrom === $currencyTo) {
            return 1;
        }

        if (!isset(self::EXCHANGE_RATE[$currencyFrom->value][$currencyTo->value])) {
            throw new ExchangeRateCurrencyNotValidException($currencyFrom, $currencyTo);
        }

        return self::EXCHANGE_RATE[$currencyFrom->value][$currencyTo->value];

    }
}