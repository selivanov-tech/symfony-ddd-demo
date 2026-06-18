<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Loan;

use App\Application\Loan\Command\ApplyForLoan\ApplyForLoanCommand;
use App\Application\Loan\Command\ApplyForLoan\ApplyForLoanHandler;
use App\Domain\Customer\Entity\Customer;
use App\Domain\Customer\Repository\CustomerRepositoryInterface;
use App\Domain\Loan\Event\LoanApproved;
use App\Domain\Loan\Event\LoanRejected;
use App\Domain\Loan\Repository\LoanRepositoryInterface;
use App\Domain\Loan\Service\LoanEligibilityChecker;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use App\Tests\Builder\CustomerBuilder;
use App\Tests\Builder\ProductBuilder;
use App\Tests\Support\FixedNewYorkLottery;
use App\Tests\Support\SpyEventBus;
use PHPUnit\Framework\TestCase;

final class ApplyForLoanHandlerTest extends TestCase
{
    private UuidFactoryInterface $uuid;

    protected function setUp(): void
    {
        $this->uuid = new SymfonyUuidFactory();
    }

    public function testEligibleApplicantGetsAnApprovedLoanAndPublishesLoanApproved(): void
    {
        $spy = new SpyEventBus();
        $customer = $this->customer();
        $handler = $this->handler($customer, $this->product(), $spy);

        $decision = $handler(new ApplyForLoanCommand('product-1', 'customer-1'));

        self::assertTrue($decision->approved);
        self::assertCount(1, $spy->published);

        $event = $spy->published[0];
        if (!$event instanceof LoanApproved) {
            self::fail('Expected a LoanApproved event.');
        }
        self::assertSame($customer->getId()->toString(), $event->customerId);
        self::assertSame($decision->loanId, $event->aggregateId());
    }

    public function testIneligibleApplicantGetsARejectedLoanAndPublishesLoanRejected(): void
    {
        $spy = new SpyEventBus();
        $handler = $this->handler(
            $this->customer(ficoScore: 500),
            $this->product(minFicoScore: 800),
            $spy,
        );

        $decision = $handler(new ApplyForLoanCommand('product-1', 'customer-2'));

        self::assertFalse($decision->approved);

        $event = $spy->published[0];
        if (!$event instanceof LoanRejected) {
            self::fail('Expected a LoanRejected event.');
        }
        self::assertStringContainsString('Credit score too low', $event->reason);
    }

    private function handler(Customer $customer, Product $product, SpyEventBus $eventBus): ApplyForLoanHandler
    {
        $products = $this->createMock(ProductRepositoryInterface::class);
        $products->method('findById')->willReturn($product);

        $customers = $this->createMock(CustomerRepositoryInterface::class);
        $customers->method('findById')->willReturn($customer);

        return new ApplyForLoanHandler(
            $products,
            $customers,
            $this->createMock(LoanRepositoryInterface::class),
            new LoanEligibilityChecker(new FixedNewYorkLottery(rejects: false)),
            $this->uuid,
            $eventBus,
        );
    }

    private function customer(int $ficoScore = 720): Customer
    {
        return (new CustomerBuilder($this->uuid))->withFicoScore($ficoScore)->build();
    }

    private function product(int $minFicoScore = 600): Product
    {
        return (new ProductBuilder($this->uuid))->withMinFICOScore($minFicoScore)->build();
    }
}
