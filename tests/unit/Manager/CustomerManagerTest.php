<?php
declare(strict_types=1);

namespace App\Tests\unit\Manager;

use App\Entity\Customer;
use App\Entity\CustomerTransaction;
use App\Enum\Currency;
use App\Exception\CustomerNotFoundException;
use App\Manager\CustomerManager;
use App\Repository\CustomerRepository;
use App\Service\CurrencyConverter;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class CustomerManagerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|CustomerRepository $customerRepository;
    private ObjectProphecy|CurrencyConverter $currencyConverter;
    private CustomerManager $customerManager;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customerRepository = $this->prophesize(CustomerRepository::class);
        $this->currencyConverter = $this->prophesize(CurrencyConverter::class);
        
        $this->customerManager = new CustomerManager(
            $this->customerRepository->reveal(),
            $this->currencyConverter->reveal()
        );
    }

    public function testGetCustomerTransactionsInEurRaiseCustomerNotFoundException(): void
    {
        $customerId = 1;

        self::expectExceptionObject(new CustomerNotFoundException($customerId));

        $this->customerRepository
            ->find($customerId)
            ->willReturn(null)
            ->shouldBeCalledOnce();

        $this->currencyConverter
            ->convert(Argument::any())
            ->shouldNotBeCalled();

        $this->customerManager->getCustomerTransactionsInEur($customerId);
    }

    public function testGetCustomerTransactionsWithoutTransactions(): void
    {
        $customerId = 1;
        $customer = new Customer();

        $this->customerRepository
            ->find($customerId)
            ->willReturn($customer)
            ->shouldBeCalledOnce();

        $this->currencyConverter
            ->convert(Argument::any())
            ->shouldNotBeCalled();

        $result =$this->customerManager->getCustomerTransactionsInEur($customerId);

        self::assertEquals([], $result);
    }

    public function testGetCustomerTransactionsWithTransactions(): void
    {
        $customerId = 1;
        Chronos::setTestNow(Chronos::now());

        $customerTransaction = (new CustomerTransaction())
            ->setDate(Chronos::now())
            ->setCurrency(Currency::GBP)
            ->setValue(1.00);

        $customer = (new Customer())
            ->addTransaction($customerTransaction);

        $this->customerRepository
            ->find($customerId)
            ->willReturn($customer)
            ->shouldBeCalledOnce();

        $this->currencyConverter
            ->convert(
                $customerTransaction->getCurrency(),
                $customerTransaction->getValue(),
                Currency::EUR
            )
            ->willReturn(0.5)
            ->shouldBeCalledOnce();

        $result =$this->customerManager->getCustomerTransactionsInEur($customerId);

        self::assertCount(1, $result);
        self::assertEquals(Chronos::now()->format('Y-m-d'), $result[0]->date);
        self::assertEquals(Currency::EUR, $result[0]->currency);
        self::assertEquals(0.50, $result[0]->value);

        Chronos::setTestNow();
    }

}