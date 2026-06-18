<?php

declare(strict_types=1);

namespace App\Domain\Customer\ValueObject;

use App\Domain\Customer\Exception\InvalidFICOScoreException;

final class FicoScore
{
    public const MIN = 300;
    public const MAX = 850;

    public function __construct(
        public readonly int $value,
    ) {
        if ($value < self::MIN || $value > self::MAX) {
            throw new InvalidFICOScoreException($value);
        }
    }

    public function isAtLeast(self $other): bool
    {
        return $this->value >= $other->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
