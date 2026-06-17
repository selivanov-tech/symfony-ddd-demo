<?php

declare(strict_types=1);

namespace App\Domain\Loan\Service;

interface NewYorkLotteryInterface
{
    public function rejects(): bool;
}
