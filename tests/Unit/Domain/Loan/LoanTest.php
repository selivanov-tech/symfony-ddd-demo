<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Loan;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Loan\Entity\Loan;
use App\Domain\Loan\Event\LoanApproved;
use App\Domain\Loan\Event\LoanRejected;
use App\Domain\Product\Entity\Product;
use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class LoanTest extends TestCase
{
    public function testApprovedLoanRecordsALoanApprovedEvent(): void
    {
        $customer = $this->customer('customer-1');
        $amount = new Money(500000);

        $loan = Loan::approved($customer, $this->product(), $amount);

        self::assertTrue($loan->isApproved());
        self::assertNull($loan->getRejectReason());
        self::assertSame($amount, $loan->getAmount());

        $events = $loan->releaseEvents();
        self::assertCount(1, $events);

        $event = $events[0];
        self::assertInstanceOf(LoanApproved::class, $event);
        self::assertSame($loan->getId(), $event->aggregateId());
        self::assertSame('customer-1', $event->customerId);
        self::assertSame($amount, $event->amount);
    }

    public function testRejectedLoanRecordsALoanRejectedEventWithTheReason(): void
    {
        $customer = $this->customer('customer-2');

        $loan = Loan::rejected($customer, $this->product(), new Money(500000), 'Credit score too low');

        self::assertFalse($loan->isApproved());
        self::assertSame('Credit score too low', $loan->getRejectReason());

        $events = $loan->releaseEvents();
        self::assertCount(1, $events);

        $event = $events[0];
        self::assertInstanceOf(LoanRejected::class, $event);
        self::assertSame('customer-2', $event->customerId);
        self::assertSame('Credit score too low', $event->reason);
    }

    private function customer(string $id): Customer
    {
        $customer = new Customer();
        (new ReflectionProperty(Customer::class, 'id'))->setValue($customer, $id);

        return $customer;
    }

    private function product(): Product
    {
        return new Product();
    }
}
