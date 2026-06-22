<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Loan;

use App\Module\Loan\Application\EventHandler\LoanDecisionNotifier;
use App\Module\Loan\Application\ReadModel\ApplicantProfile;
use App\Module\Loan\Application\Repository\ApplicantReadModelRepositoryInterface;
use App\Module\Loan\Domain\Entity\Loan;
use App\Module\Loan\Domain\Event\LoanApproved;
use App\Module\Loan\Domain\Repository\LoanRepositoryInterface;
use App\Shared\Application\Notification\NotificationSenderInterface;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\Identity\UuidInterface;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use App\Tests\Builder\CreditProfileBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Recipient\Recipient;

final class LoanDecisionNotifierTest extends TestCase
{
    private UuidFactoryInterface $uuid;

    protected function setUp(): void
    {
        $this->uuid = new SymfonyUuidFactory();
    }

    public function testItNotifiesTheCustomerWhenALoanIsApproved(): void
    {
        $customerId = $this->uuid->uuid7();
        $loan = Loan::approved($this->uuid, $customerId, $this->uuid->uuid7(), new Money(500000));

        $loans = $this->createMock(LoanRepositoryInterface::class);
        $loans->method('findById')->with($loan->getId()->toString())->willReturn($loan);

        $applicants = $this->createMock(ApplicantReadModelRepositoryInterface::class);
        $applicants->method('findById')->with($customerId->toString())->willReturn($this->applicant($customerId));

        $notifications = $this->createMock(NotificationSenderInterface::class);
        $notifications->expects(self::once())
            ->method('send')
            ->with(
                'Loan Request Results',
                self::stringContains('approved'),
                self::isInstanceOf(Recipient::class),
            );

        $notifier = new LoanDecisionNotifier($loans, $applicants, $notifications);
        $notifier->onApproved(new LoanApproved($loan->getId()->toString(), $customerId->toString(), new Money(500000)));
    }

    public function testItDoesNothingWhenTheLoanIsGone(): void
    {
        $loans = $this->createMock(LoanRepositoryInterface::class);
        $loans->method('findById')->willReturn(null);

        $applicants = $this->createMock(ApplicantReadModelRepositoryInterface::class);

        $notifications = $this->createMock(NotificationSenderInterface::class);
        $notifications->expects(self::never())->method('send');

        $notifier = new LoanDecisionNotifier($loans, $applicants, $notifications);
        $notifier->onApproved(new LoanApproved('missing-loan', 'customer-1', new Money(500000)));
    }

    private function applicant(UuidInterface $id): ApplicantProfile
    {
        return new ApplicantProfile(
            $id,
            'Jane Doe',
            'jane.doe@example.com',
            '5550000001',
            (new CreditProfileBuilder())->build(),
        );
    }
}
