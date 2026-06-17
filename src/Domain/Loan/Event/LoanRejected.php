<?php

declare(strict_types=1);

namespace App\Domain\Loan\Event;

use App\Shared\Domain\Event\AbstractDomainEvent;
use App\Shared\Domain\ValueObject\Money;

final class LoanRejected extends AbstractDomainEvent
{
    public function __construct(
        string $loanId,
        public readonly string $customerId,
        public readonly Money $amount,
        public readonly string $reason,
    ) {
        parent::__construct($loanId);
    }

    public function eventName(): string
    {
        return 'loan.rejected';
    }
}
