<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Domain\Customer\Entity\Customer;
use App\Tests\Support\DatabaseTestCase;
use App\Tests\Support\LendingFixtures;

final class CustomerPersistenceTest extends DatabaseTestCase
{
    use LendingFixtures;

    public function testValueObjectsAndEmbeddedAddressRoundTripThroughDoctrine(): void
    {
        $customer = $this->createCustomer($this->em, $this->customerBuilder()->withEmail('round.trip@example.com')->withFicoScore(815));
        $id = $customer->getId();

        $this->em->clear();

        $reloaded = $this->em->find(Customer::class, $id);
        if (!$reloaded instanceof Customer) {
            self::fail('Customer was not persisted.');
        }

        self::assertSame('round.trip@example.com', (string) $reloaded->getEmail());
        self::assertSame('5550000001', (string) $reloaded->getPhone());
        self::assertSame('123-45-6789', (string) $reloaded->getSsn());
        self::assertSame(815, $reloaded->getFicoScore()->value);
        self::assertSame('CA', $reloaded->getAddress()->getState());
    }
}
