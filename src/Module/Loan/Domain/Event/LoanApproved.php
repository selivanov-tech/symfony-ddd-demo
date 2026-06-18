<?php

declare(strict_types=1);

namespace App\Module\Loan\Domain\Event;

use App\Shared\Domain\Event\AbstractDomainEvent;
use App\Shared\Domain\ValueObject\Money;

final class LoanApproved extends AbstractDomainEvent
{
    public function __construct(
        string $loanId,
        public readonly string $customerId,
        public readonly Money $amount,
    ) {
        parent::__construct($loanId);
    }

    public function eventName(): string
    {
        return 'loan.approved';
    }
}
