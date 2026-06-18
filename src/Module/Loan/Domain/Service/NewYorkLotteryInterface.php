<?php

declare(strict_types=1);

namespace App\Module\Loan\Domain\Service;

interface NewYorkLotteryInterface
{
    public function rejects(): bool;
}
