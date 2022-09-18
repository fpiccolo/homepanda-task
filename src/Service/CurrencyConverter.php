<?php
declare(strict_types=1);

namespace App\Service;

use App\Enum\Currency;

class CurrencyConverter
{
    private CurrencyWebService $currencyWebService;

    public function __construct(
        CurrencyWebService $currencyWebService
    )
    {
        $this->currencyWebService = $currencyWebService;
    }

    public function convert(Currency $currency, float $value, Currency $currencyToConvert): float
    {
        $exchangeRate = $this->currencyWebService->getExchangeRate($currencyToConvert, $currency);

        return $value * $exchangeRate;
    }

}