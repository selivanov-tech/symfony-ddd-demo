<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Loan;

use App\Module\Loan\Application\Command\ApplyForLoan\ApplyForLoanCommand;
use App\Module\Loan\Application\Command\ApplyForLoan\ApplyForLoanHandler;
use App\Module\Loan\Application\ReadModel\ApplicantProfile;
use App\Module\Loan\Application\ReadModel\ProductOffer;
use App\Module\Loan\Application\Repository\ApplicantReadModelRepositoryInterface;
use App\Module\Loan\Application\Repository\ProductReadModelRepositoryInterface;
use App\Module\Loan\Domain\Event\LoanApproved;
use App\Module\Loan\Domain\Event\LoanRejected;
use App\Module\Loan\Domain\Repository\LoanRepositoryInterface;
use App\Module\Loan\Domain\Service\LoanEligibilityChecker;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Infrastructure\Identity\SymfonyUuidFactory;
use App\Tests\Builder\CreditProfileBuilder;
use App\Tests\Builder\ProductTermsBuilder;
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
        $applicant = $this->applicant();
        $handler = $this->handler($applicant, $this->offer(), $spy);

        $decision = $handler(new ApplyForLoanCommand('product-1', 'customer-1'));

        self::assertTrue($decision->approved);
        self::assertCount(1, $spy->published);

        $event = $spy->published[0];
        if (!$event instanceof LoanApproved) {
            self::fail('Expected a LoanApproved event.');
        }
        self::assertSame($applicant->id->toString(), $event->customerId);
        self::assertSame($decision->loanId, $event->aggregateId());
    }

    public function testIneligibleApplicantGetsARejectedLoanAndPublishesLoanRejected(): void
    {
        $spy = new SpyEventBus();
        $handler = $this->handler(
            $this->applicant(ficoScore: 500),
            $this->offer(minFicoScore: 800),
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

    private function handler(ApplicantProfile $applicant, ProductOffer $offer, SpyEventBus $eventBus): ApplyForLoanHandler
    {
        $products = $this->createMock(ProductReadModelRepositoryInterface::class);
        $products->method('findById')->willReturn($offer);

        $applicants = $this->createMock(ApplicantReadModelRepositoryInterface::class);
        $applicants->method('findById')->willReturn($applicant);

        return new ApplyForLoanHandler(
            $products,
            $applicants,
            $this->createMock(LoanRepositoryInterface::class),
            new LoanEligibilityChecker(new FixedNewYorkLottery(rejects: false)),
            $this->uuid,
            $eventBus,
        );
    }

    private function applicant(int $ficoScore = 720): ApplicantProfile
    {
        return new ApplicantProfile(
            $this->uuid->uuid7(),
            'Jane Doe',
            'jane.doe@example.com',
            '5550000001',
            (new CreditProfileBuilder())->withFicoScore($ficoScore)->build(),
        );
    }

    private function offer(int $minFicoScore = 600): ProductOffer
    {
        return new ProductOffer(
            $this->uuid->uuid7(),
            10000.0,
            (new ProductTermsBuilder())->withMinFicoScore($minFicoScore)->build(),
        );
    }
}
