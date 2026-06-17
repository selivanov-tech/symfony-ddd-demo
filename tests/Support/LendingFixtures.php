<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\ValueObject\StatesScoreMultiplierCollection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Builds and persists the loan-domain entities feature tests need.
 *
 * Defaults describe an eligible applicant in CA; override fields to exercise the
 * denial paths. State multipliers are intentionally empty to keep fixtures simple.
 */
trait LendingFixtures
{
    /**
     * @param array<string, mixed> $overrides
     */
    private function createCustomer(EntityManagerInterface $em, array $overrides = []): Customer
    {
        $customer = (new Customer())
            ->setEmail($overrides['email'] ?? 'jane.doe@example.com')
            ->setPhone($overrides['phone'] ?? '5550000001')
            ->setSsn($overrides['ssn'] ?? '123-45-6789')
            ->setFirstName($overrides['firstName'] ?? 'Jane')
            ->setLastName($overrides['lastName'] ?? 'Doe')
            ->setBirthday($overrides['birthday'] ?? new \DateTimeImmutable('1990-01-01'))
            ->setFicoScore($overrides['ficoScore'] ?? 720)
            ->setAddress($overrides['address'] ?? [
                'street' => '1 Market St',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip' => '94105',
            ])
            ->setMonthlyIncome($overrides['monthlyIncome'] ?? 6000);

        $em->persist($customer);
        $em->flush();

        return $customer;
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProduct(EntityManagerInterface $em, array $overrides = []): Product
    {
        $product = (new Product())
            ->setName($overrides['name'] ?? 'Personal Loan')
            ->setTermInMonths($overrides['termInMonths'] ?? 24)
            ->setInterestRate($overrides['interestRate'] ?? 9.5)
            ->setAmount($overrides['amount'] ?? 10000.0)
            ->setMinFICOScore($overrides['minFICOScore'] ?? 600)
            ->setMinMonthlyIncome($overrides['minMonthlyIncome'] ?? 2000)
            ->setMinAge($overrides['minAge'] ?? 18)
            ->setMaxAge($overrides['maxAge'] ?? 70)
            ->setAvailableStates($overrides['availableStates'] ?? ['CA', 'NV'])
            ->setStatesScoreMultipliers(new StatesScoreMultiplierCollection([]));

        $em->persist($product);
        $em->flush();

        return $product;
    }
}
