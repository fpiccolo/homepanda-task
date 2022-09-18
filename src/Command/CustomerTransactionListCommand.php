<?php
declare(strict_types=1);

namespace App\Command;

use App\Manager\CustomerManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:customer:transaction:list',
    description: 'Get transaction list of a customer',
    hidden: false
)]
class CustomerTransactionListCommand extends Command
{
    private const PARAM_CUSTOMER = 'customer';

    private const TABLE_HEADER = [
        'date',
        'currency',
        'value'
    ];

    private CustomerManager $customerManager;
    private TableHelper $tableHelper;

    public function __construct(
        CustomerManager $customerManager,
        TableHelper $tableHelper
    )
    {
        parent::__construct();
        $this->customerManager = $customerManager;
        $this->tableHelper = $tableHelper;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::PARAM_CUSTOMER,
                InputArgument::REQUIRED,
                'The ID of the customer.'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $customerId = (int) $input->getArgument(self::PARAM_CUSTOMER);

        try {
            $transactions = $this->customerManager->getCustomerTransactionsInEur($customerId);
        }catch (\Exception $exception){
            $output->writeln('<error>'.$exception->getMessage().'</error>');
            return Command::FAILURE;
        }

        $table = $this->tableHelper->instantiateTable($output);
        $table->setHeaders(self::TABLE_HEADER);

        foreach ($transactions as $transaction){
            $table->addRow([
                $transaction->date,
                $transaction->currency->value,
                $transaction->value,
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}