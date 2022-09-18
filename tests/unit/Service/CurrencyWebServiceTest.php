<?php
declare(strict_types=1);

namespace App\Tests\unit\Service;

use App\Enum\Currency;
use App\Exception\ExchangeRateCurrencyNotValidException;
use App\Service\CurrencyWebService;
use PHPUnit\Framework\TestCase;

class CurrencyWebServiceTest extends TestCase
{
    private CurrencyWebService $currencyWebService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyWebService = new CurrencyWebService();
    }

    /**
     * @dataProvider getExchangeRateReturnOneWithSameCurrenciesDataProvider
     */
    public function testGetExchangeRateReturnOneWithSameCurrencies(Currency $currency): void
    {
        self::assertEquals(1, $this->currencyWebService->getExchangeRate($currency, $currency));
    }

    private function getExchangeRateReturnOneWithSameCurrenciesDataProvider(): iterable
    {
        yield [Currency::GBP];
        yield [Currency::EUR];
        yield [Currency::USD];
    }

    public function testGetExchangeRateRaiseExchangeRateCurrencyNotValidException(): void
    {
        $currencyFrom = Currency::GBP;
        $currencyTo = Currency::EUR;

        self::expectExceptionObject(new ExchangeRateCurrencyNotValidException($currencyFrom, $currencyTo));

        $this->currencyWebService->getExchangeRate(
            $currencyFrom,
            $currencyTo
        );
    }

    public function testGetExchangeRate(): void
    {
        $currencyFrom = Currency::EUR;
        $currencyTo = Currency::GBP;


        $result = $this->currencyWebService->getExchangeRate(
            $currencyFrom,
            $currencyTo
        );

        self::assertIsFloat($result);
    }
}