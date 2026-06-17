<?php

declare(strict_types=1);

namespace App\Domain\Customer\ValueObject;

use InvalidArgumentException;
use Stringable;

final class Phone implements Stringable
{
    public function __construct(
        public readonly string $value,
    ) {
        if (preg_match('/^\d{10}$/', $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid phone number "%s"; expected 10 digits.', $value));
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
