<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Module\Loan\Domain\Entity\Loan;
use App\Shared\Domain\ValueObject\Money;
use App\Tests\Support\DatabaseTestCase;
use App\Tests\Support\LendingFixtures;

final class LoanPersistenceTest extends DatabaseTestCase
{
    use LendingFixtures;

    public function testLoanWithMoneyRoundTripsThroughDoctrine(): void
    {
        $customerId = $this->uuidFactory()->uuid7();
        $productId = $this->uuidFactory()->uuid7();

        $loan = Loan::approved($this->uuidFactory(), $customerId, $productId, new Money(750000));
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
        self::assertSame($customerId->toString(), $reloaded->getCustomerId()->toString());
    }
}
