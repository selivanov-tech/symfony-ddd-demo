<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Loan;

use App\Module\Loan\Domain\Exception\LoanApplicationDeniedException;
use App\Module\Loan\Domain\Service\LoanEligibilityChecker;
use App\Module\Loan\Domain\ValueObject\CreditProfile;
use App\Module\Loan\Domain\ValueObject\ProductTerms;
use App\Tests\Builder\CreditProfileBuilder;
use App\Tests\Builder\ProductTermsBuilder;
use App\Tests\Support\FixedNewYorkLottery;
use PHPUnit\Framework\TestCase;

final class LoanEligibilityCheckerTest extends TestCase
{
    private LoanEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new LoanEligibilityChecker(new FixedNewYorkLottery(rejects: false));
    }

    public function testEligibleApplicantPasses(): void
    {
        self::assertTrue(
            $this->checker->isEligible((new ProductTermsBuilder())->build(), (new CreditProfileBuilder())->build())
        );
    }

    public function testNewYorkApplicantIsDeniedWhenTheLotterySelectsThem(): void
    {
        $checker = new LoanEligibilityChecker(new FixedNewYorkLottery(rejects: true));

        try {
            $checker->isEligible(
                (new ProductTermsBuilder())->withAvailableStates(['CA', 'NY'])->build(),
                (new CreditProfileBuilder())->withState('NY')->build(),
            );
            self::fail('Expected LoanApplicationDeniedException was not thrown.');
        } catch (LoanApplicationDeniedException $exception) {
            self::assertStringContainsString('Random rejection for NY state', $exception->getReason());
        }
    }

    public function testNewYorkApplicantPassesWhenTheLotteryDoesNot(): void
    {
        $checker = new LoanEligibilityChecker(new FixedNewYorkLottery(rejects: false));

        self::assertTrue(
            $checker->isEligible(
                (new ProductTermsBuilder())->withAvailableStates(['CA', 'NY'])->build(),
                (new CreditProfileBuilder())->withState('NY')->build(),
            )
        );
    }

    public function testTooLowFicoIsDenied(): void
    {
        $reason = $this->denialReason(
            (new ProductTermsBuilder())->withMinFicoScore(800)->build(),
            (new CreditProfileBuilder())->withFicoScore(700)->build(),
        );

        self::assertStringContainsString('Credit score too low', $reason);
    }

    public function testTooLowIncomeIsDenied(): void
    {
        $reason = $this->denialReason(
            (new ProductTermsBuilder())->withMinMonthlyIncome(10000)->build(),
            (new CreditProfileBuilder())->withMonthlyIncome(3000)->build(),
        );

        self::assertStringContainsString('Monthly income too low', $reason);
    }

    public function testAgeOutsideRangeIsDenied(): void
    {
        $reason = $this->denialReason(
            (new ProductTermsBuilder())->withMinAge(18)->withMaxAge(25)->build(),
            (new CreditProfileBuilder())->withAge(46)->build(),
        );

        self::assertStringContainsString('Age not eligible', $reason);
    }

    public function testUnavailableStateIsDenied(): void
    {
        $reason = $this->denialReason(
            (new ProductTermsBuilder())->withAvailableStates(['NV'])->build(),
            (new CreditProfileBuilder())->withState('CA')->build(),
        );

        self::assertStringContainsString('State not eligible', $reason);
    }

    private function denialReason(ProductTerms $terms, CreditProfile $applicant): string
    {
        try {
            $this->checker->isEligible($terms, $applicant);
        } catch (LoanApplicationDeniedException $exception) {
            return $exception->getReason();
        }

        self::fail('Expected LoanApplicationDeniedException was not thrown.');
    }
}
