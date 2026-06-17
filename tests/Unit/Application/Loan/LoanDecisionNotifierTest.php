<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Loan;

use App\Application\Loan\EventHandler\LoanDecisionNotifier;
use App\Domain\Customer\Entity\Customer;
use App\Domain\Loan\Entity\Loan;
use App\Domain\Loan\Event\LoanApproved;
use App\Domain\Loan\Repository\LoanRepositoryInterface;
use App\Domain\Product\Entity\Product;
use App\Infrastructure\Service\Notification\NotificationService;
use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Notifier\Recipient\Recipient;

final class LoanDecisionNotifierTest extends TestCase
{
    public function testItNotifiesTheCustomerWhenALoanIsApproved(): void
    {
        $loan = Loan::approved($this->customer(), new Product(), new Money(500000));

        $loans = $this->createMock(LoanRepositoryInterface::class);
        $loans->method('findById')->with($loan->getId())->willReturn($loan);

        $notifications = $this->createMock(NotificationService::class);
        $notifications->expects(self::once())
            ->method('send')
            ->with(
                'Loan Request Results',
                self::stringContains('approved'),
                self::isInstanceOf(Recipient::class),
            );

        $notifier = new LoanDecisionNotifier($loans, $notifications);
        $notifier->onApproved(new LoanApproved($loan->getId(), 'customer-1', new Money(500000)));
    }

    public function testItDoesNothingWhenTheLoanIsGone(): void
    {
        $loans = $this->createMock(LoanRepositoryInterface::class);
        $loans->method('findById')->willReturn(null);

        $notifications = $this->createMock(NotificationService::class);
        $notifications->expects(self::never())->method('send');

        $notifier = new LoanDecisionNotifier($loans, $notifications);
        $notifier->onApproved(new LoanApproved('missing-loan', 'customer-1', new Money(500000)));
    }

    private function customer(): Customer
    {
        $customer = (new Customer())
            ->setEmail('jane.doe@example.com')
            ->setPhone('5550000001')
            ->setSsn('123-45-6789')
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setBirthday(new \DateTimeImmutable('1990-01-01'))
            ->setFicoScore(720)
            ->setAddress(['street' => '1 Market St', 'city' => 'San Francisco', 'state' => 'CA', 'zip' => '94105'])
            ->setMonthlyIncome(6000);

        (new ReflectionProperty(Customer::class, 'id'))->setValue($customer, 'customer-1');

        return $customer;
    }
}
