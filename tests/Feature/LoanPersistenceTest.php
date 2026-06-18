<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Domain\Loan\Entity\Loan;
use App\Shared\Domain\ValueObject\Money;
use App\Tests\Support\DatabaseTestCase;
use App\Tests\Support\LendingFixtures;

final class LoanPersistenceTest extends DatabaseTestCase
{
    use LendingFixtures;

    public function testLoanWithMoneyRoundTripsThroughDoctrine(): void
    {
        $customer = $this->createCustomer($this->em);
        $product = $this->createProduct($this->em);

        $loan = Loan::approved($this->uuidFactory(), $customer, $product, new Money(750000));
        $this->em->persist($loan);
        $this->em->flush();
        $loanId = $loan->getId();

        $this->em->clear();

        $reloaded = $this->em->find(Loan::class, $loanId);
        if (!$reloaded instanceof Loan) {
            self::fail('Loan was not persisted.');
        }

        self::assertTrue($reloaded->isApproved());
        self::assertTrue($reloaded->getAmount()->equals(new Money(750000)));
        self::assertSame($customer->getId()->toString(), $reloaded->getCustomer()->getId()->toString());
    }
}
