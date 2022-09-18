<?php
declare(strict_types=1);

namespace App\Tests\unit\Controller;

use App\Controller\CustomerTransactionController;
use App\DTO\CustomerTransactionOutput;
use App\DTO\ExceptionOutput;
use App\Enum\Currency;
use App\Manager\CustomerManager;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomerTransactionControllerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|CustomerManager $customerManager;

    private ObjectProphecy|ContainerInterface $container;

    private CustomerTransactionController $customerTransactionController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerManager = $this->prophesize(CustomerManager::class);
        $this->container = $this->prophesize(ContainerInterface::class);

        $this->customerTransactionController = new CustomerTransactionController(
            $this->customerManager->reveal()
        );

        $this->container->has(Argument::any())->willReturn(false);

        $this->customerTransactionController->setContainer($this->container->reveal());
    }

    public function testGetCustomerTransactionsActionReturnAnExceptionResponse(): void
    {
        $exception = new \Exception('message', Response::HTTP_NOT_FOUND);

        $this->customerManager
            ->getCustomerTransactionsInEur(1)
            ->willThrow($exception)
            ->shouldBeCalledOnce()
        ;

        $response = $this->customerTransactionController->getCustomerTransactionsAction(1);

        $expectedResponse = new JsonResponse(
            new ExceptionOutput(
                $exception->getMessage(),
                $exception->getCode()
            ),
            $exception->getCode()
        );

        self::assertEquals($expectedResponse->getStatusCode(), $response->getStatusCode());
        self::assertEquals($expectedResponse->getContent(), $response->getContent());

    }

    public function testGetCustomerTransactionsActionReturnASuccessResponse(): void
    {
        $customerTransactionDto = new CustomerTransactionOutput(
            Chronos::now(),
            Currency::EUR,
            15.25
        );

        $this->customerManager
            ->getCustomerTransactionsInEur(1)
            ->willReturn([$customerTransactionDto])
            ->shouldBeCalledOnce()
        ;

        $response = $this->customerTransactionController->getCustomerTransactionsAction(1);

        $expectedResponse = new JsonResponse(
            [$customerTransactionDto],
        );

        self::assertEquals($expectedResponse->getStatusCode(), $response->getStatusCode());
        self::assertEquals($expectedResponse->getContent(), $response->getContent());

    }
}