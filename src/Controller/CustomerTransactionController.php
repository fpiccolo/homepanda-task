<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\ExceptionOutput;
use App\Manager\CustomerManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CustomerTransactionController extends AbstractController
{
    private CustomerManager $customerManager;

    public function __construct(CustomerManager $customerManager)
    {
        $this->customerManager = $customerManager;
    }

    #[Route('customer/{customerId}/transactions', name: 'customer_transactions', methods: ['GET'])]
    public function getCustomerTransactionsAction(int $customerId): JsonResponse
    {
        try {
            $transactions = $this->customerManager->getCustomerTransactionsInEur($customerId);
        }catch (\Exception $exception){
            return $this->json(
                new ExceptionOutput(
                    $exception->getMessage(),
                    $exception->getCode()
                ),
                $exception->getCode()
            );
        }

        return $this->json(
            $transactions
        );
    }
}