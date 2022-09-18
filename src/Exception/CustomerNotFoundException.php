<?php
declare(strict_types=1);

namespace App\Exception;

class CustomerNotFoundException extends NotFoundException
{
    public function __construct(int $customerId)
    {
        parent::__construct("Customer with id [{$customerId}] not found");
    }
}