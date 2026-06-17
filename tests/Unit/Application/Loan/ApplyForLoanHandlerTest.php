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
use App\Domain\Product\ValueObject\StatesScoreMultiplierCollection;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
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
            $this->customer(['ficoScore' => 500]),
            $this->product(['minFICOScore' => 800]),
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

    /**
     * @param array<string, mixed> $overrides
     */
    private function customer(array $overrides = []): Customer
    {
        return (new Customer($this->uuid->uuid7()))
            ->setEmail('jane.doe@example.com')
            ->setPhone('5550000001')
            ->setSsn('123-45-6789')
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setBirthday(new \DateTimeImmutable('1990-01-01'))
            ->setFicoScore($overrides['ficoScore'] ?? 720)
            ->setAddress(['street' => '1 Market St', 'city' => 'San Francisco', 'state' => 'CA', 'zip' => '94105'])
            ->setMonthlyIncome(6000);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function product(array $overrides = []): Product
    {
        return (new Product($this->uuid->uuid7()))
            ->setName('Personal Loan')
            ->setTermInMonths(24)
            ->setInterestRate(9.5)
            ->setAmount(10000.0)
            ->setMinFICOScore($overrides['minFICOScore'] ?? 600)
            ->setMinMonthlyIncome(2000)
            ->setMinAge(18)
            ->setMaxAge(70)
            ->setAvailableStates(['CA', 'NV'])
            ->setStatesScoreMultipliers(new StatesScoreMultiplierCollection([]));
    }
}
