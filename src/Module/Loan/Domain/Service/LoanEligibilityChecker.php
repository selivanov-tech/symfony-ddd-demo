<?php

namespace App\Module\Loan\Domain\Service;

use App\Module\Loan\Domain\Exception\LoanApplicationDeniedException;
use App\Module\Loan\Domain\ValueObject\CreditProfile;
use App\Module\Loan\Domain\ValueObject\ProductTerms;

class LoanEligibilityChecker
{
    public function __construct(
        private readonly NewYorkLotteryInterface $newYorkLottery,
    ) {
    }

    public function isEligible(ProductTerms $terms, CreditProfile $applicant): bool
    {
        if ($applicant->ficoScore < $terms->minFicoScore) {
            throw new LoanApplicationDeniedException(
                sprintf('Credit score too low, it should be at least %d.', $terms->minFicoScore)
            );
        }

        if ($applicant->monthlyIncome < $terms->minMonthlyIncome) {
            throw new LoanApplicationDeniedException(
                sprintf('Monthly income too low, it should be more than %d.', $terms->minMonthlyIncome)
            );
        }

        if ($applicant->age < $terms->minAge || $applicant->age > $terms->maxAge) {
            throw new LoanApplicationDeniedException(
                sprintf('Age not eligible for a loan, it should be between %d and %d years.', $terms->minAge, $terms->maxAge)
            );
        }

        if (!in_array($applicant->state, $terms->availableStates)) {
            throw new LoanApplicationDeniedException(
                sprintf(
                    'State not eligible for a loan, it should be in: %s.',
                    implode(', ', $terms->availableStates)
                )
            );
        }

        if ($applicant->state === 'NY' && $this->newYorkLottery->rejects()) {
            throw new LoanApplicationDeniedException('Random rejection for NY state.', public: false);
        }

        return true;
    }
}
