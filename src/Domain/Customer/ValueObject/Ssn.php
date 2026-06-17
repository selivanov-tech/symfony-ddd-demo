<?php

declare(strict_types=1);

namespace App\Domain\Customer\ValueObject;

use InvalidArgumentException;
use Stringable;

final class Ssn implements Stringable
{
    public function __construct(
        public readonly string $value,
    ) {
        if (preg_match('/^\d{3}-\d{2}-\d{4}$/', $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid SSN "%s"; expected the format XXX-XX-XXXX.', $value));
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
