<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Identity;

use App\Shared\Domain\Identity\Uuid;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\Identity\UuidInterface;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

final class SymfonyUuidFactory implements UuidFactoryInterface
{
    public function uuid7(): UuidInterface
    {
        return new Uuid(SymfonyUuid::v7());
    }

    public function fromString(string $uuid): UuidInterface
    {
        return new Uuid(SymfonyUuid::fromString($uuid));
    }

    public function fromBytes(string $bytes): UuidInterface
    {
        return new Uuid(SymfonyUuid::fromBinary($bytes));
    }
}
