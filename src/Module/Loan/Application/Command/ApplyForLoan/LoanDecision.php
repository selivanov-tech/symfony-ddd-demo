<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\Command\ApplyForLoan;

final class LoanDecision
{
    public function __construct(
        public readonly string $loanId,
        public readonly bool $approved,
    ) {
    }
}
