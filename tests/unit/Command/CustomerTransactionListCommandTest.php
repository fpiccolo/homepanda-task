<?php
declare(strict_types=1);

namespace App\Tests\unit\Command;

use App\Command\CustomerTransactionListCommand;
use App\Command\TableHelper;
use App\DTO\CustomerTransactionOutput;
use App\Enum\Currency;
use App\Manager\CustomerManager;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomerTransactionListCommandTest extends TestCase
{
    use ProphecyTrait;

    private const PARAM_CUSTOMER = 'customer';

    private const TABLE_HEADER = [
        'date',
        'currency',
        'value'
    ];

    private ObjectProphecy|CustomerManager $customerManager;
    private ObjectProphecy|TableHelper $tableHelper;
    private ObjectProphecy|InputInterface $input;
    private ObjectProphecy|OutputInterface $output;
    private CustomerTransactionListCommand $customerTransactionListCommand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerManager = $this->prophesize(CustomerManager::class);
        $this->tableHelper = $this->prophesize(TableHelper::class);
        $this->input = $this->prophesize(InputInterface::class);
        $this->output = $this->prophesize(OutputInterface::class);

        $this->customerTransactionListCommand = new CustomerTransactionListCommand(
            $this->customerManager->reveal(),
            $this->tableHelper->reveal()
        );
    }

    public function testExecuteFailWithException(): void
    {
        $customerId = 1;
        $exception = new \Exception('message');

        $this->input
            ->getArgument(self::PARAM_CUSTOMER)
            ->willReturn($customerId)
            ->shouldBeCalledOnce();

        $this->customerManager
            ->getCustomerTransactionsInEur($customerId)
            ->willThrow($exception)
            ->shouldBeCalledOnce();

        $this->output->writeln('<error>'.$exception->getMessage().'</error>')
            ->shouldBeCalledOnce();

        $result = $this->customerTransactionListCommand->execute(
            $this->input->reveal(),
            $this->output->reveal()
        );

        self::assertEquals(Command::FAILURE, $result);
    }

    public function testExecuteSuccess(): void
    {
        $customerId = 1;
        $customerTransactionDto = new CustomerTransactionOutput(
            Chronos::now(),
            Currency::EUR,
            15.25
        );

        /** @var MockObject $table */
        $table = $this->createMock(Table::class);

        $this->input
            ->getArgument(self::PARAM_CUSTOMER)
            ->willReturn($customerId)
            ->shouldBeCalledOnce();

        $this->customerManager
            ->getCustomerTransactionsInEur($customerId)
            ->willReturn([$customerTransactionDto])
            ->shouldBeCalledOnce();

        $this->tableHelper
            ->instantiateTable($this->output)
            ->willReturn($table)
            ->shouldBeCalledOnce();

        $table
            ->expects(self::once())
            ->method('setHeaders')
            ->with(self::TABLE_HEADER);

        $table
            ->expects(self::once())
            ->method('addRow')
            ->with([
                $customerTransactionDto->date,
                $customerTransactionDto->currency->value,
                $customerTransactionDto->value,
            ]);

        $table
            ->expects(self::once())
            ->method('render');


        $result = $this->customerTransactionListCommand->execute(
            $this->input->reveal(),
            $this->output->reveal()
        );

        self::assertEquals(Command::SUCCESS, $result);
    }
}