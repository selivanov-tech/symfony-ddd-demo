<?php

declare(strict_types=1);

namespace App\Application\Loan\Command\ApplyForLoan;

use App\Shared\Application\Bus\Command\CommandInterface;

final class ApplyForLoanCommand implements CommandInterface
{
    public function __construct(
        public readonly string $productId,
        public readonly string $customerId,
    ) {
    }
}
