<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\Query\CheckEligibility;

use App\Shared\Application\Bus\Query\QueryInterface;

final class CheckLoanEligibilityQuery implements QueryInterface
{
    public function __construct(
        public readonly string $productId,
        public readonly string $customerId,
    ) {
    }
}
