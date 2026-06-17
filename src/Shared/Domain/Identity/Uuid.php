<?php

declare(strict_types=1);

namespace App\Shared\Domain\Identity;

use Symfony\Component\Uid\Uuid as SymfonyUuid;

final class Uuid implements UuidInterface
{
    public function __construct(
        private readonly SymfonyUuid $uuid,
    ) {
    }

    public function toString(): string
    {
        return $this->uuid->toRfc4122();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function getBytes(): string
    {
        return $this->uuid->toBinary();
    }

    public function equals(?object $other): bool
    {
        return $other instanceof self && $this->uuid->equals($other->uuid);
    }
}
