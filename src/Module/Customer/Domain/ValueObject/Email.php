<?php

declare(strict_types=1);

namespace App\Module\Customer\Domain\ValueObject;

use InvalidArgumentException;
use Stringable;

final class Email implements Stringable
{
    public function __construct(
        public readonly string $value,
    ) {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException(sprintf('Invalid email address "%s".', $value));
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
