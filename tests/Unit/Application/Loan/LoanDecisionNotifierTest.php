<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Loan;

use App\Module\Customer\Domain\Entity\Customer;
use App\Module\Loan\Application\EventHandler\LoanDecisionNotifier;
use App\Module\Loan\Domain\Entity\Loan;
use App\Module\Loan\Domain\Event\LoanApproved;
use App\Module\Loan\Domain\Repository\LoanRepositoryInterface;
use App\Shared\Application\Notification\NotificationSenderInterface;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use App\Tests\Builder\CustomerBuilder;
use App\Tests\Builder\ProductBuilder;
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
        $loan = Loan::approved($this->uuid, $this->customer(), (new ProductBuilder($this->uuid))->build(), new Money(500000));

        $loans = $this->createMock(LoanRepositoryInterface::class);
        $loans->method('findById')->with($loan->getId()->toString())->willReturn($loan);

        $notifications = $this->createMock(NotificationSenderInterface::class);
        $notifications->expects(self::once())
            ->method('send')
            ->with(
                'Loan Request Results',
                self::stringContains('approved'),
                self::isInstanceOf(Recipient::class),
            );

        $notifier = new LoanDecisionNotifier($loans, $notifications);
        $notifier->onApproved(new LoanApproved($loan->getId()->toString(), 'customer-1', new Money(500000)));
    }

    public function testItDoesNothingWhenTheLoanIsGone(): void
    {
        $loans = $this->createMock(LoanRepositoryInterface::class);
        $loans->method('findById')->willReturn(null);

        $notifications = $this->createMock(NotificationSenderInterface::class);
        $notifications->expects(self::never())->method('send');

        $notifier = new LoanDecisionNotifier($loans, $notifications);
        $notifier->onApproved(new LoanApproved('missing-loan', 'customer-1', new Money(500000)));
    }

    private function customer(): Customer
    {
        return (new CustomerBuilder($this->uuid))->build();
    }
}
