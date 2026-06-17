<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Loan;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Loan\Exception\LoanApplicationDeniedException;
use App\Domain\Loan\Service\LoanEligibilityChecker;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\ValueObject\StatesScoreMultiplierCollection;
use PHPUnit\Framework\TestCase;

final class LoanEligibilityCheckerTest extends TestCase
{
    private LoanEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new LoanEligibilityChecker();
    }

    public function testEligibleApplicantPasses(): void
    {
        self::assertTrue(
            $this->checker->isEligible($this->product(), $this->customer())
        );
    }

    public function testTooLowFicoIsDenied(): void
    {
        $reason = $this->denialReason(
            $this->product(['minFICOScore' => 800]),
            $this->customer(['ficoScore' => 700]),
        );

        self::assertStringContainsString('Credit score too low', $reason);
    }

    public function testTooLowIncomeIsDenied(): void
    {
        $reason = $this->denialReason(
            $this->product(['minMonthlyIncome' => 10000]),
            $this->customer(['monthlyIncome' => 3000]),
        );

        self::assertStringContainsString('Monthly income too low', $reason);
    }

    public function testAgeOutsideRangeIsDenied(): void
    {
        $reason = $this->denialReason(
            $this->product(['minAge' => 18, 'maxAge' => 25]),
            $this->customer(['birthday' => new \DateTimeImmutable('1980-01-01')]),
        );

        self::assertStringContainsString('Age not eligible', $reason);
    }

    public function testUnavailableStateIsDenied(): void
    {
        $reason = $this->denialReason(
            $this->product(['availableStates' => ['NV']]),
            $this->customer(),
        );

        self::assertStringContainsString('State not eligible', $reason);
    }

    private function denialReason(Product $product, Customer $customer): string
    {
        try {
            $this->checker->isEligible($product, $customer);
        } catch (LoanApplicationDeniedException $exception) {
            return $exception->getReason();
        }

        self::fail('Expected LoanApplicationDeniedException was not thrown.');
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function customer(array $overrides = []): Customer
    {
        return (new Customer())
            ->setFicoScore($overrides['ficoScore'] ?? 720)
            ->setMonthlyIncome($overrides['monthlyIncome'] ?? 6000)
            ->setBirthday($overrides['birthday'] ?? new \DateTimeImmutable('1990-01-01'))
            ->setAddress($overrides['address'] ?? [
                'street' => '1 Market St',
                'city' => 'San Francisco',
                'state' => 'CA',
                'zip' => '94105',
            ]);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function product(array $overrides = []): Product
    {
        return (new Product())
            ->setMinFICOScore($overrides['minFICOScore'] ?? 600)
            ->setMinMonthlyIncome($overrides['minMonthlyIncome'] ?? 2000)
            ->setMinAge($overrides['minAge'] ?? 18)
            ->setMaxAge($overrides['maxAge'] ?? 70)
            ->setAvailableStates($overrides['availableStates'] ?? ['CA', 'NV'])
            ->setInterestRate($overrides['interestRate'] ?? 9.5)
            ->setStatesScoreMultipliers(new StatesScoreMultiplierCollection([]));
    }
}
