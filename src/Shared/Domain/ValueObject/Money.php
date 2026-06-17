<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use InvalidArgumentException;

final class Money
{
    public function __construct(
        public readonly int $minorUnits,
        public readonly string $currency = 'USD',
    ) {
        if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid currency code "%s".', $currency));
        }
    }

    public static function fromFloat(float $amount, string $currency = 'USD'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    public function toFloat(): float
    {
        return $this->minorUnits / 100;
    }

    public function equals(self $other): bool
    {
        return $this->minorUnits === $other->minorUnits
            && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return sprintf('%.2f %s', $this->toFloat(), $this->currency);
    }
}
