<?php

declare(strict_types=1);

namespace App\Application\Loan\Command\ApplyForLoan;

final class LoanDecision
{
    public function __construct(
        public readonly string $loanId,
        public readonly bool $approved,
    ) {
    }
}
