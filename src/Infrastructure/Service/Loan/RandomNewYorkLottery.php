<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Loan;

use App\Domain\Loan\Service\NewYorkLotteryInterface;

final class RandomNewYorkLottery implements NewYorkLotteryInterface
{
    public function rejects(): bool
    {
        return random_int(0, 1) === 0;
    }
}
