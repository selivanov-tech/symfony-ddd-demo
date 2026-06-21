<?php

declare(strict_types=1);

namespace App\Module\Loan\Infrastructure\Service;

use App\Module\Loan\Domain\Service\NewYorkLotteryInterface;

final class RandomNewYorkLottery implements NewYorkLotteryInterface
{
    public function rejects(): bool
    {
        return random_int(0, 1) === 0;
    }
}
