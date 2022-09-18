<?php
declare(strict_types=1);

namespace App\Manager;

use App\DTO\CustomerTransactionOutput;
use App\Entity\Customer;
use App\Enum\Currency;
use App\Exception\CustomerNotFoundException;
use App\Repository\CustomerRepository;
use App\Service\CurrencyConverter;

class CustomerManager
{
    private CustomerRepository $customerRepository;
    private CurrencyConverter $currencyConverter;

    public function __construct(
        CustomerRepository $customerRepository,
        CurrencyConverter $currencyConverter

    )
    {
        $this->customerRepository = $customerRepository;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * @return CustomerTransactionOutput[]
     */
    public function getCustomerTransactionsInEur(int $customerId): array
    {
        $customer = $this->getCustomer($customerId);

        $transactions = [];
        foreach ($customer->getTransactions() as $transaction){
            $valueConverted = $this->currencyConverter->convert(
                $transaction->getCurrency(),
                $transaction->getValue(),
                Currency::EUR
            );

            $transactions[] = new CustomerTransactionOutput(
                $transaction->getDate(),
                Currency::EUR,
                round($valueConverted,2)
            );
        }

        return $transactions;
    }

    private function getCustomer(int $customerId): Customer
    {
        $customer = $this->customerRepository->find($customerId);

        if(null === $customer){
            throw new CustomerNotFoundException($customerId);
        }

        return $customer;
    }
}