<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Loan;

use App\Module\Customer\Domain\Entity\Customer;
use App\Module\Customer\Domain\Repository\CustomerRepositoryInterface;
use App\Module\Loan\Application\Command\ApplyForLoan\ApplyForLoanCommand;
use App\Module\Loan\Application\Command\ApplyForLoan\ApplyForLoanHandler;
use App\Module\Loan\Domain\Event\LoanApproved;
use App\Module\Loan\Domain\Event\LoanRejected;
use App\Module\Loan\Domain\Repository\LoanRepositoryInterface;
use App\Module\Loan\Domain\Service\LoanEligibilityChecker;
use App\Module\Product\Domain\Entity\Product;
use App\Module\Product\Domain\Repository\ProductRepositoryInterface;
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
