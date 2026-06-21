<?php

namespace App\Module\Loan\Domain\Enum;

enum LoanResultStatusEnum: string
{
    case DENIED = 'denied';
    case APPROVED = 'approved';
}
