<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Loan;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Loan\Exception\LoanApplicationDeniedException;
use App\Domain\Loan\Service\LoanEligibilityChecker;
use App\Domain\Product\Entity\Product;
use App\Tests\Builder\CustomerBuilder;
use App\Tests\Builder\ProductBuilder;
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
            $this->checker->isEligible((new ProductBuilder())->build(), (new CustomerBuilder())->build())
        );
    }

    public function testNewYorkApplicantIsDeniedWhenTheLotterySelectsThem(): void
    {
        $checker = new LoanEligibilityChecker(new FixedNewYorkLottery(rejects: true));

        try {
            $checker->isEligible(
                (new ProductBuilder())->withAvailableStates(['CA', 'NY'])->build(),
                (new CustomerBuilder())->withAddress($this->address('NY'))->build(),
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
                (new ProductBuilder())->withAvailableStates(['CA', 'NY'])->build(),
                (new CustomerBuilder())->withAddress($this->address('NY'))->build(),
            )
        );
    }

    public function testTooLowFicoIsDenied(): void
    {
        $reason = $this->denialReason(
            (new ProductBuilder())->withMinFICOScore(800)->build(),
            (new CustomerBuilder())->withFicoScore(700)->build(),
        );

        self::assertStringContainsString('Credit score too low', $reason);
    }

    public function testTooLowIncomeIsDenied(): void
    {
        $reason = $this->denialReason(
            (new ProductBuilder())->withMinMonthlyIncome(10000)->build(),
            (new CustomerBuilder())->withMonthlyIncome(3000)->build(),
        );

        self::assertStringContainsString('Monthly income too low', $reason);
    }

    public function testAgeOutsideRangeIsDenied(): void
    {
        $reason = $this->denialReason(
            (new ProductBuilder())->withMinAge(18)->withMaxAge(25)->build(),
            (new CustomerBuilder())->withBirthday(new \DateTimeImmutable('1980-01-01'))->build(),
        );

        self::assertStringContainsString('Age not eligible', $reason);
    }

    public function testUnavailableStateIsDenied(): void
    {
        $reason = $this->denialReason(
            (new ProductBuilder())->withAvailableStates(['NV'])->build(),
            (new CustomerBuilder())->build(),
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
     * @return array{street: string, city: string, state: string, zip: string}
     */
    private function address(string $state): array
    {
        return ['street' => '1 Market St', 'city' => 'San Francisco', 'state' => $state, 'zip' => '94105'];
    }
}
