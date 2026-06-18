<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Loan;

use App\Module\Customer\Domain\Entity\Customer;
use App\Module\Loan\Domain\Entity\Loan;
use App\Module\Loan\Domain\Event\LoanApproved;
use App\Module\Loan\Domain\Event\LoanRejected;
use App\Module\Product\Domain\Entity\Product;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use App\Tests\Builder\CustomerBuilder;
use App\Tests\Builder\ProductBuilder;
use PHPUnit\Framework\TestCase;

final class LoanTest extends TestCase
{
    private UuidFactoryInterface $uuid;

    protected function setUp(): void
    {
        $this->uuid = new SymfonyUuidFactory();
    }

    public function testApprovedLoanRecordsALoanApprovedEvent(): void
    {
        $customer = $this->customer();
        $amount = new Money(500000);

        $loan = Loan::approved($this->uuid, $customer, $this->product(), $amount);

        self::assertTrue($loan->isApproved());
        self::assertNull($loan->getRejectReason());
        self::assertSame($amount, $loan->getAmount());

        $events = $loan->releaseEvents();
        self::assertCount(1, $events);

        $event = $events[0];
        self::assertInstanceOf(LoanApproved::class, $event);
        self::assertSame($loan->getId()->toString(), $event->aggregateId());
        self::assertSame($customer->getId()->toString(), $event->customerId);
        self::assertSame($amount, $event->amount);
    }

    public function testRejectedLoanRecordsALoanRejectedEventWithTheReason(): void
    {
        $customer = $this->customer();

        $loan = Loan::rejected($this->uuid, $customer, $this->product(), new Money(500000), 'Credit score too low');

        self::assertFalse($loan->isApproved());
        self::assertSame('Credit score too low', $loan->getRejectReason());

        $events = $loan->releaseEvents();
        self::assertCount(1, $events);

        $event = $events[0];
        self::assertInstanceOf(LoanRejected::class, $event);
        self::assertSame($customer->getId()->toString(), $event->customerId);
        self::assertSame('Credit score too low', $event->reason);
    }

    private function customer(): Customer
    {
        return (new CustomerBuilder($this->uuid))->build();
    }

    private function product(): Product
    {
        return (new ProductBuilder($this->uuid))->build();
    }
}
