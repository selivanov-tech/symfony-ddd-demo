<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Module\Loan\Domain\Service\NewYorkLotteryInterface;

final class FixedNewYorkLottery implements NewYorkLotteryInterface
{
    public function __construct(
        private readonly bool $rejects,
    ) {
    }

    public function rejects(): bool
    {
        return $this->rejects;
    }
}
