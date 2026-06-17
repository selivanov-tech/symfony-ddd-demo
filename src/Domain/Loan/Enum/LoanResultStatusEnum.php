<?php

namespace App\Domain\Loan\Enum;

enum LoanResultStatusEnum: string
{
    case DENIED = 'denied';
    case APPROVED = 'approved';
}
