<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\Command\ApplyForLoan;

use OpenApi\Attributes as OA;

final class LoanDecision
{
    public function __construct(
        #[OA\Property(example: '0193b3e9-9c2f-7a18-bd34-5e6f7a8b9c01')]
        public readonly string $loanId,
        #[OA\Property(example: true)]
        public readonly bool $approved,
    ) {
    }
}
