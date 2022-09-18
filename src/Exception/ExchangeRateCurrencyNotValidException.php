<?php
declare(strict_types=1);

namespace App\Exception;

use App\Enum\Currency;

class ExchangeRateCurrencyNotValidException extends NotFoundException
{
    public function __construct(Currency $currencyFrom, Currency $currencyTo)
    {
        parent::__construct("Impossible find exchage rate from [{$currencyFrom->value}] to [{$currencyTo->value}]");
    }
}