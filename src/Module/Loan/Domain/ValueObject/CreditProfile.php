<?php

declare(strict_types=1);

namespace App\Module\Loan\Domain\ValueObject;

final class CreditProfile
{
    public function __construct(
        public readonly int $ficoScore,
        public readonly int $monthlyIncome,
        public readonly int $age,
        public readonly string $state,
    ) {
    }
}
