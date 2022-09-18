<?php
declare(strict_types=1);

namespace App\Tests\unit\Service;

use App\Enum\Currency;
use App\Service\CurrencyConverter;
use App\Service\CurrencyWebService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class CurrencyConverterTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|CurrencyWebService $currencyWebService;
    private CurrencyConverter $currencyConverter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyWebService = $this->prophesize(CurrencyWebService::class);

        $this->currencyConverter = new CurrencyConverter(
            $this->currencyWebService->reveal()
        );
    }

    public function testConvert(): void
    {
        $currencyToConvert = Currency::EUR;
        $currency = Currency::GBP;
        $value = 8.50;
        $exchangeRate = 0.5;

        $this->currencyWebService
            ->getExchangeRate(
                $currencyToConvert,
                $currency
            )
            ->willReturn($exchangeRate)
            ->shouldBeCalledOnce();

        $result = $this->currencyConverter->convert(
            $currency,
            $value,
            $currencyToConvert
        );

        self::assertEquals($value * $exchangeRate, $result);
    }
}