<?php

namespace App\Domain\Loan\Service;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Loan\Exception\LoanApplicationDeniedException;
use App\Domain\Product\Entity\Product;

class LoanEligibilityChecker
{
    public function __construct(
        private readonly NewYorkLotteryInterface $newYorkLottery,
    ) {
    }

    public function isEligible(Product $product, Customer $customer): bool
    {
        $minFICO = $product->getMinFICOScore();
        if ($customer->getFicoScore() < $minFICO) {
            throw new LoanApplicationDeniedException(
                sprintf('Credit score too low, it should be at least %d.', $minFICO)
            );
        }

        $minMonthlyIncome = $product->getMinMonthlyIncome();
        if ($customer->getMonthlyIncome() < $minMonthlyIncome) {
            throw new LoanApplicationDeniedException(
                sprintf('Monthly income too low, it should be more than %d.', $minMonthlyIncome)
            );
        }

        $age = $customer->getAge();
        $minAge = $product->getMinAge();
        $maxAge = $product->getMaxAge();
        if ($age < $minAge || $age > $maxAge) {
            throw new LoanApplicationDeniedException(
                sprintf('Age not eligible for a loan, it should be between %d and %d years.', $minAge, $maxAge)
            );
        }

        $state = $customer->getAddress()->getState();

        $availableStates = $product->getAvailableStates();
        if (!in_array($state, $availableStates)) {
            throw new LoanApplicationDeniedException(
                sprintf(
                    'State not eligible for a loan, it should be in: %s.',
                    implode(', ', $availableStates)
                )
            );
        }

        if ($state === 'NY' && $this->newYorkLottery->rejects()) {
            throw new LoanApplicationDeniedException('Random rejection for NY state.', public: false);
        }

        $stateHasRule = $product->getStatesScoreMultipliers()->getRuleForState($state);
        if ($stateHasRule !== null) {
            $stateHasRule->applyRule($product);
        }

        return true;
    }
}
