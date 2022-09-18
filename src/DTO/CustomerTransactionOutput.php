<?php
declare(strict_types=1);

namespace App\DTO;

use App\Enum\Currency;

class CustomerTransactionOutput
{
    public string $date;

    public Currency $currency;

    public float $value;

    public function __construct(
        \DateTimeInterface $date,
        Currency $currency,
        float $value
    )
    {
        $this->date = $date->format('Y-m-d');
        $this->currency = $currency;
        $this->value = $value;
    }
}