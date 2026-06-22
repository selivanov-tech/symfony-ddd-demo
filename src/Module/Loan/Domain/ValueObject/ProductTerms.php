<?php

declare(strict_types=1);

namespace App\Module\Loan\Domain\ValueObject;

final class ProductTerms
{
    /**
     * @param string[] $availableStates
     */
    public function __construct(
        public readonly int $minFicoScore,
        public readonly int $minMonthlyIncome,
        public readonly int $minAge,
        public readonly int $maxAge,
        public readonly array $availableStates,
    ) {
    }
}
