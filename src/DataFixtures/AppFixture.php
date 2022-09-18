<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\CustomerTransaction;
use App\Enum\Currency;
use Cake\Chronos\Chronos;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class AppFixture extends Fixture
{
    private const FIXTURE_DATA = [
        [
            "name" => 'company 1',
            "transactions" => [
                [
                    "date" => "2015-04-01",
                    "currency" => Currency::GBP,
                    "value" => 50.00
                ],
                [
                    "date" => "2015-04-02",
                    "currency" => Currency::GBP,
                    "value" => 11.04
                ],
                [
                    "date" => "2015-04-02",
                    "currency" => Currency::EUR,
                    "value" => 1.00
                ],
                [
                    "date" => "2015-04-03",
                    "currency" => Currency::USD,
                    "value" => 23.05
                ]
            ]
        ],
        [
            "name" => 'company 2',
            "transactions" => [
                [
                    "date" => "2015-04-01",
                    "currency" => Currency::USD,
                    "value" => 66.10
                ],
                [

                    "date" => "2015-04-02",
                    "currency" => Currency::EUR,
                    "value" => 12.00
                ],
                [

                    "date" => "2015-04-02",
                    "currency" => Currency::GBP,
                    "value" => 6.50
                ],
                [
                    "date" => "2015-04-04",
                    "currency" => Currency::EUR,
                    "value" => 6.50
                ]

            ]
        ]
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::FIXTURE_DATA as $customerData) {
            $customer = (new Customer())
                ->setName($customerData['name']);

            foreach ($customerData['transactions'] as $transactionData) {
                $customer->addTransaction(
                    (new CustomerTransaction())
                        ->setDate(Chronos::createFromFormat('Y-m-d',$transactionData['date']))
                        ->setCurrency($transactionData['currency'])
                        ->setValue($transactionData['value'])
                );

                $manager->persist($customer);
            }

            $manager->flush();
        }
    }
}